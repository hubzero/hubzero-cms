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
 * Events helper class for repeating events
 */
class EventsRepeat
{
	/**
	 * Event
	 *
	 * @var unknown
	 */
	var $row      = NULL;

	/**
	 * Year
	 *
	 * @var unknown
	 */
	var $year     = NULL;

	/**
	 * Month
	 *
	 * @var unknown
	 */
	var $month    = NULL;

	/**
	 * Day
	 *
	 * @var unknown
	 */
	var $day      = NULL;

	/**
	 * If item is viewable
	 *
	 * @var boolean
	 */
	var $viewable = NULL;

	/**
	 * Constructor
	 * Checks if a repeating event should be viewable (ie, this is one of the repeat dates)
	 *
	 * @param      object $row   Event
	 * @param      number $year  Year
	 * @param      number $month Month
	 * @param      number $day   Day
	 * @return     boolean True if viewable
	 */
	public function EventsRepeat($row=NULL, $year=NULL, $month=NULL, $day=NULL)
	{
		if (is_null($row))
		{
			return false;
		}

		$select_date = sprintf("%4d-%02d-%02d", $year, $month, $day);
		$numero_du_jour = date("w",mktime(0, 0, 0, $month, $day, $year));

		if ($numero_du_jour == 0)
		{
		}

		$end_of_month = date("t",mktime(0, 0, 0, ($month+1), 0, $year));
		$event_up = new EventsDate($row->publish_up);
		$start_publish = sprintf("%4d-%02d-%02d", $event_up->year, $event_up->month, $event_up->day);
		$start_hours   = $event_up->hour;
		$start_minutes = $event_up->minute;
		$event_day     = $event_up->day;
		$event_month   = $event_up->month;
		$event_year    = $event_up->year;

		$event_down   = new EventsDate($row->publish_down);
		$stop_publish = sprintf("%4d-%02d-%02d", $event_down->year, $event_down->month, $event_down->day);
		$end_hours    = $event_down->hour;
		$end_minutes  = $event_down->minute;

		$repeat_event_type = $row->reccurtype;
		$repeat_event_day = $row->reccurday;
		$repeat_event_weekdays = $row->reccurweekdays;
		$repeat_event_weeks = $row->reccurweeks;

		$this->viewable = false;
		$is_the_event_period = false;
		$is_the_event_day = false;
		$is_the_event_daynumber = false;
		$is_the_event_dayname = false;

		// Week begin day and finish day
		$startday = _CAL_CONF_STARDAY;
		$numday = ((date("w", mktime(0, 0, 0, $month, $day, $year))-$startday)%7);
		if ($numday == -1)
		{
			$numday = 6;
		}
		$week_start = mktime (0, 0, 0, $month, ($day - $numday), $year);
		$this_week_date = new EventsDate();
		$this_week_date->setDate(date("Y", $week_start), date("m", $week_start), date("d", $week_start));
		$this_week_end_date = $this_week_date;
		$this_week_end_date->addDays(+6);

		$start_weekday = $this_week_date->day;
		$end_weekday = $this_week_end_date->day;

		// Weeks check process
		$is_week_1 = false;
		$is_week_2 = false;
		$is_week_3 = false;
		$is_week_4 = false;
		$is_week_5 = false;

		// By 7 to 7 periode
		if ((intval($day) <= 7))
		{
			$is_week_1 = true;
		}
		elseif ((intval($day) > 7) && (intval($day) <= 14))
		{
			$is_week_2 = true;
		}
		elseif ((intval($day) > 14) && (intval($day) <= 21))
		{
			$is_week_3 = true;
		}
		elseif ((intval($day) > 21) && (intval($day) <= 28))
		{
			$is_week_4 = true;
		}
		elseif ((intval($day) >= 28))
		{
			$is_week_5 = true;
		}

		// Check event time parametres
		if (($select_date <= $stop_publish) && ($select_date >= $start_publish))
		{
			$is_the_event_period = true;
		}
		if ($event_day == $day)
		{
			$is_the_event_day = true;
		}
		if ($numero_du_jour == $repeat_event_day)
		{
			$is_the_event_dayname = true;
		}
		$viewable_day = 0;
		if ($repeat_event_weekdays <> '')
		{
			$reccurweekdays = explode('|', $repeat_event_weekdays);
			$countdays = count($reccurweekdays);
			for ($x=0; $x < $countdays; $x++)
			{
				if ($reccurweekdays[$x] == $numero_du_jour)
				{
					$viewable_day = 1;
				}
			}
		}

		// Check event weeks parametres
		$pair_weeks = 0;
		$impair_weeks = 0;
		$viewable_week = 0;

		if ($repeat_event_weeks <> '')
		{
			$reccurweeks = explode('|', $repeat_event_weeks);
			$countweeks = count($reccurweeks);
			for ($x=0; $x < $countweeks; $x++)
			{
				if ($reccurweeks[$x] == 'pair')
				{
					$pair_weeks = 1;
				}
				elseif ($reccurweeks[$x] == 'impair')
				{
					$impair_weeks = 1;
				}

				if (($reccurweeks[$x] == 1) && ($is_week_1))
				{
					$viewable_week = 1;
				}
				elseif (($reccurweeks[$x] == 2) && ($is_week_2))
				{
					$viewable_week = 1;
				}
				elseif (($reccurweeks[$x] == 3) && ($is_week_3))
				{
					$viewable_week = 1;
				}
				elseif (($reccurweeks[$x] == 4) && ($is_week_4))
				{
					$viewable_week = 1;
				}
				elseif (($reccurweeks[$x] == 5) && ($is_week_5))
				{
					$viewable_week = 1;
				}
			}
		}
		else
		{
			$viewable_week = 1;
		}

		// Check repeat
		if ($is_the_event_period)
		{
			switch ($repeat_event_type)
			{
				case 0: // All days
					$this->viewable = true;
					return $this->viewable;
                break;

				case 1: // By week - 1* by week
					if (($pair_weeks && is_integer($day/2))
					 || ($impair_weeks && !is_integer($day/2))
					 || ($viewable_week) // && ($numero_du_jour <= 6))
					) {
						if ($repeat_event_day ==-1)
						{ //by day number
							if ($is_the_event_day || (($select_date >= $start_publish) && is_integer(($day - $event_day)/7)))
							{
								$this->viewable = true;
							}
						}
						elseif ($repeat_event_day >=0)
						{ //by day name
							if ($is_the_event_dayname)
							{
								$this->viewable = true;
							}
						}
					}
					return $this->viewable;
				break;

				case 2: // By week - n* by week
					if (($pair_weeks && is_integer($day/2))
					 || ($impair_weeks && !is_integer($day/2))
					 || ($viewable_week) // && ($numero_du_jour <= 6))
					) {
						if ($repeat_event_weekdays <> '')
						{ //by day select
							if ($viewable_day) {
								$this->viewable = true;
							}
						}
					}
					return $this->viewable;
				break;

				case 3: // By month - 1* by month
					if ($repeat_event_day ==-1)
					{ //by day number
						if ($is_the_event_day)
						{
							$this->viewable = true;
						}
					}
					elseif ($repeat_event_day >=0)
					{ //by day name
						if ($is_the_event_dayname)
						{
							$this->viewable = true;
						}
					}
					return $this->viewable;
				break;

				case 4: // By month - end of the month
					if ($day == $end_of_month)
					{
						$this->viewable = true;
					}
					return $this->viewable;
				break;

				case 5: // By year - 1* by year
					if ($repeat_event_day ==-1)
					{ //by day number
						if ($is_the_event_day && ($month == $event_month))
						{
							$this->viewable = true;
						}
					}
					elseif ($repeat_event_day >=0)
					{ //by day name
						if ($is_the_event_dayname
						 && (($day >= $event_day) && ($day <= $event_day+6))
						 && ($month == $event_month))
						{
							$this->viewable = true;
						}
					}
					return $this->viewable;
				break;

				default:
					return $this->viewable;
				break;
			} // end switch
		}
		else
		{
			return $this->viewable;
		}// end if
	}
}

