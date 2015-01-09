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

		// Create view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'time',
				'element' => 'summary',
				'name'    => 'overview'
			)
		);

		// Get vars from request
		$permissions   = new TimeModelPermissions('com_time');
		$view->hub_id  = JRequest::getInt('hub_id', null);
		$view->task_id = JRequest::getInt('task_id', null);
		$view->start   = JRequest::getCmd('start_date', JFactory::getDate(strtotime('today - 1 month'))->format('Y-m-d'));
		$view->end     = JRequest::getCmd('end_date', JFactory::getDate()->format('Y-m-d'));
		$view->hubs    = array();

		$records = Record::where('date', '>=', $view->start)
		                 ->where('date', '<=', $view->end);

		if (isset($view->task_id) && $view->task_id > 0)
		{
			$records->whereEquals('task_id', $view->task_id);
		}
		else if (isset($view->hub_id) && $view->hub_id > 0)
		{
			$hub_id = $view->hub_id;
			$records->whereRelatedHas('task', function($task) use ($hub_id)
			{
				$task->whereEquals('hub_id', $hub_id);
			});
		}

		foreach ($records->including('task.hub', 'user') as $record)
		{
			if (isset($view->hubs[$record->task->hub_id]))
			{
				$view->hubs[$record->task->hub_id]['total'] += $record->time;
				if (isset($view->hubs[$record->task->hub_id]['tasks'][$record->task_id]))
				{
					$view->hubs[$record->task->hub_id]['tasks'][$record->task_id]['total'] += $record->time;
					$view->hubs[$record->task->hub_id]['tasks'][$record->task_id]['records'][] = $record;
				}
				else
				{
					$view->hubs[$record->task->hub_id]['tasks'][$record->task_id] = [
						'name'    => $record->task->name,
						'total'   => $record->time,
						'records' => array(
							$record
						)
					];
				}
			}
			else
			{
				if ($permissions->can('view.report', 'hub', $record->task->hub_id))
				{
					$view->hubs[$record->task->hub_id] = [
						'name'  => $record->task->hub->name,
						'tasks' => array(
							$record->task_id => [
								'name'    => $record->task->name,
								'total'   => $record->time,
								'records' => [
									$record
								]
							]
						),
						'total' => $record->time
					];
				}
			}
		}

		// Pass permissions to view
		$view->permissions = $permissions;

		return $view->loadTemplate();
	}

	/**
	 * Get time of each task
	 *
	 * @return void
	 */
	public static function getTimePerTask()
	{
		$permissions = new TimeModelPermissions('com_time');
		$hub_id      = JRequest::getInt('hub_id',  null);
		$task_id     = JRequest::getInt('task_id', null);
		$start       = JRequest::getCmd('start_date', JFactory::getDate(strtotime('today - 1 month'))->format('Y-m-d'));
		$end         = JRequest::getCmd('end_date', JFactory::getDate()->format('Y-m-d'));

		$tasks   = Task::blank();
		$records = Record::all();
		$records = $records->select('SUM(time)', 'hours')
		                   ->select($records->getQualifiedFieldName('id'))
		                   ->select('task_id')
		                   ->select($tasks->getQualifiedFieldName('name'))
		                   ->join($tasks->getTableName(), 'task_id', $tasks->getQualifiedFieldName('id'))
		                   ->where('date', '>=', $start)
		                   ->where('date', '<=', $end)
		                   ->order('hours', 'asc')
		                   ->group('task_id');

		if (isset($task_id) && $task_id > 0)
		{
			$records->whereEquals('task_id', $task_id);
		}
		else if (isset($hub_id) && $hub_id > 0)
		{
			$records->whereRelatedHas('task', function($task) use ($hub_id)
			{
				$task->whereEquals('hub_id', $hub_id);
			});
		}

		$summary = array();

		// Loop through and check permissions and grab raw object from rows
		foreach ($records->including('task') as $record)
		{
			if ($permissions->can('view.report', 'hubs', $record->task->hub_id))
			{
				$summary[] = $record->toObject();
			}
		}

		echo json_encode($summary);

		exit();
	}
}