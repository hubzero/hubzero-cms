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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Manage resource types
 */
class CronControllerScripts extends JObject
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->database = JFactory::getDBO();

		$model = new CronJob($this->database);

		$this->filters = array(
			'state'    => 1,
			'next_run' => date('Y-m-d H:i:s', time())
		);

		$this->results = $model->getJobs($this->filters);

		$output = array();

		if ($this->results)
		{
			foreach ($this->results as $row)
			{
				$json = array(
					'id'       => $row->id,
					'title'    => $row->title,
					'plugin'   => $row->plugin,
					'event'    => $row->event,
					'last_run' => $row->last_run,
					'next_run' => $row->next_run,
					'active'   => $row->active
				);
				$model->id       = $row->id;
				/*$model->title    = $row->title;
				$model->plugin   = $row->plugin;
				$model->event    = $row->event;
				$model->last_run = $row->last_run;
				$model->next_run = $row->next_run;*/
				$model->active   = $row->active;

				if ($row->active)
				{
					continue;
				}

				$cron = Cron\CronExpression::factory($row->recurrence);

				$json['last_run'] = $model->last_run = date('Y-m-d H:i:s', time()); //$cron->getPreviousRunDate()->format('Y-m-d H:i:s');
				$json['next_run'] = $model->next_run = $cron->getNextRunDate()->format('Y-m-d H:i:s');
				$json['active']   = $model->active   = 1;

				$model->store();

				JPluginHelper::importPlugin('cron');
				$dispatcher =& JDispatcher::getInstance();

				// Show related content
				$results = $dispatcher->trigger($row->event);
				if ($results)
				{
					if (is_array($results))
					{
						foreach ($results as $result)
						{
							// Set it as active in case there were multiple plugins called on
							// the event. This is to ensure ALL processes finished.
							$json['active'] = $model->active = 1;
							if ($result)
							{
								$json['active'] = $model->active = 0;
							}
						}
						//$model->store();
					}
				}

				$model->store();

				$output[] = $json;
			}
		}

		echo json_encode($output);
	}
}
