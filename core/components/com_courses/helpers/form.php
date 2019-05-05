<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

/**
 * Form Helper class
 */
class Form
{
	/**
	 * Time remaining (in human readable language)
	 *
	 * @return  string
	 */
	public static function timeDiff($secs)
	{
		$seconds = array(1, 'second');
		$minutes = array(60 * $seconds[0], 'minute');
		$hours   = array(60 * $minutes[0], 'hour');
		$days    = array(24 * $hours[0],   'day');
		$weeks   = array(7  * $days[0],    'week');
		$rv      = array();

		foreach (array($weeks, $days, $hours, $minutes, $seconds) as $step)
		{
			list($sec, $unit) = $step;
			$times = floor($secs / $sec);

			if ($times > 0)
			{
				$secs -= $sec * $times;
				$rv[] = $times . ' ' . $unit . ($times == 1 ? '' : 's');

				if (count($rv) == 2)
				{
					break;
				}
			}
			else if (count($rv))
			{
				break;
			}
		}

		return join(', ', $rv);
	}

	/**
	 * Convert integer to ordinal number
	 *
	 * @return  string
	 */
	public static function toOrdinal($int)
	{
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');

		if (($int %100) >= 11 && ($int%100) <= 13)
		{
			$abbreviation = $int . 'th';
		}
		else
		{
			$abbreviation = $int . $ends[$int % 10];
		}

		return $abbreviation;
	}
}
