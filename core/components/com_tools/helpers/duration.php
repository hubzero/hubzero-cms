<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Helpers;

/**
 * A length of time kept in seconds, given and shown in a unit people use
 */
class Duration
{
	/**
	 * Seconds in each unit, largest first
	 *
	 * @var  array
	 */
	const UNITS = array(
		'days'    => 86400,
		'hours'   => 3600,
		'minutes' => 60,
		'seconds' => 1
	);

	/**
	 * Seconds in a number of the given unit
	 *
	 * Empty or zero means no length at all, as for a timeout that is not set.
	 *
	 * @param   mixed   $value  A number, which may have a fraction, as 1.5 hours
	 * @param   string  $unit   One of days, hours, minutes or seconds
	 * @return  integer|null  Null for no length
	 * @throws  \InvalidArgumentException  For a value that is not a number zero or more, or an unknown unit
	 */
	public static function toSeconds($value, $unit = 'seconds')
	{
		$value = trim((string) $value);

		if ($value === '')
		{
			return null;
		}

		if (!is_numeric($value) || $value < 0)
		{
			throw new \InvalidArgumentException('Not a length of time: ' . $value);
		}

		if (!isset(self::UNITS[$unit]))
		{
			throw new \InvalidArgumentException('Not a unit of time: ' . $unit);
		}

		$seconds = (int) round($value * self::UNITS[$unit]);

		return $seconds > 0 ? $seconds : null;
	}

	/**
	 * A number of seconds in the largest unit that holds it whole
	 *
	 * 7200 is 2 hours, 5400 is 90 minutes, 90 is 90 seconds.
	 *
	 * @param   integer|null  $seconds
	 * @param   string        $default  The unit to offer when there is no length
	 * @return  array  The number, empty for no length, and its unit
	 */
	public static function fromSeconds($seconds, $default = 'hours')
	{
		$seconds = (int) $seconds;

		if ($seconds <= 0)
		{
			return array('', $default);
		}

		foreach (self::UNITS as $unit => $size)
		{
			if ($seconds % $size == 0)
			{
				return array($seconds / $size, $unit);
			}
		}

		return array($seconds, 'seconds');
	}
}
