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

// include tables
require_once JPATH_ROOT . DS . 'components' . DS . 'com_events' . DS . 'tables' . DS . 'event.php';

class EventsModelEvent extends \Hubzero\Base\Model
{
	/**
	 * JTable
	 *
	 * @var string
	 */
	protected $_tbl = null;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'EventsEvent';

	/**
	 * Constructor
	 *
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct( $oid = null )
	{
		// create needed objects
		$this->_db = JFactory::getDBO();

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
		$group = Hubzero\User\Group::getInstance($this->get('scope_id'));
		return JRoute::_('index.php?option=com_groups&cn='.$group->get('cn').'&active=calendar&action=details&event_id='.$this->get('id'));
	}

	/**
	 * Returns calendar for event
	 *
	 * @return object
	 */
	public function calendar()
	{
		return EventsModelCalendar::getInstance($this->get('calendar_id'));
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
				$date = JFactory::getDate($matches[1]);
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
		$readable = JText::_('COM_EVENTS_REPEATS');

		// get parsed rule
		$rule = $this->parseRepeatingRule();

		// interval type type
		if ($rule['interval'] > 1)
		{
			$readable .= JText::sprintf('COM_EVENTS_REPEATS_EVERY', $rule['interval'], ucfirst(str_replace('ly', 's', $rule['freq'])));
		}
		else
		{
			$readable .= ucfirst($rule['freq']);
		}

		// handle end
		if ($rule['end'] == 'count')
		{
			$readable .= ' ' . JText::sprintf('COM_EVENTS_REPEATS_END_COUNT', $rule['count']);
		}
		else if ($rule['end'] == 'until')
		{
			$readable .= ' ' . JText::sprintf('COM_EVENTS_REPEATS_END_UNTIL', $rule['until']);
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
		$tzInfo = plgGroupsCalendarHelper::getTimezoneNameAndAbbreviation($this->get('time_zone'));
		$tzName = timezone_name_from_abbr($tzInfo['abbreviation']);

		// get publish up/down dates in UTC
		$publishUp   = new DateTime($this->get('publish_up'), new DateTimezone('UTC'));
		$publishDown = new DateTime($this->get('publish_down'), new DateTimezone('UTC'));

		// Set eastern timezone as publish up/down date timezones
		// since all event date/times are stores relative to eastern
		// ----------------------------------------------------------------------------------
		// The timezone param "DTSTART;TZID=" defined above will allow a users calendar app to
		// adjust date/time display according to that timezone and their systems timezone setting
		$publishUp->setTimezone( new DateTimezone(timezone_name_from_abbr('EST')) );
		$publishDown->setTimezone( new DateTimezone(timezone_name_from_abbr('EST')) );

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
		$first = JFactory::getDate(date('Y') . '-01-02 00:00:00')->toUnix();
		$last = JFactory::getDate(date('Y') . '-12-30 00:00:00')->toUnix();
		$transitions = $ttz->getTransitions($first, $last);
		$daylightStart = JFactory::getDate($transitions[1]['ts']);
		$daylightEnd = JFactory::getDate($transitions[2]['ts']);

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