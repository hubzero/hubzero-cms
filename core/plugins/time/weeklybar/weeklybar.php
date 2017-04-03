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

use Components\Time\Models\Record;
use Components\Time\Models\Proxy;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for time report weekly bar graphs
 */
class plgTimeWeeklybar extends \Hubzero\Plugin\Plugin
{
	/**
	 * List of accepted methods available to the reports controller
	 *
	 * @var array
	 **/
	public static $accepts = array('getTimeForWeeklyBar');

	/**
	 * Initial render view
	 *
	 * @return  string
	 */
	public static function render()
	{
		// Load language
		Lang::load('plg_time_weeklybar', __DIR__);

		// Create view
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'time',
				'element' => 'weeklybar',
				'name'    => 'overview'
			)
		);

		return $view->loadTemplate();
	}

	/**
	 * Get summary of time for each person on each day of the week
	 *
	 * @return  void
	 */
	public static function getTimeForWeeklyBar()
	{
		$records = Record::all();
		$records->select('user_id')
		        ->select('SUM(time)', 'time')
		        ->select('DATE_FORMAT(CONVERT_TZ(date, "+00:00", "' . Config::get('offset', '+00:00') . '"), "%Y-%m-%d")', 'day')
		        ->group('user_id')
		        ->group('day');

		$users = [User::get('id')];

		// Add extra users for proxies
		foreach (Proxy::whereEquals('proxy_id', User::get('id')) as $proxy)
		{
			$users[] = $proxy->user_id;
		}

		$records->whereIn('user_id', $users);

		// Get the day of the week
		$today       = Date::of(Request::getVar('week', time()));
		$dateofToday = $today->format('Y-m-d');
		$dayOfWeek   = $today->format('N') - 1;

		$records->having('day', '>=', Date::of(strtotime("{$dateofToday} - {$dayOfWeek}days"))->toLocal('Y-m-d'))
		        ->having('day', '<', Date::of(strtotime("{$dateofToday} + " . (7-$dayOfWeek) . 'days'))->toLocal('Y-m-d'));

		$rows = $records->including('user')->rows();

		foreach ($rows as $row)
		{
			$row->set('user_name', $row->user->name);
		}

		echo json_encode($rows->toArray());

		exit();
	}
}
