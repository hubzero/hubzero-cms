<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cron\Site\Controllers;

use Components\Cron\Models\Manager;
use Hubzero\Component\SiteController;
use Request;
use User;
use stdClass;

/**
 * Controller class for bulletin boards
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
	 * Display a list of latest whiteboard entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
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

		Request::setVar('no_html', 1);
		Request::setVar('tmpl', 'component');

		$model = new Manager();

		$filters = array(
			'state'     => 1,
			'available' => true,
			'next_run'  => \JHTML::_('date', \JFactory::getDate()->toSql(), 'Y-m-d H:i:s')
		);

		$output = new stdClass;
		$output->jobs = array();

		if ($results = $model->jobs('list', $filters))
		{
			\JPluginHelper::importPlugin('cron');
			$dispatcher = \JDispatcher::getInstance();

			foreach ($results as $job)
			{
				if ($job->get('active') || !$job->isAvailable())
				{
					continue;
				}

				// Show related content
				$job->mark('start_run');

				$results = $dispatcher->trigger($job->get('event'), array($job));
				if ($results && is_array($results))
				{
					// Set it as active in case there were multiple plugins called on
					// the event. This is to ensure ALL processes finished.
					$job->set('active', 1);
					$job->store();

					foreach ($results as $result)
					{
						if ($result)
						{
							$job->set('active', 0);
						}
					}
				}

				$job->mark('end_run');
				$job->set('last_run', \JHTML::_('date', \JFactory::getDate(), 'Y-m-d H:i:s')); //JFactory::getDate()->toSql());
				$job->set('next_run', $job->nextRun());
				$job->store();

				$output->jobs[] = $job->toArray();
			}
		}

		$this->view
			->set('no_html', Request::getInt('no_html', 0))
			->set('output', $output)
			->display();
	}
}
