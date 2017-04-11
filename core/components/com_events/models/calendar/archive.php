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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models\Calendar;

use Components\Events\Models\Calendar;
use Components\Events\Tables;
use Hubzero\Base\Model\ItemList;
use Hubzero\Base\Model;
use DateTimezone;
use DateTime;
use Date;

// include calendar model
require_once dirname(__DIR__) . DS . 'calendar.php';

/**
 * Calendar archive model
 */
class Archive extends Model
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
		$this->_db = \App::get('db');
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
				if (!($this->_calendars instanceof ItemList) || $clear)
				{
					$tbl = new Tables\Calendar($this->_db);
					if ($results = $tbl->find( $filters ))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new Calendar($result);
						}
					}
					$this->_calendars = new ItemList($results);
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
		$calendarIds = Request::getVar('calendar_id', '', 'get');
		$calendarIds = array_map("intval", explode(',', $calendarIds));

		// array to hold events
		$events = new ItemList();

		// loop through and get each calendar
		foreach ($calendarIds as $k => $calendarId)
		{
			// load calendar model
			$eventsCalendar = new Calendar($calendarId);

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
		$first = Date::of(date('Y') . '-01-02 00:00:00')->toUnix();
		$last = Date::of(date('Y') . '-12-30 00:00:00')->toUnix();
		$transitions = $ttz->getTransitions($first, $last);
		$daylightStart = Date::of($transitions[1]['ts']);
		$daylightEnd = Date::of($transitions[2]['ts']);

		// loop through events
		foreach ($events as $event)
		{
			$sequence = 0;
			$uid      = $event->get('id') . '@' . $_SERVER['HTTP_HOST'];
			$title    = $event->get('title');
			$content  = str_replace("\r\n", '\n', $event->get('content'));
			$location = $event->get('adresse_info');
			$url  	  = $event->get('extra_info');
			$allDay   = $event->get('allday');

			// get event timezone setting
			// use this in "DTSTART;TZID="
			$tzInfo = \plgGroupsCalendarHelper::getTimezoneNameAndAbbreviation($event->get('time_zone'));
			$tzName = timezone_name_from_abbr($tzInfo['abbreviation']);

			// get publish up/down dates in UTC
			$publishUp = Date::of($event->get('publish_up'));
			$publishDown = Date::of($event->get('publish_down'));
			if ($allDay == "1")
			{
				$dtStart = 'DTSTART;VALUE=DATE:' . $publishUp->format('Ymd', true);
				$dtEnd = 'DTEND;VALUE=DATE:' . $publishDown->format('Ymd', true);
			}
			else
			{
				$dtStart = 'DTSTART:' . $publishUp->format('Ymd\THis\Z');
				$dtEnd = 'DTEND:' . $publishDown->format('Ymd\THis\Z');
			}

			/* 2017-04-11 Patrick: This is actually no longer true, therefore the best course of action would be to add the UTC datetime (adding a Z as a suffix)
			// Set eastern timezone as publish up/down date timezones
			// since all event date/times are stores relative to eastern
			// ----------------------------------------------------------------------------------
			// The timezone param "DTSTART;TZID=" defined above will allow a users calendar app to
			// adjust date/time display according to that timezone and their systems timezone setting
			$publishUp->setTimezone(new DateTimezone(timezone_name_from_abbr('EST')));
			$publishDown->setTimezone(new DateTimezone(timezone_name_from_abbr('EST')));
			*/
			// create now, created, and modified vars
			$now      = gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
			$created  = gmdate('Ymd', strtotime($event->get('created'))) . 'T' . gmdate('His', strtotime($event->get('created'))) . 'Z';
			$modified = gmdate('Ymd', strtotime($event->get('modified'))) . 'T' . gmdate('His', strtotime($event->get('modified'))) . 'Z';

			// start output
			$output .= "BEGIN:VEVENT\r\n";
			$output .= "UID:{$uid}\r\n";
			$output .= "SEQUENCE:{$sequence}\r\n";
			$output .= "DTSTAMP:{$now}\r\n";
			$output .= $dtStart  . "\r\n";
			if ($event->get('publish_down') != '' && $event->get('publish_down') != '0000-00-00 00:00:00')
			{
				$output .= $dtEnd . "\r\n";
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
			if ($url != '' && filter_var($url, FILTER_VALIDATE_URL))
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
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		echo $output;
		exit();
	}
}
