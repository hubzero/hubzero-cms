<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_usage_domainclass' );

//-----------

class plgUsageDomainclass extends JPlugin
{
	public function plgUsageDomainclass(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'usage', 'domainclass' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onUsageAreas()
	{
		$areas = array(
			'domainclass' => JText::_('PLG_USAGE_DOMAINCLASS')
		);
		return $areas;
	}
	
	//-----------

	private function classlist(&$db, $class, $t=0, $enddate=0) 
	{
		// Set class list parameters...
		$hub = 1;
		if (!$enddate) {
			$dtmonth = date("m") - 1;
			$dtyear = date("Y");
			if (!$dtmonth) {
				$dtmonth = 12;
				$dtyear = $dtyear - 1;
			}
			$enddate = $dtyear . "-" . $dtmonth;
		}

		// Look up class list information...
		$classname = "";
		$sql = "SELECT name, valfmt, size FROM classes WHERE class = '" . mysql_escape_string($class) . "'";
		$db->setQuery( $sql );
		$result = $db->loadRow();
		if ($result) {
			$classname = $result[0];
			$valfmt = $result[1];
			$size = $result[2];
		}
		$html = '';
		if ($classname) {
			// Prepare some date ranges...
			$enddate .= "-00";
			$dtmonth = floor(substr($enddate, 5, 2));
			$dtyear = floor(substr($enddate, 0, 4));
			$dt = $dtyear . "-" . sprintf("%02d", $dtmonth) . "-00";
			$dtyearnext = $dtyear + 1;
			$dtmonthnext = floor(substr($enddate, 5, 2) + 1);
			if ($dtmonthnext > 12) {
				$dtmonthnext = 1;
				$dtyearnext++;
			}
			$dtyearprior = substr($enddate, 0, 4) - 1;
			$monthtext = date("F", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . " " . $dtyear;
			$yeartext = "Jan - " . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . " " . $dtyear;
			$twelvetext = date("M", mktime(0, 0, 0, $dtmonthnext, 1, $dtyear)) . " " . $dtyearprior . " - " . date("M", mktime(0, 0, 0, $dtmonth, 1, $dtyear)) . " " . $dtyear;
			$period = array(
				array("key" => 1,  "name" => $monthtext),
				array("key" => 0,  "name" => $yeartext),
				array("key" => 12, "name" => $twelvetext)
			);

			// Process each different date/time periods/range...
			$maxrank = 0;
			$classlist = array();
			for ($pidx = 0; $pidx < count($period); $pidx++) 
			{
				// Calculate the total value for this classlist...
				$classlistset = array();
				$sql = "SELECT classvals.name, classvals.value FROM classes, classvals WHERE classes.class = classvals.class AND classvals.hub = '" . mysql_escape_string($hub) . "' AND classes.class = '" . mysql_escape_string($class) . "' AND classvals.datetime = '" . mysql_escape_string($dt) . "' AND classvals.period = '" . mysql_escape_string($period[$pidx]["key"]) . "' AND classvals.rank = '0'";
				$db->setQuery( $sql );
				$results = $db->loadObjectList();
				if ($results) {
					foreach ($results as $row)
					{
						$formattedval = UsageHtml::valformat($row->value, $valfmt);
						if (strstr($formattedval, "day") !== FALSE) {
							$chopchar = strrpos($formattedval, ",");
							if ($chopchar !== FALSE) {
								$formattedval = substr($formattedval, 0, $chopchar) . "+";
							}
						}
						array_push($classlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", 100)));
					}
				}
				if (!count($classlistset)) {
					array_push($classlistset, array("n/a", 0, "n/a", "n/a"));
				}

				// Calculate the class values for the classlist...
				$rank = 1;
				$sql = "SELECT classvals.rank, classvals.name, classvals.value FROM classes, classvals WHERE classes.class = classvals.class AND classvals.hub = '" . mysql_escape_string($hub) . "' AND classes.class = '" . mysql_escape_string($class) . "' AND datetime = '" . mysql_escape_string($dt) . "' AND classvals.period = '" . mysql_escape_string($period[$pidx]["key"]) . "' AND classvals.rank > '0' ORDER BY classvals.rank, classvals.name";
				$db->setQuery( $sql );
				$results = $db->loadObjectList();
				if ($results) {
					foreach ($results as $row) 
					{
						if ($row->rank > 0 && (!$size || $row->rank <= $size)) {
							while ($rank < $row->rank) 
							{
								array_push($classlistset, array("n/a", 0, "n/a", "n/a"));
								$rank++;
							}
							$formattedval = UsageHtml::valformat($row->value, $valfmt);
							if (strstr($formattedval, "day") !== FALSE) {
								$chopchar = strrpos($formattedval, ",");
								if ($chopchar !== FALSE) {
									$formattedval = substr($formattedval, 0, $chopchar) . "+";
								}
							}
							if ($classlistset[0][1] > 0) {
								array_push($classlistset, array($row->name, $row->value, $formattedval, sprintf("%0.1f%%", (100 * $row->value / $classlistset[0][1]))));
							} else {
								array_push($classlistset, array($row->name, $row->value, $formattedval, "n/a"));
							}
							$rank++;
						}
					}
				}
				while ($rank <= $size || $rank == 1) 
				{
					array_push($classlistset, array("n/a", 0, "n/a", "n/a"));
					$rank++;
				}
				array_push($classlist, $classlistset);
				if ($rank > $maxrank) {
					$maxrank = $rank;
				}
			}

			$cls = 'even';

			// Print class list table...
			$html .= '<table summary="'.$classname.'">'."\n";
			$html .= "\t".'<caption>Table '.$t.': '.$classname.'</caption>'."\n";
			$html .= "\t".'<thead>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			for ($pidx = 0; $pidx < count($period); $pidx++) 
			{
				$html .= '<th colspan="3" scope="colgroup">'. $period[$pidx]["name"] .'</th>'."\n";
			}
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t".'</thead>'."\n";
			$html .= "\t".'<tbody>'."\n";
			$html .= "\t\t".'<tr class="summary">'."\n";
			for ($pidx = 0; $pidx < count($period); $pidx++) 
			{
				$tdcls = ($pidx != 1) ? ' class="group"' : '';
				$html .= "\t\t\t".'<th'.$tdcls.' scope="row">'. $classlist[$pidx][0][0] .'</th>'."\n";
				$html .= "\t\t\t".'<td'.$tdcls.'>'. $classlist[$pidx][0][2] .'</td>'."\n";
				$html .= "\t\t\t".'<td'.$tdcls.'>'. $classlist[$pidx][0][3] .'</td>'."\n";
			}
			$html .= "\t\t".'</tr>'."\n";
			for ($i = 1; $i < $maxrank; $i++) 
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$html .= "\t\t".'<tr class="'. $cls .'">'."\n";
				for ($pidx = 0; $pidx < count($period); $pidx++) 
				{
					$tdcls = ($pidx != 1) ? ' class="group"' : '';
					$html .= "\t\t\t".'<th'.$tdcls.' scope="row">';
					$html .= (isset($classlist[$pidx][$i][0])) ? $classlist[$pidx][$i][0] : '';
					$html .= '</th>'."\n";
					$html .= "\t\t\t".'<td'.$tdcls.'>';
					$html .= (isset($classlist[$pidx][$i][2])) ? $classlist[$pidx][$i][2] : '';
					$html .= '</td>'."\n";
					$html .= "\t\t\t".'<td'.$tdcls.'>';
					$html .= (isset($classlist[$pidx][$i][3])) ? $classlist[$pidx][$i][3] : '';
					$html .= '</td>'."\n";

				}
				$html .= "\t\t".'</tr>'."\n";
			}
			$html .= "\t".'</tbody>'."\n";
			$html .= '</table>'."\n";
		}
		return $html;
	}
	
	//-----------
	
	public function onUsageDisplay( $option, $task, $db, $months, $monthsReverse, $enddate ) 
	{
		// Check if our task is the area we want to return results for
		if ($task) {
			if (!in_array( $task, $this->onUsageAreas() ) 
			 && !in_array( $task, array_keys( $this->onUsageAreas() ) )) {
				return '';
			}
		}
		
		// Set some vars
		$thisyear = date("Y");
		
		$o = UsageHtml::options( $db, $enddate, $thisyear, $monthsReverse, 'check_for_classdata' );

		// Build HTML
		$html  = UsageHtml::form( $o, $option, $task );
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
?>
