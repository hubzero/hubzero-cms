<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Site\Controllers;

use Components\Cron\Models\Job;
use Hubzero\Component\SiteController;
use Request;
use User;
use Date;
use Event;
use stdClass;

/**
 * Controller class for cron jobs
 */
class Jobs extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('display', 'tick');

		parent::execute();
	}

	/**
	 * Run any scheduled cron tasks
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// If the current user doesn't have access to manage the component,
		// try to see if their IP address is in the whitelist.
		// Otherwise, we stop any further code execution.
		if (!User::authorise('core.manage', $this->_option))
		{
			$ip = Request::ip();

			$ips = explode(',', $this->config->get('whitelist', ''));
			$ips = array_map('trim', $ips);

			if (!in_array($ip, $ips))
			{
				// gethostbynamel() returns false on resolver failure; cast to
				// array so a DNS hiccup can't turn in_array() into a TypeError
				// (which would 500 the tick and stall all cron).
				$ips = (array) gethostbynamel($_SERVER['SERVER_NAME'] ?? '');

				if (!in_array($ip, $ips))
				{
					$ips = (array) gethostbynamel('localhost');

					if (!in_array($ip, $ips))
					{
						header("HTTP/1.1 404 Not Found");
						exit();
					}
				}
			}
		}

		// Forcefully do NOT render the template
		// (extra processing that's not needed)
		Request::setVar('no_html', 1);
		Request::setVar('tmpl', 'component');

		$now = Date::toSql();

		// Get the list of jobs that should be run
		$results = Job::all()
			->whereEquals('state', 1)
			->where('next_run', '<=', Date::toSql())
			->where('publish_up', 'IS', null, 'and', 1)->orWhere('publish_up', '<=', $now, 1)
			->resetDepth()
			->where('publish_down', 'IS', null, 'and', 1)->orWhere('publish_down', '>', $now, 1)
			->rows();

		$output = new stdClass;
		$output->jobs = array();

		// A fatal (OOM, max_execution_time, or a plugin exit()) inside a job's
		// event bypasses the finally below, leaving the row active with THIS
		// php-fpm worker's pid — which stays alive across requests, so the recovery
		// sweep reads it as a live owner and can't reclaim it before the cutoff
		// (the job is effectively wedged until then / a worker recycle). Track the
		// in-flight job and release it on shutdown so a fatal can't wedge it;
		// cleared in the finally on normal completion (then this is a no-op).
		$inFlight = new \stdClass;
		$inFlight->job = null;

		register_shutdown_function(function () use ($inFlight)
		{
			if (!$inFlight->job)
			{
				return;
			}

			$next = null;
			try
			{
				$next = $inFlight->job->nextRun();
			}
			catch (\Throwable $e)
			{
				// leave next_run as-is if the recurrence can't be parsed
			}

			try
			{
				$inFlight->job->release(gmdate('Y-m-d H:i:s'), $next);
			}
			catch (\Throwable $e)
			{
				// best-effort: nothing more can be done at shutdown
			}
		});

		foreach ($results as $job)
		{
			if (!$job->isAvailable())
			{
				continue;
			}

			// PROTOTYPE: jobs flagged "detach" in their params run in a detached
			// CLI worker instead of inline, so a long/multi-hour job doesn't tie
			// up an fpm worker or hold the tick open.
			//
			// CAUTION: a CLI worker has NO web request, so (with live_site empty)
			// it generates broken absolute URLs — only flag URL-independent batch
			// jobs (archival, bundle builds), never jobs that email links.
			if ($this->jobWantsDetach($job))
			{
				// A live detached worker already running it? leave it be.
				if ($job->get('active') && !$job->isStale())
				{
					continue;
				}

				// Free, or the previous worker died (stale): (re)launch. The
				// worker claim()s atomically, so overlapping launches across
				// ticks can never double-run the job.
				$this->spawnDetachedRunner($job->get('id'));
				continue;
			}

			// Atomically take ownership. claim() skips a job whose process is
			// still alive (genuinely running elsewhere) and atomically reclaims
			// one left active by a process that died, was killed, or timed out
			// (pid gone, or reused with a different start time) — otherwise a
			// crashed run would keep the job active forever. If another runner
			// won the race, skip; one active job never blocks the others.
			if (!$job->claim())
			{
				continue;
			}

			// Re-confirm still due after claiming: a concurrent runner (e.g. a
			// manual `muse cron:jobs run`) may have finished this job and
			// advanced next_run since this tick's list was built.
			if (!$job->stillDue())
			{
				$job->release();
				continue;
			}

			// Mark in-flight so the shutdown handler can release this job if a
			// fatal bypasses the finally below.
			$inFlight->job = $job;

			$job->mark('start_run');

			try
			{
				Event::trigger('cron.' . $job->get('event'), array($job));
			}
			catch (\Throwable $e)
			{
				// Don't let one failing job abort the tick and block the jobs
				// after it. The job is released below regardless.
			}
			finally
			{
				$job->mark('end_run');

				$next = null;
				try
				{
					$next = $job->nextRun();
				}
				catch (\Throwable $e2)
				{
					// Leave next_run as-is if the recurrence can't be parsed;
					// the job stays due and is retried rather than wedging.
				}

				// Always clear active + pid ownership and persist run times,
				// whether the event succeeded, threw, or fataled.
				$job->release(Date::toSql(), $next);

				// Completed (or threw catchably) — clear in-flight so the
				// shutdown handler registered above is a no-op for this job.
				$inFlight->job = null;
			}

			$output->jobs[] = $job->toArray();
		}

		// Output any data from the jobs that ran
		// Largely used for debugging/monitoring purposes
		$this->view
			->set('no_html', Request::getInt('no_html', 0))
			->set('output', $output)
			->display();
	}

	/**
	 * Whether a job opts into out-of-process execution via a "detach" param.
	 *
	 * @param   object   $job
	 * @return  boolean
	 */
	private function jobWantsDetach($job)
	{
		$params = $job->get('params');

		if (!is_object($params))
		{
			$params = new \Hubzero\Config\Registry($params);
		}

		return ((int) $params->get('detach', 0) === 1);
	}

	/**
	 * PROTOTYPE: launch a detached CLI worker to run a single job out of the
	 * fpm worker's lifecycle.
	 *
	 * setsid gives the worker its own session (reparented to init) so an fpm
	 * reload/worker recycle can't kill it; stdio is detached to a log and the
	 * command is backgrounded so this tick request returns immediately. The
	 * worker (`muse cron:jobs run --job=N`) claim()s the job atomically, so a
	 * race between this launch and another tick/worker can't double-run it.
	 *
	 * @param   integer  $jobId
	 * @return  boolean
	 */
	private function spawnDetachedRunner($jobId)
	{
		$jobId = (int) $jobId;

		if (!$jobId)
		{
			return false;
		}

		// The CLI php — NOT PHP_BINARY, which under php-fpm is the fpm binary.
		$php  = is_executable('/usr/bin/php') ? '/usr/bin/php' : 'php';
		$muse = PATH_CORE . DS . 'bin' . DS . 'muse';
		$log  = PATH_APP . DS . 'logs' . DS . 'cron-detached.log';

		if (!is_file($muse))
		{
			return false;
		}

		// Disable OPcache for the one-shot worker: spawned from the php-fpm web
		// tick, it lands in fpm's PrivateTmp namespace whose empty /tmp has no
		// place for OPcache's SHM lock file, so it would fatal at startup
		// ("Unable to create lock file"). A CLI job gains nothing from OPcache.
		$cmd = 'setsid ' . escapeshellarg($php)
			. ' -d opcache.enable=0 -d opcache.enable_cli=0 '
			. escapeshellarg($muse)
			. ' cron:jobs run --job=' . $jobId
			. ' < /dev/null >> ' . escapeshellarg($log) . ' 2>&1 &';

		@exec($cmd);

		return true;
	}
}
