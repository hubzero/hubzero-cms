<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Models;

use Hubzero\Database\Relational;
use Hubzero\Debug\Profiler;
use Hubzero\Config\Registry;
use Lang;
use Date;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'Cron' . DS . 'CronExpression.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'process.php';

/**
 * Cron model for a job
 */
class Job extends Relational
{
	/**
	 * Cron expression
	 *
	 * @var  object
	 */
	protected $expression = null;

	/**
	 * Profiler
	 *
	 * @var  object
	 */
	protected $profiler = null;

	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'cron';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'ordering';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 */
	protected $rules = array(
		'title'      => 'notempty',
		'recurrence' => 'notempty',
		'event'      => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'event',
		'publish_up',
		'publish_down'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Split event into plugin name and event
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 **/
	public function automaticEvent($data)
	{
		if (strstr($data['event'], '::'))
		{
			$parts = explode('::', $data['event']);
			$this->set('plugin', trim($parts[0]));
			return trim($parts[1]);
		}
		return $data['event'];
	}

	/**
	 * Set publish up value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 **/
	public function automaticPublishUp($data)
	{
		if (!isset($data['publish_up']))
		{
			$data['publish_up'] = null;
		}

		if (!$data['publish_up'] || $data['publish_up'] == '0000-00-00 00:00:00')
		{
			$data['publish_up'] = ($data['id'] ? $this->get('created') : \Date::toSql());
		}

		return $data['publish_up'];
	}
	/**
	 * Set publish down value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishDown($data)
	{
		if (!$data['publish_down'] || $data['publish_down'] == '0000-00-00 00:00:00')
		{
			$data['publish_down'] = null;
		}
		return $data['publish_down'];
	}

	/**
	 * Runs extra setup code when creating a new model
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('recurrence', function($data)
		{
			$data['recurrence'] = preg_replace('/[\s]{2,}/', ' ', $data['recurrence']);

			if (preg_match('/[^-,*\/ \\d]/', $data['recurrence']) !== 0)
			{
				return Lang::txt('Cron String contains invalid character.');
			}

			$bits = @explode(' ', $data['recurrence']);
			if (count($bits) != 5)
			{
				return Lang::txt('Cron string is invalid. Too many or too little sections.');
			}

			return false;
		});

		$this->set('params', new Registry($this->get('params')));

		$this->profiler = new Profiler('cron_job_' . $this->get('id'));
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 */
	public function save()
	{
		$params = $this->get('params');
		if (is_object($params))
		{
			$this->set('params', $params->toString());
		}

		$result = parent::save();

		$this->set('params', $params);

		return $result;
	}

	/**
	 * Defines a relationship to creator
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a relationship to modifier
	 *
	 * @return  object
	 */
	public function modifier()
	{
		return $this->belongsToOne('Hubzero\User\User', 'modified_by');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		switch (strtolower($as))
		{
			case 'date':
				return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			break;

			case 'time':
				return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
			break;

			case 'relative':
				return Date::of($this->get('created'))->relative();
			break;

			default:
				if ($as)
				{
					return Date::of($this->get('created'))->toLocal($as);
				}
				return $this->get('created');
			break;
		}
	}

	/**
	 * Get a cron expression
	 *
	 * @return  object
	 */
	public function expression()
	{
		if (!($this->expression instanceof \Cron\CronExpression))
		{
			$this->expression = \Cron\CronExpression::factory($this->get('recurrence'));
		}
		return $this->expression;
	}

	/**
	 * Is the entry published?
	 *
	 * @return  boolean
	 */
	public function isPublished()
	{
		return ($this->get('state') == self::STATE_PUBLISHED);
	}

	/**
	 * Check if the job is available
	 *
	 * @return  boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if (!$this->get('id') || !$this->isPublished())
		{
			return false;
		}

		// Make sure the item is published and within the available time range
		if ($this->started() && !$this->ended())
		{
			return true;
		}

		return false;
	}

	/**
	 * Has the job started?
	 *
	 * @return  boolean
	 */
	public function started()
	{
		if (!$this->get('id') || !$this->isPublished())
		{
			return false;
		}

		$now = Date::of('now')->toSql();

		if ($this->get('publish_up')
		 && $this->get('publish_up') != '0000-00-00 00:00:00'
		 && $this->get('publish_up') > $now)
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the job ended?
	 *
	 * @return  boolean
	 */
	public function ended()
	{
		if (!$this->get('id') || !$this->isPublished())
		{
			return true;
		}

		$now = Date::of('now')->toSql();

		if ($this->get('publish_down')
		 && $this->get('publish_down') != '0000-00-00 00:00:00'
		 && $this->get('publish_down') <= $now)
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the last run timestamp
	 *
	 * @return  void
	 */
	public function lastRun($format = 'Y-m-d H:i:s')
	{
		return $this->expression()->getPreviousRunDate()->format($format);
	}

	/**
	 * Get the next run timestamp
	 *
	 * @return  void
	 */
	public function nextRun($format = 'Y-m-d H:i:s')
	{
		return $this->expression()->getNextRunDate()->format($format);
	}

	/**
	 * Mark a time
	 *
	 * @param   string   $label
	 * @return  boolean
	 */
	public function mark($label)
	{
		return $this->profiler->mark($label);
	}

	/**
	 * Get all profiler marks.
	 *
	 * Returns an array of all marks created since the Profiler object
	 * was instantiated.
	 *
	 * @return  array  Array of profiler marks
	 */
	public function profile()
	{
		return $this->profiler->marks();
	}

	/**
	 * Return data about this job, icluding profile info as an array
	 *
	 * @param   boolean  $vebose
	 * @return  array
	 */
	public function toArray($verbose = false)
	{
		$buffer = $this->profile();

		$start = $buffer[0];
		$end   = end($buffer);

		return array(
			'id'         => $this->get('id'),
			'title'      => $this->get('title'),
			'plugin'     => $this->get('plugin'),
			'event'      => $this->get('event'),
			'last_run'   => $this->get('last_run'),
			'next_run'   => $this->get('next_run'),
			'active'     => $this->get('active'),
			'start_time' => round($start->started(), 3),
			'start_mem'  => round($start->memory(), 3),
			'end_time'   => round($end->ended(), 3),
			'end_mem'    => round($end->memory(), 3),
			'delta_time' => round($end->ended() - $start->started(), 3),
			'delta_mem'  => round($end->memory() - $start->memory(), 3)
		);
	}

	/**
	 * Get params as a Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		return new Registry($this->get('params'));
	}

	/**
	 * Read a process' start time (in jiffies / clock ticks since boot).
	 *
	 * Thin wrapper over the dependency-free Helpers\Process so the parsing and
	 * pid-identity logic can be unit-tested in isolation.
	 *
	 * @param   integer  $pid
	 * @return  string|false  start time in jiffies, or false if unreadable
	 */
	public static function procStartTime($pid)
	{
		return \Components\Cron\Helpers\Process::startTime($pid);
	}

	/**
	 * Is the process that owns this job's active run still alive?
	 *
	 * True only when a process with the recorded pid exists AND has the
	 * recorded start time (guarding against pid reuse). Only meaningful on
	 * the host that started the run, so the caller must compare hosts.
	 *
	 * @return  boolean
	 */
	public function ownerIsAlive()
	{
		return \Components\Cron\Helpers\Process::isAlive($this->get('pid'), $this->get('pid_started'));
	}

	/**
	 * Whether this job is marked active but the process that owned the run is
	 * gone (died, was killed, or timed out). Such a job would otherwise stay
	 * active forever and never run again.
	 *
	 * Process identity is only checkable on the host that started the run, so
	 * we only judge staleness when the recorded host matches this host. A run
	 * owned by another host is left alone (conservative — never reclaim a job
	 * we cannot verify is dead).
	 *
	 * @return  boolean
	 */
	public function isStale()
	{
		return \Components\Cron\Helpers\Process::isStale(
			$this->get('active'),
			$this->get('pid'),
			$this->get('pid_started'),
			$this->get('pid_host'),
			$this->get('active_since'),
			self::STALE_ACTIVE_SECONDS
		);
	}

	/**
	 * Upper bound (seconds) a job may stay active when owned by a host whose
	 * process we cannot verify locally, before it is assumed dead and
	 * reclaimable. Guards against a permanent wedge if the recorded host ever
	 * stops matching this host (rename, container, multi-node). Same-host runs
	 * are judged by live process check, not this timer.
	 *
	 * @var  integer
	 */
	const STALE_ACTIVE_SECONDS = 21600; // 6 hours

	/**
	 * Hostname of the current machine, used to scope pid checks.
	 *
	 * @return  string
	 */
	public static function thisHost()
	{
		return \Components\Cron\Helpers\Process::host();
	}

	/**
	 * Atomically take ownership of this job for the current process.
	 *
	 * Succeeds in exactly two cases, decided in a single conditional UPDATE so
	 * overlapping runners (e.g. the web tick and a `muse cron:jobs run`, which
	 * share no lock) can never both win:
	 *
	 *   - the job is free (active = 0); or
	 *   - the job is active but owned by the exact (pid, start time, host) we
	 *     observed AND that process is no longer alive — i.e. we are reclaiming
	 *     a run whose process died/was killed/timed out.
	 *
	 * The reclaim arm keys on the observed ownership, so if another runner
	 * reclaimed the same dead job a moment earlier, its new pid/start time no
	 * longer match our WHERE and we lose the race cleanly (no double run). A
	 * job whose owner is still alive (or lives on another host we can't verify)
	 * is never claimed.
	 *
	 * @return  boolean  true if this process now owns the run
	 */
	public function claim()
	{
		$id = (int) $this->get('id');

		if (!$id)
		{
			return false;
		}

		// Active with a live owner (or an owner on another host we cannot
		// verify) — genuinely running elsewhere, so it is not claimable.
		if ($this->get('active') && !$this->isStale())
		{
			return false;
		}

		$pid     = getmypid();
		$started = self::procStartTime($pid);
		$host    = self::thisHost();
		$nowSql  = Date::toSql();

		$db = \App::get('db');

		$startedSql = ($started === false) ? 'NULL' : $db->quote($started);

		// Observed (dead) ownership for the reclaim arm.
		$obsPid     = ($this->get('pid') === null)         ? 'NULL' : $db->quote($this->get('pid'));
		$obsStarted = ($this->get('pid_started') === null) ? 'NULL' : $db->quote($this->get('pid_started'));
		$obsHost    = ($this->get('pid_host') === null)    ? 'NULL' : $db->quote($this->get('pid_host'));

		$query = "UPDATE `#__cron_jobs`
			SET `active` = 1, `pid` = " . $db->quote($pid) . ",
				`pid_started` = " . $startedSql . ",
				`pid_host` = " . $db->quote($host) . ",
				`active_since` = " . $db->quote($nowSql) . "
			WHERE `id` = " . $id . "
			  AND (`active` = 0
			       OR (`active` = 1
			           AND `pid` <=> " . $obsPid . "
			           AND `pid_started` <=> " . $obsStarted . "
			           AND `pid_host` <=> " . $obsHost . "))";

		$db->setQuery($query);
		$db->query();

		if ($db->getAffectedRows() < 1)
		{
			return false;
		}

		$this->set('active', 1);
		$this->set('pid', $pid);
		$this->set('pid_started', $started);
		$this->set('pid_host', $host);
		$this->set('active_since', $nowSql);

		return true;
	}

	/**
	 * Re-confirm this job is still due, reading the current next_run from the
	 * database rather than the (possibly stale) value loaded when the run list
	 * was built. Used after claim() to avoid re-running a job that a concurrent
	 * runner finished and rescheduled while our run list was in hand.
	 *
	 * @return  boolean
	 */
	public function stillDue()
	{
		$id = (int) $this->get('id');

		if (!$id)
		{
			return false;
		}

		$row = self::all()->whereEquals('id', $id)->row();

		if (!$row || !$row->get('id'))
		{
			return false;
		}

		$next = $row->get('next_run');

		// Mirror the runner's due query (next_run <= now); a NULL next_run is
		// not selected by that query, so treat it the same here.
		return ($next !== null && $next <= Date::of('now')->toSql());
	}

	/**
	 * Release this job from the active state and clear its process ownership.
	 *
	 * Done as a targeted raw UPDATE rather than save() on purpose: releasing a
	 * lock must never be gated by the model's validation rules (a row with a
	 * malformed recurrence/empty field would otherwise fail to save, leaving
	 * the job active and re-running every tick). It also skips the params
	 * re-serialization save() does.
	 *
	 * When called after a run, pass the new last_run/next_run to persist them
	 * in the same statement. When called to GIVE BACK a claim (e.g. the job
	 * was no longer due), pass nothing: the schedule is left untouched so a
	 * concurrent runner's freshly-advanced next_run is not clobbered.
	 *
	 * @param   mixed  $lastRun  new last_run to persist, or null to leave as-is
	 * @param   mixed  $nextRun  new next_run to persist, or null to leave as-is
	 * @return  boolean
	 */
	public function release($lastRun = null, $nextRun = null)
	{
		$id = (int) $this->get('id');

		$db   = \App::get('db');
		$sets = "`active` = 0, `pid` = NULL, `pid_started` = NULL, `pid_host` = NULL, `active_since` = NULL";

		if ($lastRun !== null)
		{
			$sets .= ", `last_run` = " . $db->quote($lastRun);
		}

		if ($nextRun !== null)
		{
			$sets .= ", `next_run` = " . $db->quote($nextRun);
		}

		if ($id)
		{
			$db->setQuery("UPDATE `#__cron_jobs` SET " . $sets . " WHERE `id` = " . $id);
			$db->query();
		}

		// Keep the in-memory model in step with the row.
		$this->set('active', 0);
		$this->set('pid', null);
		$this->set('pid_started', null);
		$this->set('pid_host', null);
		$this->set('active_since', null);

		if ($lastRun !== null)
		{
			$this->set('last_run', $lastRun);
		}

		if ($nextRun !== null)
		{
			$this->set('next_run', $nextRun);
		}

		return true;
	}
}
