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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Events helper class for a date
 */
class EventsDate
{
	/**
	 * Year
	 *
	 * @var number
	 */
	var $year   = NULL;

	/**
	 * Month
	 *
	 * @var number
	 */
	var $month  = NULL;

	/**
	 * Day
	 *
	 * @var unknown
	 */
	var $day    = NULL;

	/**
	 * Hour
	 *
	 * @var integer
	 */
	var $hour   = NULL;

	/**
	 * Minute
	 *
	 * @var integer
	 */
	var $minute = NULL;

	/**
	 * Second
	 *
	 * @var integer
	 */
	var $second = NULL;

	/**
	 * Constructor
	 *
	 * @param      string $datetime Timestamp (0000-00-00 00:00:00)
	 * @return     void
	 */
    public function EventsDate($datetime='')
	{
		if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $datetime, $regs))
		{
			$this->setDate($regs[1], $regs[2], $regs[3]);
			$this->hour   = intval($regs[4]);
			$this->minute = intval($regs[5]);
			$this->second = intval($regs[6]);

			$this->month = max(1, $this->month);
			$this->month = min(12, $this->month);

			$this->day = max(1, $this->day);
			$this->day = min($this->daysInMonth(), $this->day);
		}
		else
		{
			$this->setDate(date("Y"), date("m"), date("d"));
			$this->hour   = 0;
			$this->minute = 0;
			$this->second = 0;
		}
	}

	/**
	 * Set the date
	 *
	 * @param      integer $year  Year
	 * @param      integer $month Month
	 * @param      integer $day   Day
	 * @return     void
	 */
	public function setDate($year=0, $month=0, $day=0)
	{
		$this->year  = intval($year);
		$this->month = intval($month);
		$this->day   = intval($day);

		$this->month = max(1, $this->month);
		$this->month = min(12, $this->month);

		$this->day = max(1, $this->day);
		$this->day = min($this->daysInMonth(), $this->day);
    }

	/**
	 * Get the year
	 *
	 * @param      boolean $asString Return as string?
	 * @return     mixed Integer unless $asString is true (string)
	 */
	public function getYear($asString=false)
	{
		return $asString ? sprintf("%04d", $this->year) : $this->year;
    }

	/**
	 * Get the month
	 *
	 * @param      boolean $asString Return as string?
	 * @return     mixed Integer unless $asString is true (string)
	 */
	public function getMonth($asString=false)
	{
		return $asString ? sprintf("%02d", $this->month) : $this->month;
    }

	/**
	 * Get the day
	 *
	 * @param      boolean $asString Return as string?
	 * @return     mixed Integer unless $asString is true (string)
	 */
	public function getDay($asString=false)
	{
		return $asString ? sprintf("%02d", $this->day) : $this->day;
    }

	/**
	 * Get the 12 hour time (am/pm)
	 *
	 * @return     string
	 */
	public function get12hrTime()
	{
		$hour=$this->hour;
		if ($hour > 12)
		{
			$hour -= 12;
		}
		elseif ($hour == 0)
		{
			$hour = 12;
		}
		$time = sprintf("%d:%02d", $hour, $this->minute);
		return ($this->hour >= 12) ? $time . 'pm' : $time . 'am';
	}

	/**
	 * Get the 24 hour time
	 *
	 * @return     string
	 */
	public function get24hrTime()
	{
		return sprintf("%02d:%02d", $this->hour, $this->minute);
	}

	/**
	 * Generate a URL from the date data
	 *
	 * @param      string $task Task to perform
	 * @return     string
	 */
	public function toDateURL($task='')
	{
		switch ($task)
		{
			case 'year':
				$url = 'year=' . $this->getYear(1);
				break;
			case 'month':
				$url = 'year=' . $this->getYear(1) . '&amp;month=' . $this->getMonth(1);
				break;
			case 'week':
				$url = 'year=' . $this->getYear(1) . '&amp;month=' . $this->getMonth(1) . '&amp;day=' . $this->getDay(1) . '&amp;task=week';
				break;
			case 'day':
			default:
				$url = 'year=' . $this->getYear(1) . '&amp;month=' . $this->getMonth(1) . '&amp;day=' . $this->getDay(1);
				break;
		}

		return $url;
    }

	/**
	 * Calculate the number of days in the month
	 *
	 * @param      integer $month Month
	 * @param      integer $year  Year
	 * @return     integer
	 */
	public function daysInMonth($month=0, $year=0)
	{
		$month = intval($month);
		$year = intval($year);
		if (!$month)
		{
			if (isset($this))
			{
				$month = $this->month;
			}
			else
			{
				$month = date("m");
			}
		}
		if (!$year)
		{
			if (isset($this))
			{
				$year = $this->year;
			}
			else
			{
				$year = date("Y");
			}
		}
		if ($month == 2)
		{
			if (($year % 4 == 0 && $year % 100 != 0) || $year % 400 == 0)
			{
				return 29;
			}
			else
			{
				return 28;
			}
		}
		else if ($month == 4 || $month == 6 || $month == 9 || $month == 11)
		{
			return 30;
		}
		else
		{
			return 31;
		}
	}

	/**
	 * Add months to the date
	 *
	 * @param      integer $n Number of months to add
	 * @return     void
	 */
	public function addMonths($n=0)
	{
		$an = abs($n);
		$years = floor($an / 12);
		$months = $an % 12;

		if ($n < 0)
		{
			$this->year -= $years;
			$this->month -= $months;
			if ($this->month < 1)
			{
				$this->year--;
				$this->month = 12 - $this->month;
			}
		}
		else
		{
			$this->year += $years;
			$this->month += $months;
			if ($this->month > 12)
			{
				$this->year++;
				$this->month -= 12;
			}
		}
	}

	/**
	 * Add days to the date
	 *
	 * @param      integer $n Number of days to add
	 * @return     void
	 */
	public function addDays($n=0)
	{
		$days = $this->toDays();
		$this->fromDays($days + $n);
	}

	/**
	 * Calculate the number of days until a date
	 *
	 * @param      integer $day   Day
	 * @param      number  $month Month
	 * @param      number  $year  Year
	 * @return     number
	 */
	public function toDays($day=0, $month=0, $year=0)
	{
		if (!$day)
		{
			if (isset($this))
			{
				$day = $this->day;
			}
			else
			{
				$day = date("d");
			}
		}
		if (!$month)
		{
			if (isset($this))
			{
				$month = $this->month;
			}
			else
			{
				$month = date("m");
			}
		}
		if (!$year)
		{
			if (isset($this))
			{
				$year = $this->year;
			}
			else
			{
				$year = date("Y");
			}
		}

		$century = floor($year / 100);
		$year = $year % 100;

		if ($month > 2)
		{
			$month -= 3;
		}
		else
		{
			$month += 9;
			if ($year)
			{
				$year--;
			}
			else
			{
				$year = 99;
				$century--;
			}
		}

		return (floor((146097 * $century) / 4) + floor((1461 * $year) / 4) + floor((153 * $month + 2) / 5) + $day + 1721119);
	}

	/**
	 * Calculate the date from the number of days passed
	 *
	 * @param      number $days Number of days
	 * @return     void
	 */
	public function fromDays($days)
	{
		$days -= 1721119;
		$century = floor((4 * $days - 1) /  146097);
		$days    = floor(4 * $days - 1 - 146097 * $century);
		$day     = floor($days /  4);

		$year    = floor((4 * $day +  3) /  1461);
		$day     = floor(4 * $day +  3 -  1461 * $year);
		$day     = floor(($day +  4) /  4);

		$month   = floor((5 * $day -  3) /  153);
		$day     = floor(5 * $day -  3 -  153 * $month);
		$day     = floor(($day +  5) /  5);

		if ($month < 10)
		{
			$month +=3;
		}
		else
		{
			$month -=9;
			if ($year++ == 99)
			{
				$year = 0;
				$century++;
			}
		}

		$this->day   = $day;
		$this->month = $month;
		$this->year  = $century*100 + $year;
	}
}

