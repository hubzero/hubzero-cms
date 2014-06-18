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
 * Usage plugin class for domain class
 */
class plgUsageDomainclass extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the name of the area this plugin retrieves records for
	 *
	 * @return     array
	 */
	public function onUsageAreas()
	{
		return array(
			'domainclass' => JText::_('PLG_USAGE_DOMAINCLASS')
		);
	}

	/**
	 * Build a table for the class list
	 *
	 * @param      object &$db     JDatabase
	 * @param      string $class   Class type
	 * @param      mixed  $t       Parameter description (if any) ...
	 * @param      mixed  $enddate Timestamp
	 * @return     string HTML
	 */
	private function classlist(&$db, $class, $t=0, $enddate=0)
	{
		// Set class list parameters...
		$hub = 1;
		if (!$enddate)
		{
			$dtmonth = date("m") - 1;
			$dtyear = date("Y");
			if (!$dtmonth)
			{
				$dtmonth = 12;
				$dtyear = $dtyear - 1;
			}
			$enddate = $dtyear . '-' . $dtmonth;
		}

		// Look up class list information...
		$classname = '';
		$sql = "SELECT name, valfmt, size FROM classes WHERE class = " . $db->Quote($class);
		$db->setQuery($sql);
		$result = $db->loadRow();
		if ($result)
		{
			$classname = $result[0];
			$valfmt = $result[1];
			$size = $result[2];
		}
		$html = '';
		if ($classname)
		{
			// Prepare some date ranges...
			$enddate .= '-00';
			$dtmonth = floor(substr($enddate, 5, 2));
			$dtyear = floor(substr($enddate, 0, 4));
			$dt = $dtyear . '-' . sprintf("%02d", $dtmonth) . '-00';
			$dtyearnext = $dtyear + 1;
			$dtmonthnext = floor(substr($enddate, 5, 2) + 1);
			if ($dtmonthnext > 12)
			{
				$dtmonthnext = 1;
				$dtyearnext++;
			}
			$dtyearprior = substr($enddate, 0, 4) - 1;
			$monthtext   = date("F", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$yeartext    = "Jan - " . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$twelvetext  = date("M", mktime(0, 0, 0, $dtmonthnext, 1, $dtyear)) . ' ' . $dtyearprior . ' - ' . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . ' ' . $dtyear;
			$period = array(
				array('key' => 1,  'name' => $monthtext),
				array('key' => 0,  'name' => $yeartext),
				array('key' => 12, 'name' => $twelvetext)
			);

			// Process each different date/time periods/range...
			$maxrank = 0;
			$classlist = array();
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				// Calculate the total value for this classlist...
				$classlistset = array();
				$sql = "SELECT classvals.name, classvals.value FROM classes, classvals WHERE classes.class = classvals.class AND classvals.hub = " . $db->Quote($hub) . " AND classes.class = " . $db->Quote($class) . " AND classvals.datetime = " . $db->Quote($dt) . " AND classvals.period = " . $db->Quote($period[$pidx]["key"]) . " AND classvals.rank = '0'";
				$db->setQuery($sql);
				$results = $db->loadObjectList();
				if ($results)
				{
					foreach ($results as $row)
					{
						$formattedval = UsageHtml::valformat($row->value, $valfmt);
						if (strstr($formattedval, 'day') !== FALSE)
						{
							$chopchar = strrpos($formattedval, ',');
							if ($chopchar !== FALSE)
							{
								$formattedval = substr($formattedval, 0, $chopchar) . '+';
							}
						}
						array_push($classlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", 100)));
					}
				}
				if (!count($classlistset))
				{
					array_push($classlistset, array('n/a', 0, 'n/a', 'n/a'));
				}

				// Calculate the class values for the classlist...
				$rank = 1;
				$sql = "SELECT classvals.rank, classvals.name, classvals.value FROM classes, classvals WHERE classes.class = classvals.class AND classvals.hub = '" . $db->Quote($hub) . " AND classes.class = " . $db->Quote($class) . " AND datetime = " . $db->Quote($dt) . " AND classvals.period = " . $db->Quote($period[$pidx]["key"]) . " AND classvals.rank > '0' ORDER BY classvals.rank, classvals.name";
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
								array_push($classlistset, array('n/a', 0, 'n/a', 'n/a'));
								$rank++;
							}
							$formattedval = UsageHtml::valformat($row->value, $valfmt);
							if (strstr($formattedval, 'day') !== FALSE)
							{
								$chopchar = strrpos($formattedval, ',');
								if ($chopchar !== FALSE)
								{
									$formattedval = substr($formattedval, 0, $chopchar) . '+';
								}
							}
							if ($classlistset[0][1] > 0)
							{
								array_push($classlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", (100 * $row->value / $classlistset[0][1]))));
							}
							else
							{
								array_push($classlistset, array($row->name, $row->value, $formattedval, 'n/a'));
							}
							$rank++;
						}
					}
				}
				while ($rank <= $size || $rank == 1)
				{
					array_push($classlistset, array('n/a', 0, 'n/a', 'n/a'));
					$rank++;
				}
				array_push($classlist, $classlistset);
				if ($rank > $maxrank)
				{
					$maxrank = $rank;
				}
			}

			$cls = 'even';

			// Print class list table...
			$html .= '<table>' . "\n";
			$html .= "\t" . '<caption>Table ' . $t . ': ' . $classname . '</caption>' . "\n";
			$html .= "\t" . '<thead>' . "\n";
			$html .= "\t\t" . '<tr>' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$html .= '<th colspan="3" scope="colgroup">' . $period[$pidx]["name"] . '</th>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			$html .= "\t" . '</thead>' . "\n";
			$html .= "\t" . '<tbody>' . "\n";
			$html .= "\t\t" . '<tr class="summary">' . "\n";
			for ($pidx = 0; $pidx < count($period); $pidx++)
			{
				$tdcls = ($pidx != 1) ? ' class="group"' : '';
				$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">' . $classlist[$pidx][0][0] . '</th>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $classlist[$pidx][0][2] . '</td>' . "\n";
				$html .= "\t\t\t" . '<td' . $tdcls . '>' . $classlist[$pidx][0][3] . '</td>' . "\n";
			}
			$html .= "\t\t" . '</tr>' . "\n";
			for ($i = 1; $i < $maxrank; $i++)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$html .= "\t\t" . '<tr class="' . $cls . '">' . "\n";
				for ($pidx = 0; $pidx < count($period); $pidx++)
				{
					$tdcls = ($pidx != 1) ? ' class="group"' : '';
					$html .= "\t\t\t" . '<th' . $tdcls . ' scope="row">';
					$html .= (isset($classlist[$pidx][$i][0])) ? $classlist[$pidx][$i][0] : '';
					$html .= '</th>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>';
					$html .= (isset($classlist[$pidx][$i][2])) ? $classlist[$pidx][$i][2] : '';
					$html .= '</td>' . "\n";
					$html .= "\t\t\t" . '<td' . $tdcls . '>';
					$html .= (isset($classlist[$pidx][$i][3])) ? $classlist[$pidx][$i][3] : '';
					$html .= '</td>' . "\n";

				}
				$html .= "\t\t" . '</tr>' . "\n";
			}
			$html .= "\t" . '</tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		return $html;
	}

	/**
	 * Event call for displaying usage data
	 *
	 * @param      string $option        Component name
	 * @param      string $task          Component task
	 * @param      object $db            JDatabase
	 * @param      array  $months        Month names (Jan -> Dec)
	 * @param      array  $monthsReverse Month names in reverse (Dec -> Jan)
	 * @param      string $enddate       Time period
	 * @return     string HTML
	 */
	public function onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)
	{
		// Check if our task is the area we want to return results for
		if ($task)
		{
			if (!in_array($task, $this->onUsageAreas())
			 && !in_array($task, array_keys($this->onUsageAreas())))
			{
				return '';
			}
		}

		// Set some vars
		$thisyear = date("Y");

		$o = UsageHelper::options($db, $enddate, $thisyear, $monthsReverse, 'check_for_classdata');

		// Build HTML
		$html  = '<form method="post" action="' . JRoute::_('index.php?option=' . $option . '&task=' . $task) .'">' . "\n";
		$html .= "\t" . '<fieldset class="filters">' . "\n";
		$html .= "\t\t" . '<label>' . "\n";
		$html .= "\t\t\t" . JText::_('PLG_USAGE_SHOW_DATA_FOR') . ': ' . "\n";
		$html .= "\t\t\t" . '<select name="selectedPeriod" id="selectedPeriod">' . "\n";
		$html .= $o;
		$html .= "\t\t\t" . '</select>' . "\n";
		$html .= "\t\t" . '</label> <input type="submit" value="' . JText::_('PLG_USAGE_VIEW') . '" />' . "\n";
		$html .= "\t" . '</fieldset>' . "\n";
		$html .= '</form>' . "\n";
		$html .= $this->classlist($db, 8, 1, $enddate);
		$html .= $this->classlist($db, 9, 2, $enddate);
		$html .= $this->classlist($db, 10, 3, $enddate);
		$html .= $this->classlist($db, 6, 4, $enddate);
		$html .= $this->classlist($db, 5, 5, $enddate);
		$html .= $this->classlist($db, 7, 6, $enddate);

		// Return HTML
		return $html;
	}
}
