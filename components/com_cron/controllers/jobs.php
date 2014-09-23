<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for bulletin boards
 */
class CronControllerJobs extends \Hubzero\Component\SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->registerTask('display', 'tick');

		parent::execute();
	}

	/**
	 * Display a list of latest whiteboard entries
	 *
	 * @return     string
	 */
	public function displayTask()
	{
		$ip = JRequest::ip();

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

		JRequest::setVar('no_html', 1);
		JRequest::setVar('tmpl', 'component');
		$this->view->no_html = JRequest::getInt('no_html', 0);

		$model = new CronModelJobs();

		$filters = array(
			'state'     => 1,
			'available' => true,
			'next_run'  => JHTML::_('date', JFactory::getDate()->toSql(), 'Y-m-d H:i:s')
		);

		$output = new stdClass;
		$output->jobs = array();

		if ($results = $model->jobs('list', $filters))
		{
			JPluginHelper::importPlugin('cron');
			$dispatcher = JDispatcher::getInstance();

			foreach ($results as $job)
			{
				if ($job->get('active') || !$job->isAvailable())
				{
					continue;
				}

				$job->set('last_run', JHTML::_('date', JFactory::getDate(), 'Y-m-d H:i:s')); //JFactory::getDate()->toSql());
				$job->set('next_run', $job->nextRun());
				$job->store();

				// Show related content
				$job->mark('start_run');

				$results = $dispatcher->trigger($job->get('event'), array($job->get('params')));
				if ($results)
				{
					if (is_array($results))
					{
						// Set it as active in case there were multiple plugins called on
						// the event. This is to ensure ALL processes finished.
						$job->set('active', 1);

						foreach ($results as $result)
						{
							if ($result)
							{
								$job->set('active', 0);
							}
						}
					}
				}

				$job->mark('end_run');
				$job->store();

				$output->jobs[] = $job->toArray();
			}
		}

		$this->view->output = $output;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}
}
