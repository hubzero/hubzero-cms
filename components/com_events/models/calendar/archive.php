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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// include calendar model
require_once JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'models' . DS . 'calendar.php';

class EventsModelCalendarArchive extends \Hubzero\Base\Model
{
	/**
	 * \Hubzero\Base\ItemList
	 * 
	 * @var object
	 */
	private $_calendars = null;

	/**
	 * Constructor
	 * 
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct()
	{
		// create needed objects
		$this->_db = JFactory::getDBO();
	}

	/**
	 * Get Instance this Model
	 *
	 * @param   $key   Instance Key
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances)) 
		{
			$instances = array();
		}

		if (!isset($instances[$key])) 
		{
			$instances[$key] = new self();
		}
		
		return $instances[$key];
	}

	/**
	 * Get a list of event calendars
	 *
	 * @param      string  $rtrn    What data to return
	 * @param      array   $filters Filters to apply to data retrieval
	 * @param      boolean $boolean Clear cached data?
	 * @return     mixed
	 */
	public function calendars( $rtrn = 'list', $filters = array(), $clear = false )
	{
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_calendars instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					$tbl = new EventsCalendar($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new EventsModelCalendar($result);
						}
					}
					$this->_calendars = new \Hubzero\Base\Model\ItemList($results);
				}
				return $this->_calendars;
			break;
		}
	}

	/**
	 * Subscribe to group calendars
	 * 
	 * @return void
	 */
	public function subscribe($name = 'Calendar Subscription', $scope = 'event', $scope_id = null)
	{
		// get request varse
		$calendarIds = JRequest::getVar('calendar_id','','get');
		$calendarIds = array_map("intval", explode(',', $calendarIds));
		
		// array to hold events
		$events = new \Hubzero\Base\ItemList();
		
		// loop through and get each calendar
		foreach ($calendarIds as $k => $calendarId)
		{
			// load calendar model
			$eventsCalendar = new EventsModelCalendar($calendarId);
			
			// make sure calendar is published
			if (!$eventsCalendar->get('published') && $calendarId != 0)
			{
				continue;
			}

			// get calendar events
			$rawEvents = $eventsCalendar->events('list', array(
				'scope'       => $scope,
				'scope_id'    => $scope_id,
				'calendar_id' => $calendarId,
				'state'       => array(1)
			));
			
			// merge with full events list
			$events = $events->merge($rawEvents);
		}

		//create output
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "PRODID:PHP\r\n";
		$output .= "METHOD:PUBLISH\r\n";
		$output .= "X-WR-CALNAME;VALUE=TEXT:" . $name . "\r\n";
		$output .= "X-PUBLISHED-TTL:PT15M\r\n";
		$output .= "X-ORIGINAL-URL:https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\r\n";
		$output .= "CALSCALE:GREGORIAN\r\n";

		// get daylight start and end
		$ttz = new DateTimezone(timezone_name_from_abbr('EST'));
		$first = JFactory::getDate(date('Y') . '-01-02 00:00:00')->toUnix();
		$last = JFactory::getDate(date('Y') . '-12-30 00:00:00')->toUnix();
		$transitions = $ttz->getTransitions($first, $last);
		$daylightStart = JFactory::getDate($transitions[1]['ts']);
		$daylightEnd = JFactory::getDate($transitions[2]['ts']);

		// output timezone block
		$output .= "BEGIN:VTIMEZONE\r\n";
		$output .= "TZID:America/New_York\r\n";
		$output .= "X-LIC-LOCATION:America/New_York\r\n";
		$output .= "BEGIN:DAYLIGHT\r\n";
		$output .= "TZNAME:Daylight\r\n";
		$output .= "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3\r\n";
		$output .= "TZOFFSETFROM:-0500\r\n";
		$output .= "TZOFFSETTO:-0400\r\n";
		$output .= "DTSTART:" . $daylightStart->format('Ymd\THis') . "\r\n";
		$output .= "END:DAYLIGHT\r\n";
		$output .= "BEGIN:STANDARD\r\n";
		$output .= "TZNAME:Standard\r\n";
		$output .= "RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11\r\n";
		$output .= "TZOFFSETFROM:-0400\r\n";
		$output .= "TZOFFSETTO:-0500\r\n";
		$output .= "DTSTART:" . $daylightEnd->format('Ymd\THis') . "\r\n";
		$output .= "END:STANDARD\r\n";
		$output .= "END:VTIMEZONE\r\n";
		
		// loop through events
		foreach ($events as $event)
		{
			$sequence = 0;
			$uid      = $event->get('id');
			$title    = $event->get('title');
			$content  = str_replace("\r\n", '\n', $event->get('content'));
			$location = $event->get('adresse_info');
			$url      = $event->get('extra_info');

			// get event timezone setting
			// use this in "DTSTART;TZID=" 
			$tzInfo = plgGroupsCalendarHelper::getTimezoneNameAndAbbreviation($event->get('time_zone'));
			$tzName = timezone_name_from_abbr($tzInfo['abbreviation']);
		
			// get publish up/down dates in UTC
			$publishUp   = new DateTime($event->get('publish_up'), new DateTimezone('UTC'));
			$publishDown = new DateTime($event->get('publish_down'), new DateTimezone('UTC'));
		
			// Set eastern timezone as publish up/down date timezones
			// since all event date/times are stores relative to eastern 
			// ----------------------------------------------------------------------------------
			// The timezone param "DTSTART;TZID=" defined above will allow a users calendar app to 
			// adjust date/time display according to that timezone and their systems timezone setting
			$publishUp->setTimezone( new DateTimezone(timezone_name_from_abbr('EST')) );
			$publishDown->setTimezone( new DateTimezone(timezone_name_from_abbr('EST')) );
			
			// create now, created, and modified vars
			$now      = gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
			$created  = gmdate('Ymd', strtotime($event->get('created'))) . 'T' . gmdate('His', strtotime($event->get('created'))) . 'Z';
			$modified = gmdate('Ymd', strtotime($event->get('modified'))) . 'T' . gmdate('His', strtotime($event->get('modified'))) . 'Z';
			
			// start output
			$output .= "BEGIN:VEVENT\r\n";
			$output .= "UID:{$uid}\r\n";
			$output .= "SEQUENCE:{$sequence}\r\n";
			$output .= "DTSTAMP:{$now}\r\n";
			$output .= "DTSTART;TZID={$tzName}:" . $publishUp->format('Ymd\THis') . "\r\n";
			if($event->get('publish_down') != '' && $event->get('publish_down') != '0000-00-00 00:00:00')
			{
				$output .= "DTEND;TZID={$tzName}:" . $publishDown->format('Ymd\THis') . "\r\n";
			}
			else
			{
				$output .= "DTEND;TZID={$tzName}:" . $publishUp->format('Ymd\THis') . "\r\n";
			}

			// repeating rule
			if ($event->get('repeating_rule') != '')
			{
				$output .= "RRULE:" . $event->get('repeating_rule') . "\r\n";
			}

			$output .= "CREATED:{$created}\r\n";
			$output .= "LAST-MODIFIED:{$modified}\r\n";
			$output .= "SUMMARY:{$title}\r\n";
			$output .= "DESCRIPTION:{$content}\r\n";
			// do we have extra info
			if($url != '' && filter_var($url, FILTER_VALIDATE_URL))
			{
				$output .= "URL;VALUE=URI:{$url}\r\n";
			}
			// do we have a location
			if ($location != '')
			{
				$output .= "LOCATION:{$location}\r\n";
			}
			$output .= "END:VEVENT\r\n";
		}

		// close calendar
		$output .= "END:VCALENDAR";
		
		// set headers and output
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="'. $name .'.ics"');
		header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT');
		echo $output;
		exit();
	}
}