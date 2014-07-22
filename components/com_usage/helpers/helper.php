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
 * Usage helper class
 */
class UsageHelper
{
	/**
	 * Return a usage database object
	 *
	 * @return     mixed
	 */
	public static function getUDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			$config = JComponentHelper::getParams('com_usage');

			$options['driver']   = $config->get('statsDBDriver');
			$options['host']     = $config->get('statsDBHost');
			$options['port']     = $config->get('statsDBPort');
			$options['user']     = $config->get('statsDBUsername');
			$options['password'] = $config->get('statsDBPassword');
			$options['database'] = $config->get('statsDBDatabase');
			$options['prefix']   = $config->get('statsDBPrefix');

			if ((!isset($options['password']) || $options['password'] == '')
			 && (!isset($options['user']) || $options['user'] == '')
			 && (!isset($options['database']) || $options['database'] == ''))
			{
				$instance = JFactory::getDBO();
			}
			else
			{
				$instance = JDatabase::getInstance($options);
				if (JError::isError($instance))
				{
					$instance = JFactory::getDBO();
				}
			}
		}

		if (JError::isError($instance))
		{
			return null;
		}

		return $instance;
	}

	/**
	 * Print Top X List from Database
	 *
	 * @param      object $db Parameter description (if any) ...
	 * @param      unknown $top Parameter description (if any) ...
	 * @param      mixed $t Parameter description (if any) ...
	 * @param      mixed $enddate Parameter description (if any) ...
	 * @param      integer $raw Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function toplist($db, $top, $t=0, $enddate=0, $raw=0)
	{
		// Set top list parameters...
		$hub = 1;
		$html = '';

		if (!$enddate)
		{
			$dtmonth = date("m") - 1;
			$dtyear  = date("Y");
			if (!$dtmonth)
			{
				$dtmonth = 12;
				$dtyear = $dtyear - 1;
			}
			$enddate = $dtyear . '-' . $dtmonth;
		}

		$dtyearnext = $dtyear + 1;

		// Look up top list information...
		$topname = '';
		$sql = "SELECT name, valfmt, size FROM tops WHERE top=" . $db->quote($top);
		$db->setQuery($sql);
		$result = $db->loadRow();
		if ($result)
		{
			$topname = $result[0];
			$valfmt = $result[1];
			$size = $result[2];
		}

		if ($topname)
		{
			// Prepare some date ranges...
			$enddate .= '-00';
			$dtmonth = floor(substr($enddate, 5, 2));
			$dtyear = floor(substr($enddate, 0, 4));
			$dt = $dtyear . '-' . sprintf("%02d", $dtmonth) . '-00';
			$dtmonthnext = floor(substr($enddate, 5, 2) + 1);

			if ($dtmonthnext > 12)
			{
				$dtmonthnext = 1;
				$dtyearnext++;
			}

			$dtyearprior = substr($enddate, 0, 4) - 1;
			$monthtext   = date("F", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$yeartext    = 'Jan - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$twelvetext  = date("M", mktime(0, 0, 0, $dtmonthnext, 1, $dtyear)) . ' ' . $dtyearprior . ' - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$period = array(
				array('key' => 1,  'name' => $monthtext),
				array('key' => 0,  'name' => $yeartext),
				array('key' => 12, 'name' => $twelvetext)
			);

			// Process each different date/time periods/range...
			$toplist = array();
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				// Calculate the total value for this toplist...
				$toplistset = array();
				$sql = "SELECT topvals.name, topvals.value
						FROM tops, topvals
						WHERE tops.top = topvals.top
						AND topvals.hub = " . $db->quote($hub) . "
						AND tops.top = " . $db->quote($top) . "
						AND topvals.datetime = " . $db->quote($dt) . "
						AND topvals.period = " . $db->quote($period[$pidx]["key"]) . "
						AND topvals.rank = '0'";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					foreach ($results as $row)
					{
						$formattedval = UsageHelper::valformat($row->value, $valfmt);
						if (strstr($formattedval, "day") !== FALSE)
						{
							$chopchar = strrpos($formattedval, ',');
							if ($chopchar !== FALSE)
							{
								$formattedval = substr($formattedval, 0, $chopchar) . '+';
							}
						}
						array_push($toplistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", 100)));
					}
				}
				if (!count($toplistset))
				{
					array_push($toplistset, array('n/a', 0, 'n/a', 'n/a'));
				}

				// Calculate the top X values for the toplist...
				$rank = 1;
				$sql = "SELECT topvals.rank, topvals.name, topvals.value
						FROM tops, topvals
						WHERE tops.top = topvals.top
						AND topvals.hub = " . $db->quote($hub) . "
						AND tops.top = " . $db->quote($top) . "
						AND datetime = " . $db->quote($dt) . "
						AND topvals.period = " . $db->quote($period[$pidx]["key"]) . "
						AND topvals.rank > '0'
						ORDER BY topvals.rank, topvals.name";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					foreach ($results as $row)
					{
						if ($row->rank > 0 && (!$size || $row->rank <= $size))
						{
							while ($rank < $row->rank)
							{
								array_push($toplistset, array('n/a', 0, 'n/a', 'n/a'));
								$rank++;
							}
							$formattedval = UsageHelper::valformat($row->value, $valfmt);
							if (strstr($formattedval, 'day') !== FALSE)
							{
								$chopchar = strrpos($formattedval, ',');
								if ($chopchar !== FALSE)
								{
									$formattedval = substr($formattedval, 0, $chopchar) . '+';
								}
							}
							if ($toplistset[0][1] > 0)
							{
								array_push($toplistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", (100 * $row->value / $toplistset[0][1]))));
							}
							else
							{
								array_push($toplistset, array($row->name, $row->value, $formattedval, 'n/a'));
							}
							$rank++;
						}
					}
				}
				while ($rank <= $size || $rank == 1)
				{
					array_push($toplistset, array('n/a', 0, 'n/a', 'n/a'));
					$rank++;
				}
				array_push($toplist, $toplistset);
			}

			$cls = 'even';

			// Print top list table...
			$html .= '<table summary="">' . "\n";
			$html .= "\t" . '<caption>Table ' .$t.': ' .$topname.'</caption>' . "\n";
			$html .= "\t" . '<thead>' . "\n";
			$html .= "\t\t" . '<tr>' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$html .= "\t\t\t" . '<th colspan="3" scope="colgroup">' . $period[$pidx]["name"] .'</th>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			$html .= "\t" . '</thead>' . "\n";
			$html .= "\t" . '<tbody>' . "\n";
			$html .= "\t\t" . '<tr class="summary">' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$tdcls = ($pidx != 1) ? ' class="group"' : '';

				$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">' . $toplist[$pidx][0][0] .'</th>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][0][2] .'</td>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][0][3] .'</td>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			for ($i = 1; $i < $rank; $i++)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				$html .= "\t\t" . '<tr class="' . $cls .'">' . "\n";
				for ($pidx = 0; $pidx < count($period); $pidx++)
				{
					$tdcls = ($pidx != 1) ? ' class="group"' : '';
					$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">' . $toplist[$pidx][$i][0] .'</th>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][$i][2] .'</td>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>' . $toplist[$pidx][$i][3] .'</td>' . "\n";
				}
				$html .= "\t\t" . '</tr>' . "\n";
			}
			$html .= "\t" . '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		return $html;
	}

	/**
	 * Create New Array, Dropping All Duplicates and Reindexing All Elements
	 *
	 * @param      array $somearray
	 * @return     array
	 */
	public static function array_unique_reindex($somearray)
	{
		$tmparr = array_unique($somearray);
		$i = 0;
		foreach ($tmparr as $v)
		{
			$newarr[$i] = $v;
			$i++;
		}
		return($newarr);
	}

	/**
	 * Check for data for a date
	 *
	 * @param      object $db       JDatabase
	 * @param      string $yearmonth YYYY-MM
	 * @param      string $period    Time period to check for
	 * @return     boolean True if data is found
	 */
	public static function check_for_data($db, $yearmonth, $period)
	{
		$sql = "SELECT COUNT(datetime)
				FROM totalvals
				WHERE datetime LIKE " . $db->quote($yearmonth . '-%') . "
				AND period = " . $db->quote($period);
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result && $result > 0)
		{
			return(true);
		}
		return(false);
	}

	/**
	 * Check for domain class data
	 *
	 * @param      object $db       JDatabase
	 * @param      string $yearmonth YYYY-MM
	 * @return     boolean True if data is found
	 */
	public static function check_for_classdata($db, $yearmonth)
	{
		$sql = "SELECT COUNT(datetime)
				FROM classvals
				WHERE datetime LIKE " . $db->quote($yearmonth . '-%');
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result && $result > 0)
		{
			return(true);
		}
		return(false);
	}

	/**
	 * Check for region data for a date
	 *
	 * @param      object $db       JDatabase
	 * @param      string $yearmonth YYYY-MM
	 * @return     boolean True if data is found
	 */
	public static function check_for_regiondata($db, $yearmonth)
	{
		$sql = "SELECT COUNT(datetime)
				FROM regionvals
				WHERE datetime LIKE " . $db->quote($yearmonth . '-%');
		$db->setQuery($sql);
		$result = $db->loadResult();
		if ($result && $result > 0)
		{
			return(true);
		}
		return(false);
	}

	/**
	 * Short description for 'dateformat'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @param      string $period Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function dateformat($seldate, $period='month')
	{
		$year  = substr($seldate, 0, 4);
		$month = substr($seldate, 5, 2);
		$day   = substr($seldate, 8, 2);
		switch ($period)
		{
			case 'fiscalyear':
				if ($month <= 9)
				{
					return("FY " . $year);
				}
				else
				{
					return("FY " . ($year + 1));
				}
			break;
			case 'calyear':
				return($year);
			break;
			case 'quarter':
				if ($month <= 3)
				{
					$qtr = '1st';
				}
				elseif ($month <= 6)
				{
					$qtr = '2nd';
				}
				elseif ($month <= 9)
				{
					$qtr = '3rd';
				}
				else
				{
					$qtr = '4th';
				}
				return($qtr .' Quarter ' . $year);
			break;
			default:
				if ($day > 0)
				{
					return(sprintf("%d/%d/%d", $month, $day, $year));
				}
				else
				{
					return(sprintf("%d/%d", $month, $year));
				}
			break;
		}
	}

	/**
	 * Short description for 'dateformat_plot'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function dateformat_plot($seldate)
	{
		$year  = substr($seldate, 0, 4);
		$month = substr($seldate, 5, 2);
		$day   = substr($seldate, 8, 2);
		if ($day > 0)
		{
			return(sprintf("%02d/%02d/%04d", $month, $day, $year));
		}
		else
		{
			return(sprintf("%02d/%04d", $month, $year));
		}
	}

	/**
	 * Short description for 'seldate_value'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $valarray Parameter description (if any) ...
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @param      string $valkey Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function seldate_value($valarray, $seldate, $valkey='value')
	{
		if ($valarray)
		{
			foreach ($valarray as $val)
			{
				if (substr($val['date'], 0, strlen($seldate)) == $seldate)
				{
					return($val[$valkey]);
				}
			}
		}
		return(0);
	}

	/**
	 * Short description for 'seldate_next'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function seldate_next($seldate, $period)
	{
		return(UsageHelper::seldate_shift($seldate, $period, 1));
	}

	/**
	 * Short description for 'seldate_prev'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @param      unknown $period Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function seldate_prev($seldate, $period)
	{
		return(UsageHelper::seldate_shift($seldate, $period, 0));
	}

	/**
	 * Short description for 'seldate_nextyear'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function seldate_nextyear($seldate)
	{
		$date = $seldate;
		for ($i = 0; $i < 12; $i++)
		{
			$date = UsageHelper::seldate_shift($date, 'month', 1);
		}
		return($date);
	}

	/**
	 * Short description for 'seldate_prevyear'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function seldate_prevyear($seldate)
	{
		$date = $seldate;
		for ($i = 0; $i < 12; $i++)
		{
			$date = UsageHelper::seldate_shift($date, 'month', 0);
		}
		return($date);
	}

	/**
	 * Short description for 'seldate_fix'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @param      string $period Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function seldate_fix($seldate, $period)
	{
		$year  = substr($seldate, 0, 4);
		$month = substr($seldate, 5, 2);
		$day   = substr($seldate, 8, 2);
		if ($period == 'fiscalyear')
		{
			if ($month < 9)
			{
				$month = 9;
			}
			if ($month > 9)
			{
				$month = 9;
				$year++;
			}
		}
		elseif ($period == 'calyear')
		{
			if ($month != 12)
			{
				$month = 12;
			}
		}
		elseif ($period == 'quarter')
		{
			if ($month < 3)
			{
				$month = 3;
			}
			elseif ($month > 3 && $month < 6)
			{
				$month = 6;
			}
			elseif ($month > 6 && $month < 9)
			{
				$month = 9;
			}
			elseif ($month > 9 && $month < 12)
			{
				$month = 12;
			}
		}
		return(sprintf("%04d-%02d-%02d", $year, $month, $day));
	}

	/**
	 * Short description for 'seldate_shift'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $seldate Parameter description (if any) ...
	 * @param      unknown $period Parameter description (if any) ...
	 * @param      unknown $right Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function seldate_shift($seldate, $period, $right)
	{
		$year  = substr($seldate, 0, 4);
		$month = substr($seldate, 5, 2);
		$day   = substr($seldate, 8, 2);
		if ($right)
		{
			switch ($period)
			{
				case 'fiscalyear':
					$year++;
				break;
				case 'calyear':
					$year++;
				break;
				case 'quarter':
					$month += 3;
					if ($month > 12)
					{
						$year++;
						$month -= 12;
					}
				break;
				default:
					$month++;
					if ($month > 12)
					{
						$year++;
						$month = 1;
					}
				break;
			}
		}
		else
		{
			switch ($period)
			{
				case 'fiscalyear':
					$year--;
				break;
				case 'calyear':
					$year--;
				break;
				case 'quarter':
					$month -= 3;
					if ($month < 1)
					{
						$year--;
						$month += 12;
					}
				break;
				default:
					$month--;
					if ($month < 1)
					{
						$year--;
						$month = 12;
					}
				break;
			}
		}
		return(sprintf("%04d-%02d-%02d", $year, $month, $day));
	}

	/**
	 * Short description for 'seldate_valuedescsortkey'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array &$arr Parameter description (if any) ...
	 * @param      unknown $date Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public static function seldate_valuedescsortkey(&$arr, $date)
	{
		$reversealpha = array(
			',' => '',  '.' => '',  'A' => 'Z', 'B' => 'Y', 'C' => 'X', 'D' => 'W', 'E' => 'V',
			'F' => 'U', 'G' => 'T', 'H' => 'S', 'I' => 'R', 'J' => 'Q', 'K' => 'P', 'L' => 'O',
			'M' => 'N', 'N' => 'M', 'O' => 'L', 'P' => 'K', 'Q' => 'J', 'R' => 'I', 'S' => 'H',
			'T' => 'G', 'U' => 'F', 'V' => 'E', 'W' => 'D', 'X' => 'C', 'Y' => 'B', 'Z' => 'A'
		);
		$dmax = 0;
		$tmax = 0;
		for ($i = 0; $i < count($arr); $i++)
		{
			$dateval = UsageHelper::seldate_value($arr[$i], $date);
			$len = strlen($dateval);
			if ($len > $dmax)
			{
				$dmax = $len;
			}
			$len = strlen($arr[$i]['total']);
			if ($len > $tmax)
			{
				$tmax = $len;
			}
		}
		$format = "%0" . $dmax . "d%0" . $tmax . "d";
		for ($i = 0; $i < count($arr); $i++)
		{
			$arr[$i]['sortkey'] = '';
			$dateval = UsageHelper::seldate_value($arr[$i], $date);
			if (!$dateval)
			{
				$dateval = "0";
			}
			$arr[$i]['sortkey'] .= sprintf($format, $dateval, $arr[$i]['total']) . strtr(strtoupper($arr[$i]['name']), $reversealpha);
		}
		return($arr);
	}

	/**
	 * Short description for 'seldate_valuedescsort'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown &$arr Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function seldate_valuedescsort(&$arr)
	{
		return(usort($arr, "arraykeyeddesccmp"));
	}

	/**
	 * Short description for 'valformat'
	 *
	 * Long description (if any) ...
	 *
	 * @param      number $value Parameter description (if any) ...
	 * @param      integer $format Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function valformat($value, $format)
	{
		if ($format == 1)
		{
			return(number_format($value));
		}
		elseif ($format == 2 || $format == 3)
		{
			if ($format == 2)
			{
				$min = round($value / 60);
			}
			else
			{
				$min = floor($value / 60);
				$sec = $value - ($min * 60);
			}
			$hr = floor($min / 60);
			$min -= ($hr * 60);
			$day = floor($hr / 24);
			$hr -= ($day * 24);
			if ($day == 1)
			{
				$day = '1 ' . JText::_('COM_USAGE_DAY') . ', ';
			}
			elseif ($day > 1)
			{
				$day = number_format($day) . ' ' . JText::_('COM_USAGE_DAYS') . ', ';
			}
			else
			{
				$day = '';
			}
			if ($format == 2)
			{
				return(sprintf("%s%d:%02d", $day, $hr, $min));
			}
			else
			{
				return(sprintf("%s%d:%02d:%02d", $day, $hr, $min, $sec));
			}
		}
		else
		{
			return($value);
		}
	}

	/**
	 * Build a list of select options
	 *
	 * @param      object  $db           JDatabase
	 * @param      string  $enddate       Parameter description (if any) ...
	 * @param      integer $thisyear      Current year
	 * @param      array   $monthsReverse List of months (Dec -> Jan)
	 * @param      string  $func          Function to perform
	 * @return     string HTML
	 */
	public static function options($db, $enddate, $thisyear, $monthsReverse, $func='')
	{
		$o = '';
		for ($i = $thisyear; $i >= 2004; $i--)
		{
			foreach ($monthsReverse as $key => $month)
			{
				$value = $i . '-' . $key;
				if (UsageHelper::$func($db, $value))
				{
					$o .= '<option value="' . $value . '"';
					if ($value == $enddate)
					{
						$o .= ' selected="selected"';
					}
					$o .= '>' . $month . ' ' . $i . '</option>' . "\n";
				}
			}
		}
		return $o;
	}
}

