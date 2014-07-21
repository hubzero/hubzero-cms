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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Plugin for time report summary
 */
class plgTimeSummary extends \Hubzero\Plugin\Plugin
{
	/**
	 * List of accepted methods available by reports controller
	 *
	 * @var array
	 **/
	public static $accepts = array('getTimePerTask');

	/**
	 * Initial render view
	 *
	 * @return (string) view contents
	 */
	public static function render()
	{
		// Load language
		JFactory::getLanguage()->load('plg_time_summary', JPATH_ADMINISTRATOR);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'time',
				'element' => 'summary',
				'name'    => 'overview'
			)
		);

		$database         = JFactory::getDbo();
		$hubTbl           = new TimeHubs($database);
		$taskTbl          = new TimeTasks($database);
		$recordTbl        = new TimeRecords($database);
		$view->hub_id     = JRequest::getInt('hub_id', null);
		$view->task_id    = JRequest::getInt('task_id', null);
		$view->start      = JRequest::getCmd('start_date', JFactory::getDate(strtotime('today - 1 month'))->format('Y-m-d'));
		$view->end        = JRequest::getCmd('end_date', JFactory::getDate()->format('Y-m-d'));
		$view->tasksList  = $taskTbl->getTasks();
		$view->hubsList   = $hubTbl->getRecords();
		$view->hubs       = array();
		$records          = $recordTbl->getRecords(
			array(
				'orderby'  => 'h.name',
				'orderdir' => 'ASC',
				'q'        => array(
					array(
						'column' => 'date',
						'o'      => '>=',
						'value'  => $view->start
					),
					array(
						'column' => 'date',
						'o'      => '<=',
						'value'  => $view->end
					),
					array(
						'column' => 'h.id',
						'o'      => '=',
						'value'  => (isset($view->hub_id) && $view->hub_id > 0) ? $view->hub_id : null
					),
					array(
						'column' => 'p.id',
						'o'      => '=',
						'value'  => (isset($view->task_id) && $view->task_id > 0) ? $view->task_id : null
					),
				)
			)
		);

		foreach ($records as $record)
		{
			if (isset($view->hubs[$record->hid]))
			{
				$view->hubs[$record->hid]['total'] += $record->time;
				if (isset($view->hubs[$record->hid]['tasks'][$record->pid]))
				{
					$view->hubs[$record->hid]['tasks'][$record->pid]['total'] += $record->time;
					$view->hubs[$record->hid]['tasks'][$record->pid]['records'][] = $record;
				}
				else
				{
					$view->hubs[$record->hid]['tasks'][$record->pid] = array(
						'name'    => $record->pname,
						'total'   => $record->time,
						'records' => array(
							$record
						)
					);
				}
			}
			else
			{
				$view->hubs[$record->hid] = array(
					'name'  => $record->hname,
					'tasks' => array(
						$record->pid => array(
							'name'    => $record->pname,
							'total'   => $record->time,
							'records' => array(
								$record
							)
						)
					),
					'total' => $record->time
				);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Get time of each task
	 *
	 * @return void
	 */
	public static function getTimePerTask()
	{
		$database   = JFactory::getDbo();
		$records    = new TimeRecords($database);
		$hub_id     = JRequest::getInt('hub_id',  null);
		$task_id    = JRequest::getInt('task_id', null);
		$start      = JRequest::getCmd('start_date', JFactory::getDate(strtotime('today - 1 month'))->format('Y-m-d'));
		$end        = JRequest::getCmd('end_date', JFactory::getDate()->format('Y-m-d'));
		$summary    = $records->getSummaryHours(
			array(
				'orderby'    => 'hours',
				'orderdir'   => 'ASC',
				'hub_id'     => $hub_id,
				'task_id'    => $task_id,
				'start_date' => $start,
				'end_date'   => $end
			)
		);
		echo json_encode($summary);

		exit();
	}
}