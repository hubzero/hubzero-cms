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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Activity\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Activity\Log as Activity;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;

/**
 * Activity controller class for entries
 */
class Entries extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$start = Date::of('now')->modify('-4 weeks');

		$year  = (int)$start->format('Y');
		$month = (int)$start->format('m');
		$day   = (int)$start->format('d');

		$tyear  = (int)Date::of('now')->format('Y');
		$tmonth = (int)Date::of('now')->format('m');
		$tday   = (int)Date::of('now')->format('d');

		// Group data by year and gather some info for each user
		$y = gmdate("Y");
		$y++;
		$today = false;
		$total = 0;

		$data = array();
		for ($k=$year, $n=$y; $k < $n; $k++)
		{
			if ($today)
			{
				break;
			}

			$i = 1;
			if ($k == $year)
			{
				$i = $month;
			}

			for ($i; $i <= 12; $i++)
			{
				if ($today)
				{
					break;
				}

				$days = cal_days_in_month(CAL_GREGORIAN, $i, $k);

				$d = 1;
				if ($k == $year && $i == $month)
				{
					$d = $day;
				}

				for ($d; $d <= $days; $d++)
				{
					$amount = Activity::all()
						->where('created', '>', $k . '-' . ($i < 10 ? '0' . $i : $i) . '-' . ($d < 10 ? '0' . $d : $d) . ' 00:00:00')
						->where('created', '<', $k . '-' . ($i < 10 ? '0' . $i : $i) . '-' . ($d < 10 ? '0' . $d : $d) . ' 24:59:59')
						->total();

					$data[$k . ',' . $i . ',' . $d] = $amount;

					$total = $total + $amount;

					if ($k == $tyear && $i == $tmonth && $d == $tday)
					{
						// Today!
						$today = true;
						break;
					}
				}
			}
		}

		// Output the HTML
		$this->view
			->set('data', $data)
			->set('total', $total)
			->display();
	}
}
