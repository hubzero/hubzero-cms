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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Components\Time\Models\Permissions;
use Components\Time\Models\Record;
use Components\Time\Models\Task;

// No direct access
defined('_HZEXEC_') or die();

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
		Lang::load('plg_time_summary', __DIR__);

		// Create view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'time',
				'element' => 'summary',
				'name'    => 'overview'
			)
		);

		// Get vars from request
		$permissions   = new Permissions('com_time');
		$view->hub_id  = Request::getInt('hub_id', null);
		$view->task_id = Request::getInt('task_id', null);
		$view->start   = Request::getCmd('start_date', Date::of(strtotime('today - 1 month'))->format('Y-m-d'));
		$view->end     = Request::getCmd('end_date', Date::format('Y-m-d'));
		$view->hubs    = array();

		$records = Record::where('date', '>=', Date::of($view->start . ' 00:00:00', Config::get('offset'))->toSql())
		                 ->where('date', '<=', Date::of($view->end   . ' 23:59:59', Config::get('offset'))->toSql());

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
		$permissions = new Permissions('com_time');
		$hub_id      = Request::getInt('hub_id',  null);
		$task_id     = Request::getInt('task_id', null);
		$start       = Request::getCmd('start_date', Date::of(strtotime('today - 1 month'))->format('Y-m-d'));
		$end         = Request::getCmd('end_date', Date::format('Y-m-d'));

		$tasks   = Task::blank();
		$records = Record::all();
		$records = $records->select('SUM(time)', 'hours')
		                   ->select($records->getQualifiedFieldName('id'))
		                   ->select('task_id')
		                   ->select($tasks->getQualifiedFieldName('name'))
		                   ->join($tasks->getTableName(), 'task_id', $tasks->getQualifiedFieldName('id'))
		                   ->where('date', '>=', Date::of($start . ' 00:00:00', Config::get('offset'))->toSql())
		                   ->where('date', '<=', Date::of($end   . ' 23:59:59', Config::get('offset'))->toSql())
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
			if ($permissions->can('view.report', 'hub', $record->task->hub_id))
			{
				$summary[] = $record->toObject();
			}
		}

		echo json_encode($summary);

		exit();
	}
}
