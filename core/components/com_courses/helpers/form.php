<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
