<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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

namespace Modules\EventsLatest;

use Hubzero\Module\Module;
use Lang;
use App;

/**
 * Parameters:
 * ===========
 *
 * maxEvents = max. no. of events to display in the module (1 to 10, default is 5)
 *
 * mode:
 * = 0  (default) display events for current week and following week only up to 'maxEvents'.
 *
 * = 1  same as 'mode'=0 except some past events for the current week will also be
 *      displayed if num of future events is less than $maxEvents.
 *
 * = 2  display events for +'days' range relative to current day up to $maxEvents.
 *
 * = 3  same as mode 2 except if there are < 'maxEvents' in the range,
 *      then display past events within -'days' range.
 *
 * = 4  display events for current month up to 'maxEvents'.
 *
 * days: (default=7) range of days relative to current day to display events for mode 1 or 3.
 *
 * displayLinks = 1 (default is 0) display event titles as links to the 'view_detail' com_events
 *                   task which will display details of the event.
 *
 * displayYear = 1 (default is 0) display year when displaying dates in the non-customized event's listing.
 */
class Helper extends Module
{
	/**
	 * Display module output
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
	 * Generate module output
	 *
	 * @return  voif
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
			$this->error = Lang::txt('MOD_EVENTS_LATEST_COMPONENT_REQUIRED');
			return;
		}

		$database = App::get('db');

		// Get module parameters
		$mode              = $this->params->get('mode')                ? abs(intval($this->params->get('mode'))) : 4;
		$days              = $this->params->get('days')                ? abs(intval($this->params->get('days'))) : 7;
		$maxEvents         = $this->params->get('max_events')          ? abs(intval($this->params->get('max_events'))) : 5;
		$displayLinks      = $this->params->get('display_links')       ? abs(intval($this->params->get('display_links'))) : 0;
		$displayYear       = $this->params->get('display_year')        ? abs(intval($this->params->get('display_year'))) : 0;
		$disableTitleStyle = $this->params->get('display_title_style') ? abs(intval($this->params->get('display_title_style'))) : 0;
		$disableDateStyle  = $this->params->get('display_date_style')  ? abs(intval($this->params->get('display_date_style'))) : 0;
		$customFormatStr   = $this->params->get('custom_format_str')   ? $this->params->get('custom_format_str') : null;
		$charlimit         = $this->params->get('char_limit')          ? abs(intval($this->params->get('char_limit'))) : 150;

		// Can't have a mode greater than 4
		if ($mode > 4)
		{
			$mode = 0;
		}

		// Hardcoded to 10 for now to avoid bad mistakes in params
		if (!$maxEvents || $maxEvents > 100)
		{
			$maxEvents = 10;
		}

		// Derive the event date range we want based on current date and form the db query.
		$todayBegin = date('Y-m-d') . ' 00:00:00';
		$yesterdayEnd = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))) . ' 23:59:59';

		// Get the start day
		$startday = $this->params->get('start_day');
		if (!defined('_CAL_CONF_STARDAY'))
		{
			define('_CAL_CONF_STARDAY', $startday);
		}

		// Set some vars depending upon mode
		switch ($mode)
		{
			case 0:
			case 1:
				// week start (ie. Sun or Mon) is according to what has been selected in the events
				// component configuration thru the events admin interface.
				//if (!defined(_CAL_CONF_STARDAY)) define(_CAL_CONF_STARDAY, 0);
				$numDay = (date("w")-_CAL_CONF_STARDAY + 7)%7;
				// begin of this week
				$beginDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $numDay, date('Y'))) . ' 00:00:00';
				//$thisWeekEnd = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - date('w')+6, date('Y'))." 23:59:59";
				// end of next week
				$endDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $numDay + 13, date('Y'))) . ' 23:59:59';
				break;
			case 2:
			case 3:
				// Begin of today - $days
				$beginDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $days, date('Y'))) . ' 00:00:00';
				// End of today + $days
				$endDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + $days, date('Y'))) . ' 23:59:59';
				break;
			case 4:
			default:
				// Beginning of this month
				//$beginDate = date('Y-m-d', mktime(0,0,0,date('m'),1, date('Y')))." 00:00:00";
				//start today
				$beginDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y'))) . ' 00:00:00';
				// end of this month
				//$endDate = date('Y-m-d', mktime(0,0,0,date('m')+1,0, date('Y'))) . ' 23:59:59';
				// end of this year
				//$endDate = date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y') + 1)) . ' 23:59:59';
				$endDate = gmdate('Y-m-d', mktime(0, 0, 0, gmdate('m')+1, 0, gmdate('Y'))) . ' 23:59:59';
				break;
		}

		// Display events
		$query = "SELECT `#__events`.* FROM `#__events`, `#__categories` as b"
			. " WHERE #__events.catid = b.id "
			. " AND #__events.state='1'"
			. " AND ((publish_up <= '$todayBegin%' AND publish_down >= '$todayBegin%')"
			. " OR (publish_up <= '$endDate%' AND publish_down >= '$endDate%')"
			. " OR (publish_up <= '$endDate%' AND publish_up >= '$todayBegin%')"
			. " OR (publish_down <= '$endDate%' AND publish_down >= '$todayBegin%'))"
			. " AND (#__events.scope IS NULL OR #__events.scope=" . $database->quote('event') . ")"
			. " ORDER BY publish_up ASC LIMIT $maxEvents";

		// Retrieve the list of returned records as an array of objects
		$database->setQuery($query);
		$this->events = $database->loadObjectList();

		// // Determine the events that occur each day within our range
		// $events = 0;
		// $date = mktime(0, 0, 0);
		// $lastDate = mktime(0, 0, 0, intval(substr($endDate, 5, 2)), intval(substr($endDate, 8, 2)), intval(substr($endDate, 0, 4)));
		// $i = 0;

		// $content  = '';
		// $seenThisEvent = array();

		// if (count($rows))
		// {
		// 	while ($date <= $lastDate)
		// 	{
		// 		// Get the events for this $date
		// 		$eventsThisDay = $this->_getEventsByDate($rows, $date, $seenThisEvent);
		// 		echo '<pre>';
		// 		print_r($eventsThisDay);
		// 		echo '</pre>';
		// 		if (count($eventsThisDay))
		// 		{
		// 			// dmcd May 7/04  bug fix to not exceed maxEvents
		// 			$eventsToAdd = min($maxEvents-$events, count($eventsThisDay));
		// 			$eventsThisDay = array_slice($eventsThisDay, 0, $eventsToAdd);
		// 			$eventsByRelDay[$i] = $eventsThisDay;
		// 			$events += count($eventsByRelDay[$i]);
		// 		}
		// 		if ($events >= $maxEvents)
		// 		{
		// 			break;
		// 		}
		// 		$date = mktime(0, 0, 0, date('m', $date), date('d', $date) + 1, date('Y', $date));
		// 		$i++;
		// 	}
		// }

		// echo '<pre>';
		// print_r(this);
		// echo '</pre>';

		// // Do we actually have any events to display?
		// if ($events < $maxEvents && ($mode==1 || $mode==3))
		// {
		// 	// Display some recent previous events too up to a total of $maxEvents
		// 	// Changed by Swaroop to display only events that are not announcements
		// 	$query = "SELECT #__events.* FROM #__events, #__categories as b"
		// 		. "\nWHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid AND (#__events.state='1' $ancmnt AND #__events.checked_out='0')"
		// 		. "\n	AND ((publish_up <= '$beginDate%' AND publish_down >= '$beginDate%')"
		// 		. "\n	OR (publish_up <= '$yesterdayEnd%' AND publish_down >= '$yesterdayEnd%')"
		// 		. "\n   OR (publish_up <= '$yesterdayEnd%' AND publish_up >= '$beginDate%')"
		// 		. "\n   OR (publish_down <= '$yesterdayEnd%' AND publish_down >= '$beginDate%'))"
		// 		. "\n   AND (#__events.scope IS NULL OR #__events.scope=" . $database->quote('event') . ")"
		// 		. "\n  ORDER BY publish_up DESC";

		// 	// Initialise the query in the $database connector
		// 	// This translates the '#__' prefix into the real database prefix
		// 	$database->setQuery($query);

		// 	// Retrieve the list of returned records as an array of objects
		// 	$prows = $database->loadObjectList();

		// 	if (count($prows))
		// 	{
		// 		// Start from yesterday
		// 		$date = mktime(23, 59, 59, date('m'), date('d') - 1, date('Y'));
		// 		$lastDate = mktime(0, 0, 0, intval(substr($beginDate, 5, 2)), intval(substr($beginDate, 8, 2)), intval(substr($beginDate, 0, 4)));
		// 		$i = -1;

		// 		while ($date >= $lastDate)
		// 		{
		// 			// Get the events for this $date
		// 			$eventsThisDay = $this->_getEventsByDate($prows, $date, $seenThisEvent, $norepeat);
		// 			if (count($eventsThisDay))
		// 			{
		// 				$eventsByRelDay[$i] = $eventsThisDay;
		// 				$events += count($eventsByRelDay[$i]);
		// 			}
		// 			if ($events >= $maxEvents)
		// 			{
		// 				break;
		// 			}
		// 			$date = mktime(0, 0, 0, date('m', $date), date('d', $date) - 1, date('Y', $date));
		// 			$i--;
		// 		}
		// 	}
		// }

		// if (isset($eventsByRelDay) && count($eventsByRelDay))
		// {
		// 	// Now to display these events, we just start at the smallest index of the $eventsByRelDay array and work our way up.
		// 	ksort($eventsByRelDay, SORT_NUMERIC);
		// 	reset($eventsByRelDay);

		// 	$this->eventsByRelDay = $eventsByRelDay;
		// }
		// else
		// {
		// 	$this->eventsByRelDay = null;
		// }

		require $this->getLayoutPath();
	}

	/**
	 * This custom sort compare function compares the start times of events that are refernced by the a & b vars
	 *
	 * @param   object   &$a  Parameter description (if any) ...
	 * @param   object   &$b  Parameter description (if any) ...
	 * @return  integer  Return description (if any) ...
	 */
	public function cmpByStartTime(&$a, &$b)
	{
		list($date, $aStrtTime) = preg_split('# #', $a->publish_up);
		list($date, $bStrtTime) = preg_split('# #', $b->publish_up);
		if ($aStrtTime == $bStrtTime)
		{
			return 0;
		}
		return ($aStrtTime > $bStrtTime) ? -1 : 1;
	}

	/**
	 * The function below is essentially the 'ShowEventsByDate' function in the com_events module,
	 * except no actual output is performed.  Rather this function returns an array of references to
	 * $rows within the $rows (ie events) input array which occur on the input '$date'.  This
	 * is determined by the complicated com_event algorithm according to the event's repeatting type.
	 *
	 * @param   array    &$rows           Parameter description (if any) ...
	 * @param   unknown  $date            Parameter description (if any) ...
	 * @param   array    &$seenThisEvent  Parameter description (if any) ...
	 * @return  array    Return description (if any) ...
	 */
	private function _getEventsByDate(&$rows, $date, &$seenThisEvent)
	{
		$num_events = count($rows);
		$new_rows_events = array();

		if ($num_events > 0)
		{
			$year  = date('Y', $date);
			$month = date('m', $date);
			$day   = date('d', $date);

			for ($r = 0; $r < count($rows); $r++)
			{
				$row = $rows[$r];
				if (isset($seenThisEvent[$row->id]))
				{
					continue;
				}

				$seenThisEvent[$row->id] = 1;
				$new_rows_events[] =& $rows[$r];
			}

			usort($new_rows_events, array($this, 'cmpByStartTime'));
		}

		return $new_rows_events;
	}
}
