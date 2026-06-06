<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Process;

/**
 * Queue/state for asynchronous publication-bundle building — one row per
 * publication version, table #__publication_bundle_queue. (Distinct from the
 * Bundle model, which wraps a built bundle file's contents.)
 *
 * Lifecycle: queued -> building -> ready | failed. State transitions are raw,
 * validation-free UPDATEs (a malformed row must never block a release), and
 * claim() is a single atomic conditional UPDATE so overlapping workers (the
 * cron dispatcher and a manual `muse publications:bundle`, which share no lock)
 * can never both build the same version.
 *
 * A failing build never blocks the queue: attempts + last_attempt_at drive
 * exponential backoff, nextEligible() skips cooling-down rows and picks the
 * oldest other queued one, and at max_attempts the row is dead-lettered to
 * failed and dropped from selection. A build whose worker died or hung is
 * requeued by reclaimStale() via Hubzero\Utility\Process.
 */
class BundleQueue extends Relational
{
	const STATUS_QUEUED   = 'queued';
	const STATUS_BUILDING = 'building';
	const STATUS_READY    = 'ready';
	const STATUS_FAILED   = 'failed';

	/**
	 * Base for exponential retry backoff (seconds): a row that has failed N
	 * times waits 2^N * BACKOFF_BASE before it is eligible again.
	 */
	const BACKOFF_BASE = 300;

	/**
	 * A 'building' row is reclaimed once it has been building longer than this —
	 * the backstop for a worker that can't be verified dead: one on another host
	 * (no /proc to check from here) or a same-host worker that is alive but
	 * hung/wedged. A same-host worker whose process has actually died is caught
	 * immediately by the live-process check; this cutoff only bounds the
	 * unverifiable and stuck-but-alive cases.
	 *
	 * Set well above the largest real build so a long but legitimate build is
	 * never reclaimed mid-run — that would spawn a second worker building the
	 * same bundle (wasteful, though verifyZip + the atomic rename keep the result
	 * correct). The biggest bundles are hundreds of GB (~323 GB seen), whose zip
	 * runs in a few hours even on slow/contended disk, so 12h leaves wide margin.
	 */
	const MAX_BUILD_SECONDS = 43200; // 12 hours

	/**
	 * @var  string
	 */
	protected $table = '#__publication_bundle_queue';

	/**
	 * @var  string
	 */
	public $namespace = 'publication';

	/**
	 * @var  string
	 */
	public $orderBy = 'queued_at';

	/**
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * The queue row for a version (a new/blank model if none).
	 *
	 * @param   integer  $versionId
	 * @return  BundleQueue
	 */
	public static function forVersion($versionId)
	{
		return self::all()
			->whereEquals('publication_version_id', (int) $versionId)
			->row();
	}

	/**
	 * Whether a version can be built off-request by BundleBuilder — i.e. it is a
	 * file bundle, having at least one role-1 file attachment. Versions without
	 * one are not file bundles (a Databases version's primary is a CSV generated
	 * from a stored database; a Series links other publications); BundleBuilder
	 * cannot produce them, so they must stay on the synchronous packager. This is
	 * the single rule that BOTH the enqueue and the serve-time async router gate
	 * on, so a non-buildable version is never queued and never routed to async
	 * (which would otherwise loop forever "preparing").
	 *
	 * @param   integer  $versionId
	 * @return  boolean
	 */
	public static function isAsyncBuildable($versionId)
	{
		$versionId = (int) $versionId;

		if (!$versionId)
		{
			return false;
		}

		$db = \App::get('db');
		$db->setQuery(
			"SELECT COUNT(*) FROM `#__publication_attachments`
			 WHERE `publication_version_id` = " . $versionId . " AND `type` = 'file' AND `role` = 1"
		);

		return ((int) $db->loadResult() >= 1);
	}

	/**
	 * Queue a version for (re)building. New version -> queued. A previously
	 * ready/failed bundle -> re-queued (the repair path). A row already
	 * queued/building is left alone. Race-safe via the unique version key.
	 *
	 * @param   integer  $versionId
	 * @return  boolean
	 */
	public static function enqueueVersion($versionId)
	{
		$versionId = (int) $versionId;

		if (!$versionId)
		{
			return false;
		}

		// Only file-based publications are (re)built off-request; enqueuing a
		// non-buildable one would just have the worker fail "no primary files"
		// and dead-letter it. The serve path applies the SAME test before routing
		// to async (see isAsyncBuildable), so the two never disagree.
		if (!self::isAsyncBuildable($versionId))
		{
			return false;
		}

		$db   = \App::get('db');
		$now  = $db->quote(\Date::toSql());
		$done = "`status` IN ('" . self::STATUS_READY . "','" . self::STATUS_FAILED . "')";

		$query = "INSERT INTO `#__publication_bundle_queue`
				(`publication_version_id`, `status`, `attempts`, `queued_at`)
			VALUES (" . $versionId . ", '" . self::STATUS_QUEUED . "', 0, " . $now . ")
			ON DUPLICATE KEY UPDATE
				`attempts`   = IF(" . $done . ", 0, `attempts`),
				`queued_at`  = IF(" . $done . ", " . $now . ", `queued_at`),
				`last_error` = IF(" . $done . ", NULL, `last_error`),
				`status`     = IF(" . $done . ", '" . self::STATUS_QUEUED . "', `status`)";

		$db->setQuery($query);

		return $db->query() !== false;
	}

	/**
	 * Atomically take this queued row for the current process. One winner.
	 *
	 * @return  boolean  true if this process now owns the build
	 */
	public function claim()
	{
		$id = (int) $this->get('id');

		if (!$id)
		{
			return false;
		}

		$pid     = getmypid();
		$started = Process::startTime($pid);
		$host    = Process::host();

		$db         = \App::get('db');
		$startedSql = ($started === false) ? 'NULL' : $db->quote($started);

		$query = "UPDATE `#__publication_bundle_queue`
			SET `status` = '" . self::STATUS_BUILDING . "',
				`attempts` = `attempts` + 1,
				`worker_pid` = " . $db->quote($pid) . ",
				`worker_started` = " . $startedSql . ",
				`worker_host` = " . $db->quote($host) . ",
				`last_attempt_at` = " . $db->quote(\Date::toSql()) . "
			WHERE `id` = " . $id . " AND `status` = '" . self::STATUS_QUEUED . "'";

		$db->setQuery($query);
		$db->query();

		if ($db->getAffectedRows() < 1)
		{
			return false;
		}

		$this->set('status', self::STATUS_BUILDING);
		$this->set('attempts', (int) $this->get('attempts') + 1);
		$this->set('worker_pid', $pid);
		$this->set('worker_started', $started);
		$this->set('worker_host', $host);

		return true;
	}

	/**
	 * Mark a finished build ready (records the file/size/source signature).
	 *
	 * @param   string   $file
	 * @param   integer  $size
	 * @param   string   $sourceHash
	 * @return  boolean
	 */
	public function markReady($file, $size, $sourceHash)
	{
		$id = (int) $this->get('id');

		if (!$id)
		{
			return false;
		}

		$db = \App::get('db');

		$query = "UPDATE `#__publication_bundle_queue`
			SET `status` = '" . self::STATUS_READY . "',
				`bundle_file` = " . $db->quote($file) . ",
				`bundle_size` = " . $db->quote((int) $size) . ",
				`source_hash` = " . $db->quote($sourceHash) . ",
				`built_at` = " . $db->quote(\Date::toSql()) . ",
				`last_error` = NULL,
				`worker_pid` = NULL, `worker_started` = NULL, `worker_host` = NULL
			WHERE `id` = " . $id;

		$db->setQuery($query);

		return $db->query() !== false;
	}

	/**
	 * Mark a failed attempt: back to queued for another try (subject to
	 * backoff), or dead-lettered to failed once attempts reach the cap.
	 *
	 * @param   string  $error
	 * @return  boolean
	 */
	public function markFailed($error)
	{
		$id = (int) $this->get('id');

		if (!$id)
		{
			return false;
		}

		$db   = \App::get('db');
		$dead = ((int) $this->get('attempts') >= (int) $this->get('max_attempts'));
		$next = $dead ? self::STATUS_FAILED : self::STATUS_QUEUED;

		$query = "UPDATE `#__publication_bundle_queue`
			SET `status` = " . $db->quote($next) . ",
				`last_error` = " . $db->quote((string) $error) . ",
				`worker_pid` = NULL, `worker_started` = NULL, `worker_host` = NULL
			WHERE `id` = " . $id;

		$db->setQuery($query);

		return $db->query() !== false;
	}

	/**
	 * The next queued bundle eligible to build: under the attempt cap and past
	 * its backoff window, oldest first. A cooling-down failure is skipped, so
	 * it can never block a different queued build.
	 *
	 * @return  BundleQueue|null
	 */
	public static function nextEligible()
	{
		$db = \App::get('db');

		$query = "SELECT `id` FROM `#__publication_bundle_queue`
			WHERE `status` = '" . self::STATUS_QUEUED . "'
			  AND `attempts` < `max_attempts`
			  AND (`last_attempt_at` IS NULL
			       OR `last_attempt_at` <= (NOW() - INTERVAL (POW(2, `attempts`) * " . (int) self::BACKOFF_BASE . ") SECOND))
			ORDER BY `queued_at` ASC
			LIMIT 1";

		$db->setQuery($query);
		$id = $db->loadResult();

		return $id ? self::oneOrNew($id) : null;
	}

	/**
	 * How many builds are in flight (status=building) — for the dispatcher's
	 * size-aware concurrency cap.
	 *
	 * @return  integer
	 */
	public static function buildingCount()
	{
		$db = \App::get('db');
		$db->setQuery("SELECT COUNT(*) FROM `#__publication_bundle_queue` WHERE `status` = '" . self::STATUS_BUILDING . "'");

		return (int) $db->loadResult();
	}

	/**
	 * Requeue (or dead-letter) any 'building' rows whose worker is gone — dead
	 * pid, reused pid, or (cross-host) building past MAX_BUILD_SECONDS.
	 *
	 * @return  integer  number reclaimed
	 */
	public static function reclaimStale()
	{
		$rows      = self::all()->whereEquals('status', self::STATUS_BUILDING)->rows();
		$reclaimed = 0;

		foreach ($rows as $row)
		{
			$stale = Process::isStale(
				1,
				$row->get('worker_pid'),
				$row->get('worker_started'),
				$row->get('worker_host'),
				$row->get('last_attempt_at'),
				self::MAX_BUILD_SECONDS
			);

			if ($stale)
			{
				$row->markFailed('Build worker gone (died, killed, or timed out); requeued.');
				$reclaimed++;
			}
		}

		return $reclaimed;
	}

	/**
	 * Is a ready bundle still fresh for the given current source signature?
	 * A NULL stored source_hash is grandfathered (pre-async / no-backfill):
	 * trusted as fresh and never auto-rebuilt.
	 *
	 * @param   string  $currentHash
	 * @return  boolean
	 */
	public function isFresh($currentHash)
	{
		$stored = $this->get('source_hash');

		return ($stored === null || $stored === '' || (string) $stored === (string) $currentHash);
	}
}
