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
				$ips = gethostbynamel($_SERVER['SERVER_NAME']);

				if (!in_array($ip, $ips))
				{
					$ips = gethostbynamel('localhost');

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

		foreach ($results as $job)
		{
			if ($job->get('active') || !$job->isAvailable())
			{
				continue;
			}

			// Set it as active in case there were multiple plugins called on
			// the event. This is to ensure ALL processes finished.
			$job->set('active', 1);
			$job->save();

			// Show related content
			$job->mark('start_run');

			$res = Event::trigger('cron.' . $job->get('event'), array($job));

			if ($res && is_array($res))
			{
				foreach ($res as $result)
				{
					if ($result)
					{
						$job->set('active', 0);
					}
				}
			}

			$job->mark('end_run');
			$job->set('last_run', Date::toSql());
			$job->set('next_run', $job->nextRun());
			$job->save();

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
