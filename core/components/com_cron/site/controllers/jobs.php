<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		// try to see if their IP address is in the whtielist.
		// Otherwise, we stop any further code execution.
		if (!User::authorise('core.manage', $this->_option))
		{
			$ip = Request::ip();

			$ips = explode(',', $this->config->get('whitelist',''));
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
			->where('next_run', '<=', Date::toLocal('Y-m-d H:i:s'))
			->whereEquals('publish_up', '0000-00-00 00:00:00', 1)->orWhere('publish_up', '<=', $now, 1)
			->resetDepth()
			->whereEquals('publish_down', '0000-00-00 00:00:00', 1)->orWhere('publish_down', '>', $now, 1)
			->rows();

		$output = new stdClass;
		$output->jobs = array();

		if ($results)
		{
			foreach ($results as $job)
			{
				if ($job->get('active') || !$job->isAvailable())
				{
					continue;
				}

				// Show related content
				$job->mark('start_run');

				$results = Event::trigger('cron.' . $job->get('event'), array($job));
				if ($results && is_array($results))
				{
					// Set it as active in case there were multiple plugins called on
					// the event. This is to ensure ALL processes finished.
					$job->set('active', 1);
					$job->save();

					foreach ($results as $result)
					{
						if ($result)
						{
							$job->set('active', 0);
						}
					}
				}

				$job->mark('end_run');
				$job->set('last_run', Date::toLocal('Y-m-d H:i:s')); //Date::toSql());
				$job->set('next_run', $job->nextRun());
				$job->save();

				$output->jobs[] = $job->toArray();
			}
		}

		// Output any data from the jobs that ran
		// Largely used for debugging/monitoring purposes
		$this->view
			->set('no_html', Request::getInt('no_html', 0))
			->set('output', $output)
			->display();
	}
}
