<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Utility\Date;
use Hubzero\Utility\Str;
use Components\Cron\Models\Job;
use Event;
use Lang;

require_once dirname(__DIR__) . '/models/job.php';

/**
 * CRON jobs
 **/
class Jobs extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Run jobs
	 *
	 * @museDescription  Run pending jobs
	 * @return  void
	 **/
	public function run()
	{
		$now = with(new Date('now'))->toLocal();

		if ($id = $this->arguments->getOpt('job'))
		{
			// Get the list of jobs that should be run
			$jobs = Job::all()
				->whereEquals('id', $id)
				->rows();
		}
		else
		{
			// Get the list of jobs that should be run
			$jobs = Job::all()
				->whereEquals('state', Job::STATE_PUBLISHED)
				->where('next_run', '<=', $now)
				->where('publish_up', 'IS', null, 'and', 1)
					->orWhere('publish_up', '<=', $now, 1)
					->resetDepth()
				->where('publish_down', 'IS', null, 'and', 1)
				->orWhere('publish_down', '>', $now, 1)
				->rows();
		}

		$processed = array();

		// A specific --job is force-run regardless of its schedule.
		$forced = (bool) $id;

		if ($jobs->count())
		{
			$this->output->addLine(Lang::txt('[%s] Starting scheduled jobs ...', $now), 'info');

			foreach ($jobs as $job)
			{
				if (!$job->isAvailable())
				{
					continue;
				}

				$wasActive = $job->get('active');

				// Atomically take ownership. claim() skips a job whose process
				// is still alive and atomically reclaims one left active by a
				// dead/killed/timed-out process (pid gone or reused). If another
				// runner won the race, skip.
				if (!$job->claim())
				{
					continue;
				}

				// Re-confirm still due after claiming (a concurrent runner may
				// have finished it and advanced next_run since our list was
				// built). Forced --job runs ignore the schedule.
				if (!$forced && !$job->stillDue())
				{
					$job->release();
					continue;
				}

				if ($wasActive)
				{
					$this->output->addLine(Lang::txt('[%s] Reclaimed job "%s" left active by a dead process.', with(new Date('now'))->toLocal(), $job->get('event')), 'warning');
				}

				$now = with(new Date('now'))->toLocal();

				$this->output->addLine(Lang::txt('[%s] Starting event "%s" ...', $now, $job->get('event')), 'info');

				$job->mark('start_run');

				try
				{
					Event::trigger('cron.' . $job->get('event'), array($job));

					$this->output->addLine(Lang::txt('[%s] Finished event "%s".', with(new Date('now'))->toLocal(), $job->get('event')), 'info');
				}
				catch (\Throwable $e)
				{
					$this->output->addLine(Lang::txt('[%s] Event "%s" generated an error: %s', with(new Date('now'))->toLocal(), $job->get('event'), $e->getMessage()), 'error');
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
					$job->release(with(new Date('now'))->toLocal(), $next);
				}

				$processed[] = $job->toArray();
			}

			$this->output->addLine(Lang::txt('[%s] Finished scheduled jobs.', with(new Date('now'))->toLocal()), 'success');
		}
		else
		{
			$this->output->addLine(Lang::txt('No scheduled jobs found.'), 'info');
		}
	}

	/**
	 * Trigger the scheduled-job tick over HTTP — the in-process replacement for
	 * the legacy Python hzcms-tick. It requests the com_cron tick endpoint so
	 * jobs run in the WEB context (correct absolute URLs, etc.), under a
	 * single-flight lock, and logs the outcome.
	 *
	 * The hub URL comes from the CMS `live_site` config; if that is empty it
	 * errors out rather than guess, since a CLI tick has no web request to derive
	 * the host/scheme from (which would yield broken links). Wire it from cron in
	 * place of hzcms-tick, e.g.:
	 *   * * * * * apache  /var/www/purr/core/bin/muse cron:jobs tick >> <log>
	 *
	 * @museDescription  Trigger the scheduled-job tick over HTTP (replaces hzcms-tick)
	 * @return  void
	 **/
	public function tick()
	{
		$host = gethostname() ?: 'localhost';
		$pid  = getmypid();
		$log  = function ($msg) use ($host, $pid)
		{
			$this->output->addLine(gmdate('c') . ' ' . $host . ' cron:tick[' . $pid . '] ' . $msg);
		};

		// The hub URL must come from the CMS config; a CLI tick has no request to
		// derive it from, so refuse rather than guess (broken URLs / wrong host).
		$base = rtrim((string) \Config::get('live_site'), '/');

		if ($base === '')
		{
			$this->output->error('live_site is not set in the CMS configuration; set it (e.g. https://your-hub) so the cron tick knows which URL to request.');
			return;
		}

		$url = $base . '/index.php?option=com_cron&task=tick&no_html=1';

		// Single-flight: don't let ticks pile up. flock auto-releases on process
		// exit, so there is no stale-lockfile problem.
		$tmp = (string) \Config::get('tmp_path');
		$tmp = $tmp !== '' ? rtrim($tmp, '/') : sys_get_temp_dir();
		$lock = @fopen($tmp . '/.cron-tick.lock', 'c');

		if ($lock === false || !flock($lock, LOCK_EX | LOCK_NB))
		{
			$log('skipped — a previous tick is still running');
			return;
		}

		// Hit the endpoint. Follow redirects (the hub forces https), skip TLS
		// verification (self-referential request), bounded timeout, and a
		// recognizable user agent so com_usage keeps filtering the hit out of
		// traffic stats.
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 1800,
			CURLOPT_USERAGENT      => 'hubzero-cms cron tick',
		));

		$start = microtime(true);
		$body  = curl_exec($ch);
		$secs  = microtime(true) - $start;
		$code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$cerr  = curl_error($ch);
		curl_close($ch);

		// Count the jobs the endpoint reported running (legacy hzcms-tick format).
		$count = '-';

		if ($body !== false && ($p = strpos($body, '{')) !== false)
		{
			$data = json_decode(substr($body, $p), true);

			if (isset($data['jobs']) && is_array($data['jobs']))
			{
				$count = count($data['jobs']);
			}
		}

		$log(sprintf('GET %s %.6f %s HTTP %d%s', $url, $secs, $count, $code, ($body === false ? ' ERROR ' . $cerr : '')));

		if ($body === false || $code !== 200)
		{
			$this->output->error('cron tick request failed');
		}
	}

	/**
	 * List jobs
	 *
	 * @museDescription  List available jobs
	 * @return  void
	 **/
	public function list()
	{
		$now = with(new Date('now'))->toLocal();

		if ($this->arguments->getOpt('a'))
		{
			$jobs = Job::all()
				->whereEquals('state', Job::STATE_PUBLISHED)
				->rows();
		}
		else
		{
			$jobs = Job::all()
				->whereEquals('state', Job::STATE_PUBLISHED)
				->where('next_run', '<=', $now)
				->where('publish_up', 'IS', null, 'and', 1)
					->orWhere('publish_up', '<=', $now, 1)
					->resetDepth()
				->where('publish_down', 'IS', null, 'and', 1)
				->orWhere('publish_down', '>', $now, 1)
				->rows();
		}

		$rows = array(
			array(
				Lang::txt('ID'),
				Lang::txt('Title'),
				Lang::txt('Last Run'),
				Lang::txt('Next Run')
			)
		);

		foreach ($jobs as $job)
		{
			$rows[] = array(
				$job->get('id'),
				Str::truncate($job->get('title'), 30),
				$job->get('last_run'),
				$job->get('next_run')
			);
		}

		$this->output->addTable($rows, true);
	}

	/**
	 * Mark a job as inactive
	 *
	 * @museDescription  Mark a job as inactive
	 * @return  void
	 **/
	public function deactivate()
	{
		$id = $this->arguments->getOpt('job');

		if (!$id)
		{
			$this->output->addLine(Lang::txt('A job ID must be provided. Example: `muse cron:job deactivate --job=1`'), 'warning');
			return;
		}

		$job = Job::oneOrNew($id);

		if (!$job || $job->isNew())
		{
			$this->output->addLine(Lang::txt('Specified job %s does not exist.', $id), 'error');
			return;
		}

		$job->set('active', 0);
		$job->set('pid', null);
		$job->set('pid_started', null);
		$job->set('pid_host', null);

		if (!$job->save())
		{
			$this->output->addLine(Lang::txt('Failed to mark job %s as inactive: %s', $id, $job->getError()), 'error');
		}
		else
		{
			$this->output->addLine(Lang::txt('Job %s marked as inactive.', $id), 'success');
		}
	}

	/**
	 * Mark a job as unpublished
	 *
	 * @museDescription  Mark a job as unpublished
	 * @return  void
	 **/
	public function unpublish()
	{
		$id = $this->arguments->getOpt('job');

		if (!$id)
		{
			$this->output->addLine(Lang::txt('A job ID must be provided. Example: `muse cron:job unpublish --job=1`'), 'warning');
			return;
		}

		$job = Job::oneOrNew($id);

		if (!$job || $job->isNew())
		{
			$this->output->addLine(Lang::txt('Specified job %s does not exist.', $id), 'error');
			return;
		}

		$job->set('state', Job::STATE_UNPUBLISHED);

		if (!$job->save())
		{
			$this->output->addLine(Lang::txt('Failed to unpublish job %s: %s', $id, $job->getError()), 'error');
		}
		else
		{
			$this->output->addLine(Lang::txt('Job %s unpublished.', $id), 'success');
		}
	}

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
			->getHelpOutput()
			 ->addOverview('Run scheduled jobs')
			->addTasks($this)
			->addArgument(
				'--job: run a provided job ID',
				'Provide the ID of the job to be run. This and only this job will be run.',
				'Example: --job=5'
			)
			->addArgument(
				'-a: list all published jobs',
				'List all published jobs regardless of pending status.',
				'Example: -a'
			)
			->render();
	}
}
