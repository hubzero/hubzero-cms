<?php

class iCalReader
{
	public $todo_count = 0;
	public $event_count = 0;
	public $cal;
	private $_lastKeyWord;

	public function __construct($filename)
	{
		//make sure we have a file
		if (!$filename)
		{
			return false;
		}

		//get array of lines
		$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$lines = $this->fixNewLines( $lines );

		//make sure this is a valid calendar file
		if (stristr($lines[0], 'BEGIN:VCALENDAR') === false)
		{
			return false;
		}

		//loop through each line
		foreach ($lines as $line)
		{
			$line = trim($line);
			$add  = $this->keyValueFromString($line);

			if ($add === false)
			{
				$this->addCalendarComponentWithKeyAndValue($type, false, $line);
				continue;
			}

			list($keyword, $value) = $add;

			switch ($line)
			{
				case "BEGIN:VTODO":
					$this->todo_count++;
					$type = "VTODO";
				break;

				case "BEGIN:VEVENT":
					$this->event_count++;
					$type = "VEVENT";
				break;

				case "BEGIN:VCALENDAR":
				case "BEGIN:DAYLIGHT":
				case "BEGIN:VTIMEZONE":
				case "BEGIN:STANDARD":
					$type = $value;
				break;

				case "END:VTODO":
				case "END:VEVENT":
				case "END:VCALENDAR":
				case "END:DAYLIGHT":
				case "END:VTIMEZONE":
				case "END:STANDARD":
					$type = "VCALENDAR";
				break;

				default:
					$this->addCalendarComponentWithKeyAndValue($type, $keyword, $value);
				break;
			}
		}

		return $this->cal;
	}

	public function fixNewLines( $lines )
	{
		foreach ($lines as $k => $line)
		{
			$firstChar = substr($line, 0, 1);
			if ($firstChar == ' ')
			{
				$numBack = 1;
				while($lines[$k-$numBack] == '')
				{
					$numBack++;
				}
				$appendTo = $k - $numBack;
				$lines[$appendTo] .= trim($line);
				$lines[$k] = '';
			}
		}

		return array_values(array_filter($lines));
	}


	public function keyValueFromString( $text )
	{
		preg_match("/([^:]+)[:]([\w\W]*)/", $text, $matches);
		if (count($matches) == 0)
		{
			return false;
		}
		$matches = array_splice($matches, 1, 2);
		return $matches;
	}

	public function addCalendarComponentWithKeyAndValue( $component, $keyword, $value )
	{
		if ($keyword == false)
		{
			$keyword = $this->last_keyword;
			switch ($component)
			{
				case 'VEVENT':    $value = $this->cal[$component][$this->event_count - 1][$keyword].$value;    break;
				case 'VTODO' :    $value = $this->cal[$component][$this->todo_count - 1][$keyword].$value;     break;
			}
		}

		if (stristr($keyword, "DTSTART") or stristr($keyword, "DTEND"))
		{
			$keyword = explode(";", $keyword);
			$keyword = $keyword[0];
		}

		switch ($component)
		{
			case "VTODO":     $this->cal[$component][$this->todo_count - 1][$keyword] = $value;     break;
			case "VEVENT":    $this->cal[$component][$this->event_count - 1][$keyword] = $value;    break;
			default:          $this->cal[$component][$keyword] = $value;                            break;
		}

		$this->last_keyword = $keyword;
	}

	public function iCalDateToUnixTimestamp( $icalDate )
	{
		$icalDate = str_replace('T', '', $icalDate);
		$icalDate = str_replace('Z', '', $icalDate);

		$pattern  = '/([0-9]{4})';   // 1: YYYY
		$pattern .= '([0-9]{2})';    // 2: MM
		$pattern .= '([0-9]{2})';    // 3: DD
		$pattern .= '([0-9]{0,2})';  // 4: HH
		$pattern .= '([0-9]{0,2})';  // 5: MM
		$pattern .= '([0-9]{0,2})/'; // 6: SS
		preg_match($pattern, $icalDate, $date);

		// Unix timestamp can't represent dates before 1970
		if ($date[1] <= 1970)
		{
			return false;
		}

		// Unix timestamps after 03:14:07 UTC 2038-01-19 might cause an overflow
		// if 32 bit integers are used.
		$timestamp = mktime((int)$date[4], (int)$date[5], (int)$date[6], (int)$date[2], (int)$date[3], (int)$date[1]);

		//return time
		return  $timestamp;
	}

	/**
	 * Handle All Day Events
	 *
	 */
	public function handleAllDayEvents( $start, $end )
	{
		$formattedStartTime          = date('H:i:s', $start);
		$formattedStartDate          = date("Y-m-d H:i:s", $start);
		$formattedEndDateMinusOneDay = date("Y-m-d H:i:s", strtotime('-1 DAY', $end));

		//set end time to null if this is all day event
		if ($formattedStartDate == $formattedEndDateMinusOneDay && $formattedStartTime == '00:00:00')
		{
			$end = 0;
		}

		//return start and end datetimes
		return array( $start, $end );
	}

	/**
	 * Handle Timezone offset since events are returned in UTC time
	 *
	 */
	public function handleTimezoneOffset( $start, $end )
	{
		//get current timezone
		$timezone = date('O');
		$modifier = substr($timezone, 0, 1);
		$offset   = trim(substr($timezone, 1), 0);

		//if we have a valid start date
		if (date_parse($start) && $start != 0 && date('H:i:s', $start) != '00:00:00')
		{
			$start = strtotime($modifier.$offset.'HOURS', $start);
		}

		//if we have a valid end date
		if (date_parse($end) && $end != 0)
		{
			$end = strtotime($modifier.$offset.'HOURS', $end);
		}

		//format dates before return
		$start = date("Y-m-d H:i:s", $start);
		$end   = ($end != 0) ? date("Y-m-d H:i:s", $end) : '0000-00-00 00:00:00';

		//return start and end datetimes
		return array( $start, $end );
	}

	public function events()
	{
		$array = $this->cal;
		return $array['VEVENT'];
	}

	public function firstEvent()
	{
		$array = $this->cal;
		return $array['VEVENT'][0];
	}

	public function hasEvents()
	{
		return ( count($this->events()) > 0 ? true : false );
	}

	public function eventsFromRange( $rangeStart = false, $rangeEnd = false )
	{
		$events = $this->sortEventsWithOrder($this->events(), SORT_ASC);

		if (!$events)
		{
			return false;
		}

		$extendedEvents = array();

		if ($rangeStart !== false)
		{
			$rangeStart = new DateTime();
		}

		if ($rangeEnd !== false or $rangeEnd <= 0)
		{
			$rangeEnd = new DateTime('2038/01/18');
		}
		else
		{
			$rangeEnd = new DateTime($rangeEnd);
		}

		$rangeStart = $rangeStart->format('U');
		$rangeEnd   = $rangeEnd->format('U');

		// loop through all events by adding two new elements
		foreach ($events as $anEvent)
		{
			$timestamp = $this->iCalDateToUnixTimestamp($anEvent['DTSTART']);
			if ($timestamp >= $rangeStart && $timestamp <= $rangeEnd)
			{
				$extendedEvents[] = $anEvent;
			}
		}

		return $extendedEvents;
	}

	public function sortEventsWithOrder( $events, $sortOrder = SORT_ASC )
	{
		$extendedEvents = array();

		// loop through all events by adding two new elements
		foreach ($events as $anEvent)
		{
			if (!array_key_exists('UNIX_TIMESTAMP', $anEvent))
			{
				$anEvent['UNIX_TIMESTAMP'] = $this->iCalDateToUnixTimestamp($anEvent['DTSTART']);
			}

			if (!array_key_exists('REAL_DATETIME', $anEvent))
			{
				$anEvent['REAL_DATETIME'] = date("d.m.Y", $anEvent['UNIX_TIMESTAMP']);
			}

			$extendedEvents[] = $anEvent;
		}

		foreach ($extendedEvents as $key => $value)
		{
			$timestamp[$key] = $value['UNIX_TIMESTAMP'];
		}
		array_multisort($timestamp, $sortOrder, $extendedEvents);

		return $extendedEvents;
	}
}