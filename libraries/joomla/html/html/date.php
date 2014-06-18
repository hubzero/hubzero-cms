<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Extended Utility class for handling date display.
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.3
 */
abstract class JHtmlDate
{
	/**
	 * Function to convert a static time into a relative measurement
	 *
	 * @param   string  $date  The date to convert
	 * @param   string  $unit  The optional unit of measurement to return
	 *                         if the value of the diff is greater than one
	 * @param   string  $time  An optional time to compare to, defaults to now
	 *
	 * @return  string  The converted time string
	 *
	 * @since   11.3
	 */
	public static function relative($date, $unit = null, $time = null)
	{
		if (is_null($time))
		{
			// Get now
			$time = JFactory::getDate('now');
		}

		// Get the difference in seconds between now and the time
		$diff = strtotime($time) - strtotime($date);

		// Less than a minute
		if ($diff < 60)
		{
			return JText::_('JLIB_HTML_DATE_RELATIVE_LESSTHANAMINUTE');
		}

		// Round to minutes
		$diff = round($diff / 60);

		// 1 to 59 minutes
		if ($diff < 60 || $unit == 'minute')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_MINUTES', $diff);
		}

		// Round to hours
		$diff = round($diff / 60);

		// 1 to 23 hours
		if ($diff < 24 || $unit == 'hour')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_HOURS', $diff);
		}

		// Round to days
		$diff = round($diff / 24);

		// 1 to 6 days
		if ($diff < 7 || $unit == 'day')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_DAYS', $diff);
		}

		// Round to weeks
		$diff = round($diff / 7);

		// 1 to 4 weeks
		if ($diff <= 4 || $unit == 'week')
		{
			return JText::plural('JLIB_HTML_DATE_RELATIVE_WEEKS', $diff);
		}

		// [!] HUBZERO - Added months
		// Round to months
		/*$diff = round($diff / 4);

		// 1 to 12 months
		if ($diff <= 12 || $unit == 'month')
		{
			return \JText::plural('%s months ago', $diff);
		}*/

		// [!] HUBZERO - Changed default to format "% days ago"
		// Over a month, return the absolute time
		$text = self::_ago(strtotime($date), strtotime($time));

		$parts = explode(' ', $text);

		$text  = $parts[0] . ' ' . $parts[1];
		$text .= ($parts[2]) ? ' ' . $parts[2] . ' ' . $parts[3] : '';
		return JText::sprintf('%s ago', $text);

		// Over a month, return the absolute time
		//return JHtml::_('date', $date);
	}

	/**
	 * Calculate how long ago a date was
	 *
	 * @param      number $timestamp Date to convert
	 * @return     string
	 */
	public static function _ago($timestamp, $current_time=null)
	{
		// Store the current time
		if (is_null($current_time))
		{
			// Get now
			$current_time = JFactory::getDate('now');
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
			$text .= self::_ago($new_time, $current_time);
		}

		return $text;
	}
}
