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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\EventsCalendar;

use Hubzero\Module\Module;
use Components\Events\Helpers\Html;
use Route;
use Lang;
use App;

/**
 * Class for events calendar module
 */
class Helper extends Module
{
	/**
	 * Display module utput
	 *
	 * @return  void
	 */
	public function display()
	{
		$this->css();

		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}

	/**
	 * Gnerate events calendar
	 *
	 * @return  void
	 */
	public function run()
	{
		// Check the events component
		if (file_exists(\Component::path('com_events') . DS . 'helpers' . DS . 'html.php'))
		{
			include_once(\Component::path('com_events') . DS . 'helpers' . DS . 'html.php');
			include_once(\Component::path('com_events') . DS . 'helpers' . DS . 'date.php');
		}
		else
		{
			$this->setError(Lang::txt('MOD_EVENTS_LATEST_COMPONENT_REQUIRED'));
			return;
		}

		// Display last month?
		$displayLastMonth = $this->params->get('display_last_month');
		switch ($displayLastMonth)
		{
			case 'YES_stop':
				$disp_lastMonthDays = abs(intval($this->params->get('display_last_month_days')));
				$disp_lastMonth = 1;
				break;
			case 'YES_stop_events':
				$disp_lastMonthDays = abs(intval($this->params->get('display_last_month_days')));
				$disp_lastMonth = 2;
				break;
			case 'ALWAYS':
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 1;
				break;
			case 'ALWAYS_events':
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 2;
				break;
			case 'NO':
			default:
				$disp_lastMonthDays = 0;
				$disp_lastMonth = 0;
				break;
		}

		// Display next month?
		$displayNextMonth = $this->params->get('display_next_month');
		switch ($displayNextMonth)
		{
			case 'YES_stop':
				$disp_nextMonthDays = abs(intval($this->params->get('display_next_month_days')));
				$disp_nextMonth = 1;
				break;
			case 'YES_stop_events':
				$disp_nextMonthDays = abs(intval($this->params->get('display_next_month_days')));
				$disp_nextMonth = 2;
				break;
			case 'ALWAYS':
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 1;
				break;
			case 'ALWAYS_events':
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 2;
				break;
			case 'NO':
			default:
				$disp_nextMonthDays = 0;
				$disp_nextMonth = 0;
				break;
		}

		// Get the time with offset
		$timeWithOffset = time() + (Config::get('offset')*60*60);

		// Get the start day
		$startday = $this->params->get('start_day');
		if (!defined('_CAL_CONF_STARDAY'))
		{
			define('_CAL_CONF_STARDAY', $startday);
		}
		//define('_CAL_CONF_DATEFORMAT',1);
		//define('_CAL_CONF_MAILVIEW','YES');
		if ((!$startday) || ($startday > 1))
		{
			$startday = 0;
		}

		// An array of the names of the days of the week
		$day_name = array(
			Lang::txt('EVENTS_CAL_LANG_SUNDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_MONDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_TUESDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_WEDNESDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_THURSDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_FRIDAYSHORT'),
			Lang::txt('EVENTS_CAL_LANG_SATURDAYSHORT')
		);

		$this->content = '';

		// Display a calendar. Want to show 1,2, or 3 calendars optionally
		// depending upon module parameters. (IE. Last Month, This Month, or Next Month)
		$thisDayOfMonth = date("j", $timeWithOffset);
		$daysLeftInMonth = date("t", $timeWithOffset) - date("j", $timeWithOffset) + 1;

		// Display last month?
		if ($disp_lastMonth && (!$disp_lastMonthDays || $thisDayOfMonth <= $disp_lastMonthDays))
		{
			// Build last month calendar
			$this->content .= $this->_calendar($timeWithOffset, $startday, mktime(0, 0, 0, date("n") - 1, 1, date("Y")), Lang::txt('_CAL_LANG_LAST_MONTH'), $day_name, $disp_lastMonth == 2);
		}

		// Build this month
		$this->content .= $this->_calendar($timeWithOffset, $startday, mktime(0, 0, 0, date("n"), 1, date("Y")), Lang::txt('EVENTS_CAL_LANG_THIS_MONTH'), $day_name);

		// Display next month?
		if ($disp_nextMonth && (!$disp_nextMonthDays || $daysLeftInMonth <= $disp_nextMonthDays))
		{
			// Build next month calendar
			$this->content .= $this->_calendar($timeWithOffset, $startday, mktime(0, 0, 0, date("n") + 1, 1, date("Y")), Lang::txt('_CAL_LANG_NEXT_MONTH'), $day_name, $disp_nextMonth == 2);
		}

		require $this->getLayoutPath();
	}

	/**
	 * Short description for '_calendar'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $timeWithOffset Parameter description (if any) ...
	 * @param      number $startday Parameter description (if any) ...
	 * @param      unknown $time Parameter description (if any) ...
	 * @param      unknown $linkString Parameter description (if any) ...
	 * @param      array &$day_name Parameter description (if any) ...
	 * @param      boolean $monthMustHaveEvent Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function _calendar($timeWithOffset, $startday, $time, $linkString, &$day_name, $monthMustHaveEvent=false)
	{
		$database = App::get('db');

		$cal_year  = date("Y", $time);
		$cal_month = date("m", $time);
		$calmonth  = date("n", $time);
		$to_day    = date("Y-m-d", $timeWithOffset);

		// Start building the table
		$content  = '<table class="mod_events_calendar">'."\n";
		$content .= ' <caption>'."\n";
		if ($this->params->get('show_nav_prev_month'))
		{
			$content .= ' <a class="prev month" href="' . Route::url('index.php?option=com_events&year='.($cal_month == 1 ? $cal_year - 1 : $cal_year).'&month='.($cal_month == 1 ? 12 : $cal_month - 1)) . '">'.Html::getMonthName(($cal_month == 1 ? 12 : $cal_month - 1)).'</a>'."\n";
		}
		$content .= ' <a class="current month" href="' . Route::url('index.php?option=com_events&year='.$cal_year.'&month='.$cal_month) . '">' . Html::getMonthName($cal_month) . '</a>'."\n";
		if ($this->params->get('show_nav_next_month'))
		{
			$content .= ' <a class="next month" href="' . Route::url('index.php?option=com_events&year='.($cal_month == 12 ? $cal_year + 1 : $cal_year).'&month='.($cal_month == 12 ? 1 : $cal_month + 1)) . '">'.Html::getMonthName(($cal_month == 12 ? 1 : $cal_month + 1)).'</a>'."\n";
		}
		$content .= ' </caption>'."\n";
		$content .= ' <thead>'."\n";
		$content .= '  <tr>'."\n";
		// Days name rows
		for ($i=0; $i<7; $i++)
		{
			$content.='   <th scope="col">'.$day_name[($i+$startday)%7].'</th>'."\n";
		}
		$content .= '  </tr>'."\n";
		$content .= ' </thead>'."\n";
		$content .= ' <tbody>'."\n";
		$content .= '  <tr>'."\n";

		// Fix to fill in end days out of month correctly
		$dayOfWeek = $startday;
		$start = (date("w", mktime(0, 0, 0, $cal_month, 1, $cal_year))-$startday+7)%7;
		$d = date("t", mktime(0, 0, 0, $cal_month, 0, $cal_year))-$start + 1;
		$kownt = 0;
		for ($a=$start; $a>0; $a--)
		{
			$content .= '   <td class="daylink">&nbsp;</td>'."\n";
			$dayOfWeek++;
			$kownt++;
		}

		$monthHasEvent = false;
		$lastDayOfMonth = date("t", mktime(0, 0, 0, $cal_month, 1, $cal_year));
		$rd = 0;
		for ($d=1; $d<=$lastDayOfMonth; $d++)
		{
			$do = ($d<10) ? "0$d" : "$d";
			$selected_date = "$cal_year-$cal_month-$do";

			$sql = "SELECT `#__events`.* FROM `#__events`, `#__categories` as b"
				. " WHERE #__events.catid = b.id " // AND b.access <= $gid AND #__events.access <= $gid"
				. " AND ((publish_up >= '$selected_date 00:00:00' AND publish_up <= '$selected_date 23:59:59')"
				. " OR (publish_down >= '$selected_date 00:00:00' AND publish_down <= '$selected_date 23:59:59')"
				. " OR (publish_up <= '$selected_date 00:00:00' AND publish_down >= '$selected_date 23:59:59')) AND state='1'"
				. " AND (#__events.scope IS NULL OR #__events.scope=" . $database->quote('event') . ")"
				. " ORDER BY publish_up ASC";

			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$mark_bold = '';
			$mark_close_bold = '';
			$class = ($selected_date == $to_day) ? 'todaynoevents' : 'daynoevents';

			// do we have events
			if (count($rows) > 0)
			{
				$class = 'daywithevents';
				if ($selected_date == $to_day)
				{
					$class = 'todaywithevents';
				}
			}

			// Only adds link if event scheduled that day
			$content .= '   <td class="'.$class.'">';
			if ($class == 'todaywithevents' || $class == 'daywithevents')
			{
				$content .= '<a class="mod_events_daylink" href="' . Route::url('index.php?option=com_events&year=' . $cal_year . '&month=' . $cal_month . '&day=' . $do) . '">' . $d . '</a>';
			}
			else
			{
				$content .= "$d";
			}
			$content .= '</td>'."\n";
			$rd++;

			// Check if Next week row
			if ((1 + $dayOfWeek++)%7 == $startday)
			{
				$content .= '  </tr>'."\n".'  <tr>'."\n";
				$rd = ($rd >= 7) ? 0 : $rd;
			}
		}

		// Fill in any blank days for the rest of the row
		for ($d=$rd; $d<=6; $d++)
		{
			$content .= '   <td>&nbsp;</td>'."\n";
		}

		// Finish off the table
		$content .= '  </tr>'."\n";
		$content .= ' </tbody>'."\n";
		$content .= '</table>'."\n";

		// Now check to see if this month needs to have at least 1 event in order to display
		if (!$monthMustHaveEvent || $monthHasEvent)
		{
			return $content;
		}
		else
		{
			return '';
		}
	}
}
