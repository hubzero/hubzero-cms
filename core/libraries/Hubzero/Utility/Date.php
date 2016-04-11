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

namespace Hubzero\Utility;

use DateTimeZone;
use DateTime;

/**
 * Date is a class that stores a date and provides logic to manipulate
 * and render that date in a variety of formats.
 *
 * @since  2.0
 */
class Date extends DateTime
{
	const DAY_ABBR = "\x021\x03";
	const DAY_NAME = "\x022\x03";
	const MONTH_ABBR = "\x023\x03";
	const MONTH_NAME = "\x024\x03";

	/**
	 * The format string to be applied when using the __toString() magic method.
	 *
	 * @var  string
	 */
	public static $format = 'Y-m-d H:i:s';

	/**
	 * Placeholder for a DateTimeZone object with GMT as the time zone.
	 *
	 * @var  object
	 */
	protected static $gmt;

	/**
	 * Placeholder for a DateTimeZone object with the default server
	 * time zone as the time zone.
	 *
	 * @var  object
	 */
	protected static $stz;

	/**
	 * An array of offsets and time zone strings representing the available
	 * options from prior CMS versions.
	 *
	 * @deprecated    12.1
	 * @var  array
	 */
	protected static $offsets = array(
		'-12' => 'Etc/GMT-12', '-11' => 'Pacific/Midway', '-10' => 'Pacific/Honolulu', '-9.5' => 'Pacific/Marquesas',
		'-9' => 'US/Alaska', '-8' => 'US/Pacific', '-7' => 'US/Mountain', '-6' => 'US/Central', '-5' => 'US/Eastern', '-4.5' => 'America/Caracas',
		'-4' => 'America/Barbados', '-3.5' => 'Canada/Newfoundland', '-3' => 'America/Buenos_Aires', '-2' => 'Atlantic/South_Georgia',
		'-1' => 'Atlantic/Azores', '0' => 'Europe/London', '1' => 'Europe/Amsterdam', '2' => 'Europe/Istanbul', '3' => 'Asia/Riyadh',
		'3.5' => 'Asia/Tehran', '4' => 'Asia/Muscat', '4.5' => 'Asia/Kabul', '5' => 'Asia/Karachi', '5.5' => 'Asia/Calcutta',
		'5.75' => 'Asia/Katmandu', '6' => 'Asia/Dhaka', '6.5' => 'Indian/Cocos', '7' => 'Asia/Bangkok', '8' => 'Australia/Perth',
		'8.75' => 'Australia/West', '9' => 'Asia/Tokyo', '9.5' => 'Australia/Adelaide', '10' => 'Australia/Brisbane',
		'10.5' => 'Australia/Lord_Howe', '11' => 'Pacific/Kosrae', '11.5' => 'Pacific/Norfolk', '12' => 'Pacific/Auckland',
		'12.75' => 'Pacific/Chatham', '13' => 'Pacific/Tongatapu', '14' => 'Pacific/Kiritimati'
	);

	/**
	 * The DateTimeZone object for usage in rending dates as strings.
	 *
	 * @var  object
	 */
	protected $_tz;

	/**
	 * Constructor.
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date.
	 * @return  void
	 * @throws  Exception
	 */
	public function __construct($date = 'now', $tz = null)
	{
		// Create the base GMT and server time zone objects.
		if (empty(self::$gmt) || empty(self::$stz))
		{
			self::$gmt = new DateTimeZone('GMT');
			self::$stz = new DateTimeZone(@date_default_timezone_get());
		}

		// If the time zone object is not set, attempt to build it.
		if (!($tz instanceof DateTimeZone))
		{
			if ($tz === null)
			{
				$tz = self::$gmt;
			}
			elseif (is_numeric($tz))
			{
				// Translate from offset.
				$tz = new DateTimeZone(self::$offsets[(string) $tz]);
			}
			elseif (is_string($tz))
			{
				$tz = new DateTimeZone($tz);
			}
		}

		// If the date is numeric assume a unix timestamp and convert it.
		date_default_timezone_set('UTC');
		$date = is_numeric($date) ? date('c', $date) : $date;

		// Call the DateTime constructor.
		parent::__construct($date, $tz);

		// reset the timezone for 3rd party libraries/extension that does not use JDate
		date_default_timezone_set(self::$stz->getName());

		// Set the timezone object for access later.
		$this->_tz = $tz;
	}

	/**
	 * Magic method to access properties of the date given by class to the format method.
	 *
	 * @param   string  $name  The name of the property.
	 * @return  mixed   A value if the property name is valid, null otherwise.
	 */
	public function __get($name)
	{
		$value = null;

		switch ($name)
		{
			case 'daysinmonth':
				$value = $this->format('t', true);
				break;

			case 'dayofweek':
				$value = $this->format('N', true);
				break;

			case 'dayofyear':
				$value = $this->format('z', true);
				break;

			case 'isleapyear':
				$value = (boolean) $this->format('L', true);
				break;

			case 'day':
				$value = $this->format('d', true);
				break;

			case 'hour':
				$value = $this->format('H', true);
				break;

			case 'minute':
				$value = $this->format('i', true);
				break;

			case 'second':
				$value = $this->format('s', true);
				break;

			case 'month':
				$value = $this->format('m', true);
				break;

			case 'ordinal':
				$value = $this->format('S', true);
				break;

			case 'week':
				$value = $this->format('W', true);
				break;

			case 'year':
				$value = $this->format('Y', true);
				break;

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
					E_USER_NOTICE
				);
		}

		return $value;
	}

	/**
	 * Magic method to render the date object in the format specified in the public
	 * static member Date::$format.
	 *
	 * @return  string  The date as a formatted string.
	 */
	public function __toString()
	{
		return (string) parent::format(self::$format);
	}

	/**
	 * Translates day of week number to a string.
	 *
	 * @param   integer  $day   The numeric day of the week.
	 * @param   boolean  $abbr  Return the abbreviated day string?
	 * @return  string  The day of the week.
	 */
	public function dayToString($day, $abbr = false)
	{
		switch ($day)
		{
			case 0:
				return $abbr ? \Lang::txt('SUN') : \Lang::txt('SUNDAY');
			case 1:
				return $abbr ? \Lang::txt('MON') : \Lang::txt('MONDAY');
			case 2:
				return $abbr ? \Lang::txt('TUE') : \Lang::txt('TUESDAY');
			case 3:
				return $abbr ? \Lang::txt('WED') : \Lang::txt('WEDNESDAY');
			case 4:
				return $abbr ? \Lang::txt('THU') : \Lang::txt('THURSDAY');
			case 5:
				return $abbr ? \Lang::txt('FRI') : \Lang::txt('FRIDAY');
			case 6:
				return $abbr ? \Lang::txt('SAT') : \Lang::txt('SATURDAY');
		}
	}

	/**
	 * Proxy for new Date().
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date.
	 * @return  object
	 */
	public static function of($date = 'now', $tz = null)
	{
		return new self($date, $tz);
	}

	/**
	 * Gets the date as a formatted string in a local calendar.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 * @return  string   The date string in the specified format format.
	 */
	public function calendar($format, $local = false, $translate = true)
	{
		return $this->format($format, $local, $translate);
	}

	/**
	 * Get the time offset from GMT in hours or seconds.
	 *
	 * @param   boolean  $hours  True to return the value in hours.
	 * @return  float    The time offset from GMT either in hours or in seconds.
	 */
	public function getOffsetFromGMT($hours = false)
	{
		return (float) $hours ? ($this->_tz->getOffset($this) / 3600) : $this->_tz->getOffset($this);
	}

	/**
	 * Translates month number to a string.
	 *
	 * @param   integer  $month  The numeric month of the year.
	 * @param   boolean  $abbr   If true, return the abbreviated month string
	 * @return  string   The month of the year.
	 */
	public function monthToString($month, $abbr = false)
	{
		switch ($month)
		{
			case 1:
				return $abbr ? \Lang::txt('JANUARY_SHORT') : \Lang::txt('JANUARY');
			case 2:
				return $abbr ? \Lang::txt('FEBRUARY_SHORT') : \Lang::txt('FEBRUARY');
			case 3:
				return $abbr ? \Lang::txt('MARCH_SHORT') : \Lang::txt('MARCH');
			case 4:
				return $abbr ? \Lang::txt('APRIL_SHORT') : \Lang::txt('APRIL');
			case 5:
				return $abbr ? \Lang::txt('MAY_SHORT') : \Lang::txt('MAY');
			case 6:
				return $abbr ? \Lang::txt('JUNE_SHORT') : \Lang::txt('JUNE');
			case 7:
				return $abbr ? \Lang::txt('JULY_SHORT') : \Lang::txt('JULY');
			case 8:
				return $abbr ? \Lang::txt('AUGUST_SHORT') : \Lang::txt('AUGUST');
			case 9:
				return $abbr ? \Lang::txt('SEPTEMBER_SHORT') : \Lang::txt('SEPTEMBER');
			case 10:
				return $abbr ? \Lang::txt('OCTOBER_SHORT') : \Lang::txt('OCTOBER');
			case 11:
				return $abbr ? \Lang::txt('NOVEMBER_SHORT') : \Lang::txt('NOVEMBER');
			case 12:
				return $abbr ? \Lang::txt('DECEMBER_SHORT') : \Lang::txt('DECEMBER');
		}
	}

	/**
	 * Method to wrap the setTimezone() function and set the internal
	 * time zone object.
	 *
	 * @param   object  $tz  The new DateTimeZone object.
	 * @return  object  The old DateTimeZone object.
	 */
	public function setTimezone($tz)
	{
		if (!($tz instanceof DateTimeZone))
		{
			$tz = new DateTimeZone($tz);
		}

		$this->_tz = $tz;

		return parent::setTimezone($tz);
	}

	/**
	 * Add to the date
	 * 
	 * @param   string  $modifier
	 * @return  object
	 */
	public function add($modifier)
	{
		return $this->modify('+' . $modifier);
	}

	/**
	 * Subtract from the date 
	 * 
	 * @param   string  $modifier
	 * @return  object
	 */
	public function subtract($modifier)
	{
		return $this->modify('-' . $modifier);
	}

	/**
	 * Gets the date as an ISO 8601 string.  IETF RFC 3339 defines the ISO 8601 format
	 * and it can be found at the IETF Web site.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string  The date string in ISO 8601 format.
	 *
	 * @link    http://www.ietf.org/rfc/rfc3339.txt
	 * @since   11.1
	 */
	public function toISO8601($local = false)
	{
		return $this->format(DateTime::RFC3339, $local, false);
	}

	/**
	 * Gets the date as an SQL datetime string.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 * @param   object   $dbo    The database driver or null to use global driver
	 * @return  string   The date string in SQL datetime format.
	 */
	public function toSql($local = false, $dbo = null)
	{
		if ($dbo === null)
		{
			$dbo = \App::get('db');
		}
		return $this->format($dbo->getDateFormat(), $local, false);
	}

	/**
	 * Gets the date as an RFC 822 string.  IETF RFC 2822 supercedes RFC 822 and its definition
	 * can be found at the IETF Web site.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 * @return  string   The date string in RFC 822 format.
	 */
	public function toRFC822($local = false)
	{
		return $this->format(DateTime::RFC2822, $local, false);
	}

	/**
	 * Gets the date as UNIX time stamp.
	 *
	 * @return  integer  The date as a UNIX timestamp.
	 */
	public function toUnix()
	{
		return (int) parent::format('U');
	}

	/**
	 * Gets the date as UNIX time stamp.
	 *
	 * @param   string  $format  The date format specification string (see {@link PHP_MANUAL#date})
	 * @return  string
	 */
	public function toLocal($format='')
	{
		$format = $format ?: self::$format;

		// get timezone idenfier from user setting otherwise user system
		$tz = \User::getParam('timezone', \Config::get('offset'));

		// set our timezone
		$this->setTimezone(new DateTimeZone($tz));

		// format date
		return $this->format($format, true);
	}

	/**
	 * Function to convert a static time into a relative measurement
	 *
	 * @param   string  $date  The date to convert
	 * @param   string  $unit  The optional unit of measurement to return
	 *                         if the value of the diff is greater than one
	 * @param   string  $time  An optional time to compare to, defaults to now
	 * @return  string  The converted time string
	 */
	public function relative($unit = null, $time = null)
	{
		if (is_null($time))
		{
			// Get now
			$time = new self('now');
		}

		// Get the difference in seconds between now and the time
		$diff = strtotime($time) - strtotime($this);

		// Less than a minute
		if ($diff < 60)
		{
			return \Lang::txt('JLIB_HTML_DATE_RELATIVE_LESSTHANAMINUTE');
		}

		// Round to minutes
		$diff = round($diff / 60);

		// 1 to 59 minutes
		if ($diff < 60 || $unit == 'minute')
		{
			return \Lang::txts('JLIB_HTML_DATE_RELATIVE_MINUTES', $diff);
		}

		// Round to hours
		$diff = round($diff / 60);

		// 1 to 23 hours
		if ($diff < 24 || $unit == 'hour')
		{
			return \Lang::txts('JLIB_HTML_DATE_RELATIVE_HOURS', $diff);
		}

		// Round to days
		$diff = round($diff / 24);

		// 1 to 6 days
		if ($diff < 7 || $unit == 'day')
		{
			return \Lang::txts('JLIB_HTML_DATE_RELATIVE_DAYS', $diff);
		}

		// Round to weeks
		$diff = round($diff / 7);

		// 1 to 4 weeks
		if ($diff <= 4 || $unit == 'week')
		{
			return \Lang::txts('JLIB_HTML_DATE_RELATIVE_WEEKS', $diff);
		}

		// [!] HUBZERO - Added months
		// Round to months
		/*$diff = round($diff / 4);

		// 1 to 12 months
		if ($diff <= 12 || $unit == 'month')
		{
			return \Lang::txt('%s months ago', $diff);
		}*/

		// [!] HUBZERO - Changed default to format "% days ago"
		// Over a month, return the absolute time
		$text = $this->_ago(strtotime($this), strtotime($time));

		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];
		$text .= ($parts[2]) ? ' ' . $parts[2] . ' ' . $parts[3] : '';

		return sprintf('%s ago', $text);
	}

	/**
	 * Calculate how long ago a date was
	 *
	 * @param   number  $timestamp  Date to convert
	 * @return  string
	 */
	protected function _ago($timestamp, $current_time=null)
	{
		// Store the current time
		if (is_null($current_time))
		{
			// Get now
			$current_time = new self('now');
			$current_time = strtotime($current_time);
		}

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0)
		{
			$val = 0;
		}

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1)
		{
			$periods[$val] .= 's';
		}

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0))
		{
			$text .= $this->_ago($new_time, $current_time);
		}

		return $text;
	}

	/**
	 * Gets the date as a formatted string.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 * @return  string   The date string in the specified format format.
	 */
	public function format($format, $local = false, $translate = true)
	{
		if ($format == 'relative')
		{
			return $this->relative();
		}

		if ($translate)
		{
			// Do string replacements for date format options that can be translated.
			$format = preg_replace('/(^|[^\\\])D/', "\\1" . self::DAY_ABBR, $format);
			$format = preg_replace('/(^|[^\\\])l/', "\\1" . self::DAY_NAME, $format);
			$format = preg_replace('/(^|[^\\\])M/', "\\1" . self::MONTH_ABBR, $format);
			$format = preg_replace('/(^|[^\\\])F/', "\\1" . self::MONTH_NAME, $format);
		}

		// If the returned time should not be local use GMT.
		if ($local == false)
		{
			parent::setTimezone(self::$gmt);
		}

		// Format the date.
		$return = parent::format($format);

		if ($translate)
		{
			// Manually modify the month and day strings in the formatted time.
			if (strpos($return, self::DAY_ABBR) !== false)
			{
				$return = str_replace(self::DAY_ABBR, $this->dayToString(parent::format('w'), true), $return);
			}

			if (strpos($return, self::DAY_NAME) !== false)
			{
				$return = str_replace(self::DAY_NAME, $this->dayToString(parent::format('w')), $return);
			}

			if (strpos($return, self::MONTH_ABBR) !== false)
			{
				$return = str_replace(self::MONTH_ABBR, $this->monthToString(parent::format('n'), true), $return);
			}

			if (strpos($return, self::MONTH_NAME) !== false)
			{
				$return = str_replace(self::MONTH_NAME, $this->monthToString(parent::format('n')), $return);
			}
		}

		if ($local == false)
		{
			parent::setTimezone($this->_tz);
		}

		return $return;
	}
}
