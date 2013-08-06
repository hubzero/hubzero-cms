<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

// Parameters:
// ===========
//
// maxEvents = max. no. of events to display in the module (1 to 10, default is 5)
//
// mode:
// = 0  (default) display events for current week and following week only up to 'maxEvents'.
//
// = 1  same as 'mode'=0 except some past events for the current week will also be
//      displayed if num of future events is less than $maxEvents.
//
// = 2  display events for +'days' range relative to current day up to $maxEvents.
//
// = 3  same as mode 2 except if there are < 'maxEvents' in the range,
//      then display past events within -'days' range.
//
// = 4  display events for current month up to 'maxEvents'.
//
// days: (default=7) range of days relative to current day to display events for mode 1 or 3.
//
// displayLinks = 1 (default is 0) display event titles as links to the 'view_detail' com_events
//                   task which will display details of the event.
//
// displayYear = 1 (default is 0) display year when displaying dates in the non-customized event's listing.

/**
 * Short description for 'modEventsLatest'
 * 
 * Long description (if any) ...
 */
class modEventsLatest
{

	/**
	 * Description for '_attributes'
	 * 
	 * @var array
	 */
	private $_attributes = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @param      unknown $module Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_attributes[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_attributes[$property]))
		{
			return $this->_attributes[$property];
		}
	}

	/**
	 * Short description for '__isset'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function display()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet($this->module->module);

		$juser =& JFactory::getUser();

		if (!$juser->get('guest') && intval($this->params->get('cache', 0)))
		{
			$cache =& JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->params->get('cache_time', 900)));
			$cache->call(array($this, 'run'));
			echo '<!-- cached ' . date('Y-m-d H:i:s', time()) . ' -->';
			return;
		}

		$this->run();
	}

	/**
	 * Short description for 'run'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function run()
	{
		// Check the events component
		if (file_exists(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php'))
		{
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'html.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'date.php');
			include_once(JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'helpers' . DS . 'repeat.php');
		}
		else
		{
			$this->error = JText::_('MOD_EVENTS_LATEST_COMPONENT_REQUIRED');
			return;
		}

		$this->_displayLatestEvents();
	}

	//-----------
	// This custom sort compare function compares the start times of events that are refernced by the a & b vars

	/**
	 * Short description for 'cmpByStartTime'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object &$a Parameter description (if any) ...
	 * @param      object &$b Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
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

	//-----------
	// The function below is essentially the 'ShowEventsByDate' function in the com_events module,
	// except no actual output is performed.  Rather this function returns an array of references to
	// $rows within the $rows (ie events) input array which occur on the input '$date'.  This
	// is determined by the complicated com_event algorithm according to the event's repeatting type.

	/**
	 * Short description for '_getEventsByDate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array &$rows Parameter description (if any) ...
	 * @param      unknown $date Parameter description (if any) ...
	 * @param      array &$seenThisEvent Parameter description (if any) ...
	 * @param      unknown $noRepeats Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	private function _getEventsByDate(&$rows, $date, &$seenThisEvent, $noRepeats)
	{
    	$num_events = count($rows);
    	$new_rows_events = array();

		$eventCheck = new EventsRepeat;

		if ($num_events > 0)
		{
			$year  = date('Y', $date);
	    	$month = date('m', $date);
	    	$day   = date('d', $date);

			for ($r = 0; $r < count($rows); $r++)
			{
				$row = $rows[$r];
				if (isset($seenThisEvent[$row->id]) && $noRepeats)
				{
					continue;
				}
				if ($eventCheck->EventsRepeat($row, $year, $month, $day))
				{
					$seenThisEvent[$row->id] = 1;
					$new_rows_events[] =& $rows[$r];
				}
			}

			usort($new_rows_events, array('modEventsLatest','cmpByStartTime'));
		}

		return $new_rows_events;
	}

	/**
	 * Short description for '_displayLatestEvents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function _displayLatestEvents()
	{
		$database =& JFactory::getDBO();

		// Get the user GID (used in some queries)
		$juser =& JFactory::getUser();
		//$gid = $juser->get('gid');

		// Get the site language setting
		$lang =& JFactory::getLanguage();

		// Get module parameters
		$mode              = $this->params->get('mode')                ? abs(intval($this->params->get('mode'))) : 4;
		$days              = $this->params->get('days')                ? abs(intval($this->params->get('days'))) : 7;
		$maxEvents         = $this->params->get('max_events')          ? abs(intval($this->params->get('max_events'))) : 5;
		$displayLinks      = $this->params->get('display_links')       ? abs(intval($this->params->get('display_links'))) : 0;
		$displayYear       = $this->params->get('display_year')        ? abs(intval($this->params->get('display_year'))) : 0;
		$disableTitleStyle = $this->params->get('display_title_style') ? abs(intval($this->params->get('display_title_style'))) : 0;
		$disableDateStyle  = $this->params->get('display_date_style')  ? abs(intval($this->params->get('display_date_style'))) : 0;
		$customFormatStr   = $this->params->get('custom_format_str')   ? $this->params->get('custom_format_str') : NULL;
		$norepeat          = $this->params->get('no_repeat')           ? abs(intval($this->params->get('no_repeat'))) : 0;
		$charlimit         = $this->params->get('char_limit')          ? abs(intval($this->params->get('char_limit'))) : 150;
		$announcements     = $this->params->get('announcements')       ? abs(intval($this->params->get('announcements'))) : 0;

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
	/**
	 * Description for ''_CAL_CONF_STARDAY''
	 */
			define('_CAL_CONF_STARDAY',$startday);
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
				//$endDate = date('Y-m-d', mktime(0,0,0,date('m')+1,0, date('Y')))." 23:59:59";
				// end of this year
				$endDate = date('Y-m-d', mktime(0, 0, 0, date('m'), 0, date('Y') + 1)) . ' 23:59:59';
				break;
		}

		switch ($announcements)
		{
			case 2:
				$ancmnt = "AND #__events.announcement='1'";
			break;
			case 1:
				$ancmnt = "AND #__events.announcement!='1'";
			break;
			case 0:
			default:
				$ancmnt = "";
			break;
		}

		// Display only events that are not announcements
		$query = "SELECT #__events.* FROM #__events, #__categories as b"
			. "\nWHERE #__events.catid = b.id " // AND b.access <= $gid AND #__events.access <= $gid 
                        . "\n   AND (#__events.state='1' $ancmnt AND #__events.checked_out='0')"
			. "\n	AND ((publish_up <= '$todayBegin%' AND publish_down >= '$todayBegin%')"
			. "\n	OR (publish_up <= '$endDate%' AND publish_down >= '$endDate%')"
			. "\n   OR (publish_up <= '$endDate%' AND publish_up >= '$todayBegin%')"
			. "\n   OR (publish_down <= '$endDate%' AND publish_down >= '$todayBegin%'))"
			. "\n   AND (#__events.scope IS NULL OR #__events.scope=" . $database->quote('event') . ")"
			. "\nORDER BY publish_up ASC";
		
		// Retrieve the list of returned records as an array of objects
		$database->setQuery($query);
		$rows = $database->loadObjectList();

		// Determine the events that occur each day within our range
		$events = 0;
		$date = mktime(0, 0, 0);
		$lastDate = mktime(0, 0, 0, intval(substr($endDate, 5, 2)), intval(substr($endDate, 8, 2)), intval(substr($endDate, 0, 4)));
		$i = 0;

		$content  = '';
		$seenThisEvent = array();

		if (count($rows))
		{
			while ($date <= $lastDate)
			{
				// Get the events for this $date
				$eventsThisDay = $this->_getEventsByDate($rows, $date, $seenThisEvent, $norepeat);
				if (count($eventsThisDay))
				{
					// dmcd May 7/04  bug fix to not exceed maxEvents
					$eventsToAdd = min($maxEvents-$events, count($eventsThisDay));
					$eventsThisDay = array_slice($eventsThisDay, 0, $eventsToAdd);
					$eventsByRelDay[$i] = $eventsThisDay;
					$events += count($eventsByRelDay[$i]);
				}
				if ($events >= $maxEvents)
				{
					break;
				}
				$date = mktime(0, 0, 0, date('m', $date), date('d', $date) + 1, date('Y', $date));
				$i++;
			}
		}

		// Do we actually have any events to display?
		if ($events < $maxEvents && ($mode==1 || $mode==3))
		{
			// Display some recent previous events too up to a total of $maxEvents
			// Changed by Swaroop to display only events that are not announcements
			$query = "SELECT #__events.* FROM #__events, #__categories as b"
				. "\nWHERE #__events.catid = b.id AND b.access <= $gid AND #__events.access <= $gid AND (#__events.state='1' $ancmnt AND #__events.checked_out='0')"
				. "\n	AND ((publish_up <= '$beginDate%' AND publish_down >= '$beginDate%')"
				. "\n	OR (publish_up <= '$yesterdayEnd%' AND publish_down >= '$yesterdayEnd%')"
				. "\n   OR (publish_up <= '$yesterdayEnd%' AND publish_up >= '$beginDate%')"
				. "\n   OR (publish_down <= '$yesterdayEnd%' AND publish_down >= '$beginDate%'))"
				. "\n   AND (#__events.scope IS NULL OR #__events.scope=" . $database->quote('event') . ")"
				. "\n  ORDER BY publish_up DESC";

			// Initialise the query in the $database connector
			// This translates the '#__' prefix into the real database prefix
			$database->setQuery($query);

			// Retrieve the list of returned records as an array of objects
			$prows = $database->loadObjectList();

			if (count($prows))
			{
				// Start from yesterday
				$date = mktime(23, 59, 59, date('m'), date('d') - 1, date('Y'));
				$lastDate = mktime(0, 0, 0, intval(substr($beginDate, 5, 2)), intval(substr($beginDate, 8, 2)), intval(substr($beginDate, 0, 4)));
				$i = -1;

				while ($date >= $lastDate)
				{
					// Get the events for this $date
					$eventsThisDay = $this->_getEventsByDate($prows, $date, $seenThisEvent, $norepeat);
					if (count($eventsThisDay))
					{
						$eventsByRelDay[$i] = $eventsThisDay;
						$events += count($eventsByRelDay[$i]);
					}
					if ($events >= $maxEvents)
					{
						break;
					}
					$date = mktime(0, 0, 0, date('m', $date), date('d', $date) - 1, date('Y', $date));
					$i--;
				}
			}
		}

		if (isset($eventsByRelDay) && count($eventsByRelDay))
		{
			// Now to display these events, we just start at the smallest index of the $eventsByRelDay array and work our way up.
			ksort($eventsByRelDay, SORT_NUMERIC);
			reset($eventsByRelDay);

			$this->eventsByRelDay = $eventsByRelDay;
		}
		else
		{
			$this->eventsByRelDay = null;
		}

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}

