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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
