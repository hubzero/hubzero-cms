<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

					$data[$k . '-' . $i . '-' . $d] = $amount;

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
