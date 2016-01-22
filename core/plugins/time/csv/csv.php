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
use Components\Time\Models\Hub;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for time report csv download
 */
class plgTimeCsv extends \Hubzero\Plugin\Plugin
{
	/**
	 * List of accepted methods available by reports controller
	 *
	 * @var array
	 **/
	public static $accepts = array('download');

	/**
	 * Initial render view
	 *
	 * @return (string) view contents
	 */
	public static function render()
	{
		// Load language
		Lang::load('plg_time_csv', __DIR__);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'time',
				'element' => 'csv',
				'name'    => 'overview'
			)
		);

		$view->hub_id = Request::getInt('hub_id', null);
		$view->start  = Request::getCmd('start_date', Date::of(strtotime('today - 1 month'))->format('Y-m-d'));
		$view->end    = Request::getCmd('end_date', Date::format('Y-m-d'));
		$records      = Record::all()->where('date', '>=', $view->start)
		                              ->where('date', '<=', $view->end);
		                              // @FIXME: order by non-native field
		                              //->order('h.name', 'asc');

		if (isset($view->hub_id) && $view->hub_id > 0)
		{
			// @FIXME: is there a better way to do this?
			$records->whereIn('task_id', Task::select('id')->whereEquals('hub_id', $view->hub_id)->rows()->fieldsByKey('id'));
		}

		// Pass permissions to view
		$view->permissions = new Permissions('com_time');
		$view->records     = $records->including('task.hub', 'user')->rows();

		return $view->loadTemplate();
	}

	/**
	 * Download CSV
	 *
	 * @return void
	 */
	public static function download()
	{
		// Load language
		Lang::load('plg_time_csv', __DIR__);

		$hub_id    = Request::getInt('hub_id', null);
		$start     = Request::getCmd('start_date', Date::of(strtotime('today - 1 month'))->format('Y-m-d'));
		$end       = Request::getCmd('end_date', Date::format('Y-m-d'));
		$records   = Record::all()->where('date', '>=', $start)
		                           ->where('date', '<=', $end);
		                           // @FIXME: order by non-native field
		                           //->order('h.name', 'asc');

		if (isset($hub_id) && $hub_id > 0)
		{
			// @FIXME: is there a better way to do this?
			$records->whereIn('task_id', Task::select('id')->whereEquals('hub_id', $hub_id)->rows()->fieldsByKey('id'));
			$hubname = Hub::oneOrFail($hub_id)->name_normalized;
		}

		$all  = true;
		foreach (Request::query() as $key => $value)
		{
			if (strpos($key, 'fields-') !== false)
			{
				$all = false;
			}
		}

		$filename  = 'time_report';
		$filename .= (isset($hubname)) ? '_' . $hubname : '';
		$filename .= '_' . Date::of($start)->format('Ymd');
		$filename .= '-' . Date::of($end)->format('Ymd');
		$filename .= '.csv';

		// Set content type headers
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=$filename");
		header("Pragma: no-cache");
		header("Expires: 0");

		$row = array();
		if ($hub = Request::getInt('fields-hub', $all))
		{
			$row[] = Lang::txt('PLG_TIME_CSV_HUB');
		}
		if ($task = Request::getInt('fields-task', $all))
		{
			$row[] = Lang::txt('PLG_TIME_CSV_TASK');
		}
		if ($user = Request::getInt('fields-user', $all))
		{
			$row[] = Lang::txt('PLG_TIME_CSV_USER');
		}
		if ($date = Request::getInt('fields-date', $all))
		{
			$row[] = Lang::txt('PLG_TIME_CSV_DATE');
		}
		if ($time = Request::getInt('fields-time', $all))
		{
			$row[] = Lang::txt('PLG_TIME_CSV_TIME');
		}
		if ($description = Request::getInt('fields-description', $all))
		{
			$row[] = Lang::txt('PLG_TIME_CSV_DESCRIPTION');
		}
		echo implode(',', $row) . "\n";

		$permissions = new Permissions('com_time');

		foreach ($records->including('task.hub', 'user') as $record)
		{
			if ($permissions->can('view.report', 'hub', $record->task->hub_id))
			{
				$output = fopen('php://output','w');
				$row    = array();
				if ($hub)
				{
					$row[] = $record->task->hub->name;
				}
				if ($task)
				{
					$row[] = $record->task->name;
				}
				if ($user)
				{
					$row[] = $record->user->name;
				}
				if ($date)
				{
					$row[] = $record->date;
				}
				if ($time)
				{
					$row[] = $record->time;
				}
				if ($description)
				{
					$row[] = $record->description;
				}

				fputcsv($output, $row);
				fclose($output);
			}
		}

		exit();
	}
}