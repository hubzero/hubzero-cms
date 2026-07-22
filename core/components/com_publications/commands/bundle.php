<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Components\Publications\Models\BundleBuilder;
use Components\Publications\Models\BundleQueue;

require_once dirname(__DIR__) . DS . 'models' . DS . 'bundlebuilder.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'bundlequeue.php';

/**
 * Build publication download bundles (core; replaces the PURR-only
 * app/bin/rebuild-publication-bundle).
 **/
class Bundle extends Base implements CommandInterface
{
	/**
	 * Default — show help.
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Build a version's bundle.
	 *
	 * With --queue, this is the worker the async dispatcher spawns: it claims
	 * the queued row (skipping if already building/claimed) and records the
	 * ready/failed result. Without --queue it is a direct manual build (the
	 * replacement for app/bin/rebuild-publication-bundle); pass --force to
	 * rebuild over an existing bundle.
	 *
	 * @museDescription  Build a publication version's download bundle
	 * @return  void
	 **/
	public function build()
	{
		$versionId = (int) $this->arguments->getOpt('version');

		if (!$versionId)
		{
			$this->output->error('A version id is required: --version=<publication_version_id>');
			return;
		}

		$queued = (bool) $this->arguments->getOpt('queue');
		$force  = (bool) $this->arguments->getOpt('force');
		$level  = $this->arguments->getOpt('level');

		$entry = null;

		if ($queued)
		{
			$entry = BundleQueue::forVersion($versionId);

			if (!$entry || !$entry->get('id') || !$entry->claim())
			{
				// Not queued, or another worker won the claim. Nothing to do.
				$this->output->addLine('Nothing to build for version ' . $versionId . ' (not queued or already building).');
				return;
			}

			$force = true; // a queued (re)build always produces a fresh bundle
		}

		$builder = new BundleBuilder();
		$result  = $builder->build($versionId, array(
			'force' => $force,
			'level' => ($level === false ? null : (int) $level),
			'log'   => function ($m) { $this->output->addLine($m); },
		));

		if ($queued && $entry)
		{
			if ($result['ok'])
			{
				$entry->markReady($result['file'], $result['size'], $result['source_hash']);
			}
			else
			{
				$entry->markFailed($result['error']);
			}
		}

		if ($result['ok'])
		{
			$this->output->addLine(sprintf('Bundle ready: %s (%s bytes)', $result['file'], number_format($result['size'])), 'success');
		}
		else
		{
			$this->output->error($result['error']);
		}
	}

	/**
	 * Queue a version for (re)building by the async worker. Use this to repair
	 * a bundle discovered to be wrong, or to pre-warm a build.
	 *
	 * @museDescription  Queue a publication version's bundle for building
	 * @return  void
	 **/
	public function enqueue()
	{
		$versionId = (int) $this->arguments->getOpt('version');

		if (!$versionId)
		{
			$this->output->error('A version id is required: --version=<publication_version_id>');
			return;
		}

		if (BundleQueue::enqueueVersion($versionId))
		{
			$this->output->addLine('Queued version ' . $versionId . ' for bundle build.', 'success');
		}
		else
		{
			$this->output->error('Could not queue version ' . $versionId . '.');
		}
	}

	/**
	 * List the bundle queue.
	 *
	 * @museDescription  List the bundle build queue
	 * @return  void
	 **/
	public function list()
	{
		$rows = array(array('Version', 'Status', 'Attempts', 'Size', 'Built', 'Last error'));

		foreach (BundleQueue::all()->order('queued_at', 'desc')->rows() as $row)
		{
			$rows[] = array(
				$row->get('publication_version_id'),
				$row->get('status'),
				$row->get('attempts') . '/' . $row->get('max_attempts'),
				$row->get('bundle_size') ? number_format($row->get('bundle_size')) : '-',
				$row->get('built_at') ?: '-',
				\Hubzero\Utility\Str::truncate((string) $row->get('last_error'), 40),
			);
		}

		$this->output->addTable($rows, true);
	}

	/**
	 * Show one version's bundle status.
	 *
	 * @museDescription  Show a version's bundle status
	 * @return  void
	 **/
	public function status()
	{
		$versionId = (int) $this->arguments->getOpt('version');

		if (!$versionId)
		{
			$this->output->error('A version id is required: --version=<publication_version_id>');
			return;
		}

		$row = BundleQueue::forVersion($versionId);

		if (!$row || !$row->get('id'))
		{
			$this->output->addLine('No bundle queue entry for version ' . $versionId . '.');
			return;
		}

		foreach (array('status', 'attempts', 'max_attempts', 'source_hash', 'bundle_file', 'bundle_size', 'queued_at', 'last_attempt_at', 'built_at', 'last_error') as $k)
		{
			$this->output->addLine(sprintf('%-16s %s', $k . ':', $row->get($k)));
		}
	}

	/**
	 * Audit bundle health for one version (--version), one publication
	 * (--pub, all its versions), or every published version (no argument).
	 * Read-only. Shows only versions with problems unless -a/--all is given.
	 *
	 * Flags: a missing bundle; incomplete:X/N when only X of N source files are
	 * present (the ticket #2782 class of truncated build); content_mismatch:0/N
	 * when none of the current source files are present though the bundle holds
	 * other data (a post-publish swap); an outer that doesn't carry the data the
	 * inner holds; a dead-lettered build; and staleness vs the recorded source
	 * signature. Presence is verified file-by-file (name + uncompressed size,
	 * plus CRC for the members of an expanded source archive), so good
	 * compression, an expanded .zip, or a stored archive is not mistaken for loss.
	 *
	 * @museDescription  Audit publication bundle health
	 * @return  void
	 **/
	public function audit()
	{
		$db      = \App::get('db');
		$version = (int) $this->arguments->getOpt('version');
		$pub     = (int) $this->arguments->getOpt('pub');
		$showAll = $this->arguments->getOpt('a') || $this->arguments->getOpt('all');
		$deep    = (bool) $this->arguments->getOpt('deep');

		// A --deep sweep reads/CRCs every file and runs for a long time; the
		// stock 128M CLI limit can be exhausted on a high-entry-count bundle and
		// kill the whole run. Raise it for --deep — but never below an operator's
		// own higher (or unlimited) -d memory_limit.
		if ($deep)
		{
			$cur   = trim((string) ini_get('memory_limit'));
			$unit  = strtolower(substr($cur, -1));
			$bytes = ($cur === '' || (int) $cur < 0)
				? PHP_INT_MAX
				: (int) $cur * ($unit === 'g' ? 1073741824 : ($unit === 'm' ? 1048576 : ($unit === 'k' ? 1024 : 1)));
			if ($bytes < 2147483648)
			{
				ini_set('memory_limit', '2048M');
			}

			// --deep can spend many minutes reading one large publication with
			// no DB activity; MySQL's idle wait_timeout (e.g. 300s here) would
			// then drop the connection and the next query fatals with "server
			// has gone away". Raise this session's idle timeout so it survives.
			try
			{
				$db = \App::get('db');
				$db->setQuery('SET SESSION wait_timeout = 86400, interactive_timeout = 86400');
				$db->execute();
			}
			catch (\Exception $e)
			{
				// non-fatal; the per-version guard below also protects the run
			}
		}

		if ($version)
		{
			$ids = array($version);
		}
		else if ($pub)
		{
			$db->setQuery("SELECT `id` FROM `#__publication_versions` WHERE `publication_id` = " . $pub . " ORDER BY `id`");
			$ids = $db->loadColumn();
		}
		else
		{
			$db->setQuery("SELECT `id` FROM `#__publication_versions` WHERE `state` = 1 ORDER BY `id`");
			$ids = $db->loadColumn();
		}

		if (empty($ids))
		{
			$this->output->addLine('No versions to audit.');
			return;
		}

		$builder = new BundleBuilder();
		$rows    = array(array('Pub/Ver', 'DOI', 'Primary', 'Inner', 'Outer', 'Queue', 'Issues'));
		$audited = 0;
		$okc     = 0;
		$probc   = 0;

		foreach ($ids as $vid)
		{
			$a = $builder->audit((int) $vid, $deep);
			$audited++;

			try
			{
				$bq = BundleQueue::forVersion((int) $vid);
			}
			catch (\Exception $e)
			{
				$bq = null; // a DB hiccup on the queue lookup must not abort the sweep
			}
			$hasRow  = ($bq && $bq->get('id'));
			$qstatus = $hasRow ? $bq->get('status') : '-';
			$issues  = $a['issues'];

			if ($hasRow)
			{
				if ($qstatus === BundleQueue::STATUS_FAILED)
				{
					$issues[] = 'build_failed';
				}

				$stored = $bq->get('source_hash');
				if ($stored !== null && $stored !== '' && $a['source_hash'] !== ''
					&& (string) $stored !== (string) $a['source_hash'])
				{
					$issues[] = 'stale';
				}
			}

			$ok = empty($issues);
			$ok ? $okc++ : $probc++;

			// Progress + immediate issue reporting to stderr, so a long sweep
			// (e.g. --deep across the whole hub) shows it is working and
			// surfaces problems as they are found, not only in the final table.
			if (!$ok)
			{
				fwrite(STDERR, sprintf("  [%d/%d] %d/%d  %s\n", $audited, count($ids), $a['publication_id'], $vid, implode(',', $issues)));
			}
			if ($audited % 100 === 0)
			{
				fwrite(STDERR, sprintf("  ...%d/%d audited, %d with issues\n", $audited, count($ids), $probc));
			}

			if (!$showAll && $ok)
			{
				continue;
			}

			$outer = $a['outer_exists']
				? (round($a['outer_size'] / 1048576) . 'MB' . ($a['outer_has_bundle'] === false ? ' !nodata' : ''))
				: 'MISSING';

			$rows[] = array(
				$a['publication_id'] . '/' . $vid,
				\Hubzero\Utility\Str::truncate((string) $a['doi'], 22),
				$a['primary_count'],
				$a['inner_exists'] ? ($a['inner_entries'] === null ? '?' : $a['inner_entries']) : '-',
				$outer,
				$qstatus,
				$ok ? 'ok' : implode(', ', $issues),
			);
		}

		if (count($rows) > 1)
		{
			$this->output->addTable($rows, true);
		}

		$this->output->addLine(
			sprintf('Audited %d version(s): %d ok, %d with issues.', $audited, $okc, $probc),
			($probc ? 'warning' : 'success')
		);
	}

	/**
	 * Help.
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->getHelpOutput()
			->addOverview('Build publication download bundles.')
			->addTasks($this)
			->addArgument('--version=<id>', 'Publication version id.', 'Example: --version=4333')
			->addArgument('--pub=<id>', 'Publication id (audit: all its versions).', 'Example: --pub=5081')
			->addArgument('--force', 'Rebuild even if a bundle already exists.', '')
			->addArgument('--queue', 'Worker mode: claim the queued row and record the result.', '')
			->addArgument('--level=<0-9>', 'zip compression level for the inner bundle (default 6).', '')
			->addArgument('-a, --all', 'audit: show every version, not just those with problems.', '')
			->addArgument('--deep', 'audit: also CRC-verify each file against the bundle (reads source bytes; slow).', '')
			->render();
	}
}
