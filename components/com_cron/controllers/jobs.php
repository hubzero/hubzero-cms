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

ximport('Hubzero_Controller');

/**
 * Controller class for bulletin boards
 */
class CronControllerJobs extends Hubzero_Controller
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
		$this->view->no_html = JRequest::getInt('no_html', 0);

		$model = new CronModelJobs();

		$filters = array(
			'state'    => 1,
			'next_run' => date('Y-m-d H:i:s', time())
		);

		$output = new stdClass;
		$output->jobs = array();

		if (($results = $model->jobs($filters)))
		{
			foreach ($results as $job)
			{
				if ($job->get('active'))
				{
					continue;
				}

				$job->set('last_run', date('Y-m-d H:i:s', time()));
				$job->set('next_run', $job->nextRun());
				$job->store();

				JPluginHelper::importPlugin('cron');
				$dispatcher =& JDispatcher::getInstance();

				// Show related content
				$results = $dispatcher->trigger($job->get('event'));
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

				$job->store();

				$output->jobs[] = $job->toArray();
			}
		}

		if (!JDEBUG)
		{
			JError::raiseError(403, JText::_('Permission denied'));
			return;
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
