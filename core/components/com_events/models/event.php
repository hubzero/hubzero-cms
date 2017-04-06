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

namespace Components\Events\Models;

use Hubzero\Base\Model;
use Hubzero\User\Group;
use DateTimezone;
use DateTime;
use Route;
use Lang;
use Date;
// include tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'event.php';
require_once Component::path('com_events') . DS . 'models' . DS . 'eventdate.php';
/**
 * Event model
 */
class Event extends Model
{
	/**
	 * Table
	 *
	 * @var string
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Events\\Tables\\Event';

	/**
	 * Constructor
	 *
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct($oid = null)
	{
		// create needed objects
		$this->_db = \App::get('db');

		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);

		// load object
		if (is_numeric($oid))
		{
			$this->_tbl->load( $oid );
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
		}
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
			$instances[$key] = new self($key);
		}

		return $instances[$key];
	}

	/**
	 * Return link to event
	 *
	 * @return string
	 */
	public function link()
	{
		$group = Group::getInstance($this->get('scope_id'));
		return Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=calendar&action=details&event_id=' . $this->get('id'));
	}

	/**
	 * Returns calendar for event
	 *
	 * @return object
	 */
	public function calendar()
	{
		return Calendar::getInstance($this->get('calendar_id'));
	}

	/**
	 * Parses the Events Repeating Rule
	 *
	 * @return array
	 */
	public function parseRepeatingRule()
	{
		// array to hold final values
		$repeating = array(
			'freq'     => '',
			'interval' => '',
			'end'      => 'never',
			'count'    => '',
			'until'    => ''
		);

		// split rules
		$parts = array_map('trim', explode(';', $this->get('repeating_rule')));

		// loop through each part
		foreach ($parts as $k => $part)
		{
			// freq
			if (preg_match('/FREQ=([A-Z]*)/u', $part, $matches))
			{
				$repeating['freq'] = strtolower($matches[1]);
				unset($parts[$k]);
			}

			// interval
			if (preg_match('/INTERVAL=([0-9]{1,2})/u', $part, $matches))
			{
				$repeating['interval'] = strtolower($matches[1]);
				unset($parts[$k]);
			}

			// count
			if (preg_match('/COUNT=([0-9]{1,2})/u', $part, $matches))
			{
				$repeating['count'] = strtolower($matches[1]);
				$repeating['end']   = 'count';
				unset($parts[$k]);
			}

			// until
			if (preg_match('/UNTIL=(.*)/u', $part, $matches))
			{
				$date = Date::of($matches[1]);
				$repeating['until'] = $date->format('m/d/Y');
				$repeating['end']   = 'until';
				unset($parts[$k]);
			}
		}

		return $repeating;
	}

	/**
	 * Generate Human Readable Repeating Info
	 *
	 * @return [type] [description]
	 */
	public function humanReadableRepeatingRule()
	{
		// reable repeating rule
		$readable = Lang::txt('COM_EVENTS_REPEATS');

		// get parsed rule
		$rule = $this->parseRepeatingRule();

		// interval type type
		if ($rule['interval'] > 1)
		{
			$readable .= Lang::txt('COM_EVENTS_REPEATS_EVERY', $rule['interval'], ucfirst(str_replace('ly', 's', $rule['freq'])));
		}
		else
		{
			$readable .= ucfirst($rule['freq']);
		}

		// handle end
		if ($rule['end'] == 'count')
		{
			$readable .= ' ' . Lang::txt('COM_EVENTS_REPEATS_END_COUNT', $rule['count']);
		}
		else if ($rule['end'] == 'until')
		{
			$readable .= ' ' . Lang::txt('COM_EVENTS_REPEATS_END_UNTIL', $rule['until']);
		}

		// return
		return $readable;
	}

	/**
	 * Export Event in iCal Format
	 *
	 * @return [type] [description]
	 */
	public function export()
	{
		// get event timezone setting
		// use this in "DTSTART;TZID="
		$tzInfo = \plgGroupsCalendarHelper::getTimezoneNameAndAbbreviation($this->get('time_zone'));
		$tzName = timezone_name_from_abbr($tzInfo['abbreviation']);

		// get publish up/down dates in UTC
		$publishUp   = new DateTime($this->get('publish_up'), new DateTimezone('UTC'));
		$publishDown = new DateTime($this->get('publish_down'), new DateTimezone('UTC'));

		// Set eastern timezone as publish up/down date timezones
		// since all event date/times are stores relative to eastern
		// ----------------------------------------------------------------------------------
		// The timezone param "DTSTART;TZID=" defined above will allow a users calendar app to
		// adjust date/time display according to that timezone and their systems timezone setting
		$publishUp->setTimezone(new DateTimezone(timezone_name_from_abbr('EST')));
		$publishDown->setTimezone(new DateTimezone(timezone_name_from_abbr('EST')));

		//event vars
		$id       = $this->get('id');
		$title    = $this->get('title');
		$desc     = str_replace("\n", '\n', $this->get('content'));
		$url      = $this->get('extra_info');
		$location = $this->get('adresse_info');
		$now      = gmdate('Ymd') . 'T' . gmdate('His') . 'Z';
		$created  = gmdate('Ymd', strtotime($this->get('created'))) . 'T' . gmdate('His', strtotime($this->get('created'))) . 'Z';
		$modified = gmdate('Ymd', strtotime($this->get('modified'))) . 'T' . gmdate('His', strtotime($this->get('modified'))) . 'Z';

		//create ouput
		$output  = "BEGIN:VCALENDAR\r\n";
		$output .= "VERSION:2.0\r\n";
		$output .= "PRODID:PHP\r\n";
		$output .= "METHOD:PUBLISH\r\n";

		// get daylight start and end
		$ttz = new DateTimezone(timezone_name_from_abbr('EST'));
		$first = Date::of(date('Y') . '-01-02 00:00:00')->toUnix();
		$last = Date::of(date('Y') . '-12-30 00:00:00')->toUnix();
		$transitions = $ttz->getTransitions($first, $last);
		$daylightStart = Date::of($transitions[1]['ts']);
		$daylightEnd = Date::of($transitions[2]['ts']);

		// output timezone block
		$output .= "BEGIN:VTIMEZONE\r\n";
		$output .= "TZID:{$tzName}\r\n";
		$output .= "X-LIC-LOCATION:{$tzName}\r\n";
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

		// ouput event info
		$output .= "BEGIN:VEVENT\r\n";
		$output .= "UID:{$id}\r\n";
		$output .= "DTSTAMP:{$now}\r\n";
		$output .= "DTSTART;TZID={$tzName}:" . $publishUp->format('Ymd\THis') . "\r\n";
		if ($this->get('publish_down') != '' && $this->get('publish_down') != '0000-00-00 00:00:00')
		{
			$output .= "DTEND;TZID={$tzName}:" . $publishDown->format('Ymd\THis') . "\r\n";
		}
		else
		{
			$output .= "DTEND;TZID={$tzName}:" . $publishUp->format('Ymd\THis') . "\r\n";
		}

		// repeating rule
		if ($this->get('repeating_rule') != '')
		{
			$output .= "RRULE:" . $this->get('repeating_rule') . "\r\n";
		}

		$output .= "CREATED:{$created}\r\n";
		$output .= "LAST-MODIFIED:{$modified}\r\n";
		$output .= "SUMMARY:{$title}\r\n";
		$output .= "DESCRIPTION:{$desc}\r\n";
		if ($url != '' && filter_var($url, FILTER_VALIDATE_URL))
		{
			$output .= "URL;VALUE=URI:{$url}\r\n";
		}
		if ($location != '')
		{
			$output .= "LOCATION:{$location}\r\n";
		}
		$output .= "END:VEVENT\r\n";
		$output .= "END:VCALENDAR\r\n";

		//set the headers for output
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', strtolower($title)) . '_export.ics');
		echo $output;
		exit();
	}
}
