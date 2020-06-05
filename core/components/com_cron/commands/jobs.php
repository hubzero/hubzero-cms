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

		if ($jobs->count())
		{
			$this->output->addLine(Lang::txt('[%s] Starting scheduled jobs ...', $now), 'info');

			foreach ($jobs as $job)
			{
				if ($job->get('active') || !$job->isAvailable())
				{
					continue;
				}

				$now = with(new Date('now'))->toLocal();

				$this->output->addLine(Lang::txt('[%s] Starting event "%s" ...', $now, $job->get('event')), 'info');

				$job->mark('start_run');

				try
				{
					$res = Event::trigger('cron.' . $job->get('event'), array($job));

					$now = with(new Date('now'))->toLocal();

					$this->output->addLine(Lang::txt('[%s] Finished event "%s".', $now, $job->get('event')), 'info');

					$job->mark('end_run');
					$job->set('last_run', $now);
					$job->set('next_run', $job->nextRun());
					$job->save();

					$processed[] = $job->toArray();
				}
				catch (\Exception $e)
				{
					$now = with(new Date('now'))->toLocal();

					$this->output->addLine(Lang::txt('[%s] Event "%s" generated an error. Skipping...', $now, $job->get('event')), 'error');
				}
			}

			$this->output->addLine(Lang::txt('[%s] Finished scheduled jobs.', $now), 'success');
		}
		else
		{
			$this->output->addLine(Lang::txt('No scheduled jobs found.'), 'info');
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
