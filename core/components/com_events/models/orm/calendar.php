<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models\Orm;

use Hubzero\Database\Relational;
use User;
use Date;

/**
 * Event Calendar model
 *
 * @uses \Hubzero\Database\Relational
 */
class Calendar extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'events';

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Defines a one to many relationship between calendar and events
	 *
	 * @return  object
	 */
	public function events()
	{
		return $this->oneToMany(__NAMESPACE__ . '\Event', 'calendar_id');
	}

	/**
	 * Subscribe to group calendars
	 *
	 * @param   string   $name
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  void
	 */
	public static function subscribe($name = 'Calendar Subscription', $scope = 'event', $scope_id = null)
	{
		// get request varse
		$calendarIds = \Request::getString('calendar_id', '', 'get');
		$calendarIds = array_map("intval", explode(',', $calendarIds));

		// array to hold events
		$events = array();

		// loop through and get each calendar
		foreach ($calendarIds as $k => $calendarId)
		{
			// load calendar model
			$eventsCalendar = self::one($calendarId);

			// make sure calendar is published
			if (!$eventsCalendar->get('published') && $calendarId != 0)
			{
				continue;
			}

			// get calendar events
			$rawEvents = $eventsCalendar->events()
				->whereEquals('scope', $scope)
				->whereEquals('scope_id', $scope_id)
				->whereIn('state', array(1))
				->rows();

			// merge with full events list
			foreach ($rawEvents as $rawEvent)
			{
				$events[] = $rawEvent;
			}
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
		$last  = Date::of(date('Y') . '-12-30 00:00:00')->toUnix();

		$transitions = $ttz->getTransitions($first, $last);
		$daylightStart = Date::of($transitions[1]['ts']);
		$daylightEnd   = Date::of($transitions[2]['ts']);

		// loop through events
		foreach ($events as $event)
		{
			$sequence = 0;
			$uid      = $event->get('id') . '@' . $_SERVER['HTTP_HOST'];
			$title    = $event->get('title');
			$content  = str_replace("\r\n", '\n', $event->get('content'));
			$location = $event->get('adresse_info');
			$url      = $event->get('extra_info');
			$allDay   = $event->get('allday');

			// get event timezone setting
			// use this in "DTSTART;TZID="
			$tzInfo = \plgGroupsCalendarHelper::getTimezoneNameAndAbbreviation($event->get('time_zone'));
			$tzName = timezone_name_from_abbr($tzInfo['abbreviation']);

			// get publish up/down dates in UTC
			$publishUp   = Date::of($event->get('publish_up'));
			$publishDown = Date::of($event->get('publish_down'));
			if ($allDay == '1')
			{
				$dtStart = 'DTSTART;VALUE=DATE:' . $publishUp->format('Ymd', true);
				$dtEnd   = 'DTEND;VALUE=DATE:' . $publishDown->format('Ymd', true);
			}
			else
			{
				$dtStart = 'DTSTART:' . $publishUp->format('Ymd\THis\Z');
				$dtEnd   = 'DTEND:' . $publishDown->format('Ymd\THis\Z');
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
