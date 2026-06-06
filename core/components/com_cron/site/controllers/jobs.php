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
}
