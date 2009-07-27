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
JPlugin::loadLanguage( 'plg_usage_overview' );

//-----------

class plgUsageOverview extends JPlugin
{
	public function plgUsageOverview(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'usage', 'overview' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	//-----------

	public function onUsageAreas()
	{
		$areas = array(
			'overview' => JText::_('USAGE_OVERVIEW')
		);
		return $areas;
	}

	//-----------------------------//
	//  Strip Usage GET variables  //
	//-----------------------------//
	
	private function usageurlstrip($url) 
	{
	   	$pvar = strpos($url, "period=");
	    if ($pvar) {
	   	    $pvar--;
	 	 	$url = substr($url, 0, $pvar);
	   	}
	   	return($url);
	}	

	//-------------------------------------------------
	//  Returns TRUE if there is data in the database
	//  for the date passed to it, FALSE otherwise.
	//-------------------------------------------------
	
	private function check_for_data(&$db, $yearmonth, $period) 
	{
	   	$sql = "SELECT COUNT(datetime) FROM totalvals WHERE datetime LIKE '" . mysql_escape_string($yearmonth) . "-%' AND period = '" . mysql_escape_string($period) . "'";
		$db->setQuery( $sql );
		$result = $db->loadResult();   	

	   	if ($result && $result > 0) {
			return(true);
	   	}
	   	return(false);
	}
	
	//-----------

	private function print_user_row(&$db, $id, $period, $datetime) 
	{
		$html = '';
		
		$sql = "SELECT value, valfmt FROM summary_user_vals WHERE rowid='".$id."' AND period='".$period."' AND datetime ='".$datetime."' ORDER BY colid";
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		if ($results) {
			$i = 0;
			foreach ($results as $row) 
			{
				$i++;
				$cls = ($i >= 7) ? ' class="group"' : '';
				if ($i == 1) {
					$cls = ' class="highlight"';
				}
				$html .= t.t.t.'<td'.$cls.'>'.trim($this->fmt_result($row->value,$row->valfmt)).'</td>'.n;
			}
		}
		if ($i == 1) {
			$html .= $this->empty_rows(10);
		}
		return $html;
	}
	
	private function getSparkline($db, $id, $period, $datetime) 
	{
		$sparkline = '';
		
		$thisyear = date("Y");
		$tp = $thisyear - 2000;
		$limit = $tp * 12;
		$limit = 12;
		
		// Pull results
		$sql = "SELECT value, valfmt, datetime FROM summary_user_vals WHERE rowid='$id' AND period='$period' AND datetime<='$datetime' AND colid='$id' ORDER BY datetime DESC LIMIT ".$limit;
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		
		if ($results) {
			// Reverse the array (we'll be getting back data in DESC order, we need it in ASC order)
			$results = array_reverse($results);

			// Find the highest value
			$vals = array();
			foreach ($results as $result) 
			{
				$vals[] = $result->value;
			}
			asort($vals);

			$highest = array_pop($vals);

			// Generate the sparkline	
			$sparkline .= '<span class="sparkline">'.n;
			foreach ($results as $result) 
			{
				$height = round(($result->value / $highest)*100);
				$sparkline .= t.'<span class="index"><span class="count" style="height: '.$height.'%;" title="'.JHTML::_('date', $result->datetime, '%d %b. %Y').': '.trim($this->fmt_result($result->value,$result->valfmt)).'">'.trim($this->fmt_result($result->value,$result->valfmt)).'</span> </span>'.n;
			}
			$sparkline .= '</span>'.n;
		}
		
		return $sparkline;
	}
	
	//-----------
	
	private function empty_rows($n) 
	{
		$html = '';
		$i = 0;
		for ($i=0, $n; $i < $n; $i++) 
		{
			$cls = ($i >= 5) ? ' class="group"' : '';
			$html .= t.t.t.'<td'.$cls.'>-</td>'.n;
		}
		return $html;
	}
	
	//-----------

	private function fmt_result($value, $fmt) 
	{
		$valfmt[0]='-'; // blank. for future use
		$valfmt[1]=' '; // no units
		$valfmt[2]='%';
		$valfmt[3]='users';
		$valfmt[4]='jobs';
		$valfmt[5]='days';
		$valfmt[6]='hours';
		$valfmt[7]=''; // text data. display as is

		if ($fmt == 0) {
			return $valfmt[0];
		} else if ($fmt == 1) {
			$value = number_format($value).$valfmt[$fmt];
			return $value;
		} else if ($fmt == 2) {
			$value = number_format($value).$valfmt[$fmt];
			return $value;
		} else if ($fmt == 5) {
			$value = number_format(($value/86400)).' '.$valfmt[$fmt];
			return $value;
		} else if ($fmt == 6) {
			return $value;
		} else {
			$value = number_format($value).' '.$valfmt[$fmt];
			return $value;
		}
	}

	//-----------

	private function navlinks($option, $task, $period='prior12') 
	{
		$html  = '<div id="sub-sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$html .= t.t.'<li';    
		if ($period == 'prior12') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task='.$task.a.'period=prior12').'"><span>'.JText::_('USAGE_PERIOD_PRIOR12').'</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($period == 'month') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task='.$task.a.'period=month').'"><span>'.JText::_('USAGE_PERIOD_MONTH').'</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($period == 'qtr') {
			$html .= ' class="active"';
		}
	    $html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task='.$task.a.'period=qtr').'"><span>'.JText::_('USAGE_PERIOD_QTR').'</span></a></li>'.n;
		$html .= t.t.'<li';  
		if ($period == 'year') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task='.$task.a.'period=year').'"><span>'.JText::_('USAGE_PERIOD_YEAR').'</span></a></li>'.n;
		$html .= t.t.'<li';  
		if ($period == 'fiscal') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'task='.$task.a.'period=fiscal').'"><span>'.JText::_('USAGE_PERIOD_FISCAL').'</span></a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div>'.n;

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
		
		$config =& JComponentHelper::getParams( $option );
		
		// Incoming
		$period = JRequest::getVar('period', 'prior12');
		$selectedPeriod = JRequest::getVar('selectedPeriod', '');
		
		if (!$selectedPeriod) {
	        $sql = "SELECT MAX(datetime) FROM summary_collab_vals";
			$db->setQuery( $sql );
			$lastdate = $db->loadResult();
	        if ($lastdate) {
	            $defaultMonth = substr($lastdate, 5, 2);
	            $defaultYear = substr($lastdate, 0, 4);
	        } else {
	            $defaultMonth = date("m");
	            $defaultYear = date("Y");
	        }
	        $selectedPeriod = $defaultYear.'-'.$defaultMonth;
	    }
		$checkyear = substr($selectedPeriod, 0, 4);
	    $checkmonth = substr($selectedPeriod, 5, 2);
	    if ($checkyear <='2007') {
	        if ($checkyear < '2007') {
	            $page = 'old';
	        } else if ($checkyear == '2007') {
	            if ($checkmonth < '10') {
	                $page = 'old';
	            } else {
	                $page = 'new';
	            }
	        }
	    } else {
	        $page = 'new';
	    }
	
		if ($period == 'nice') {
			$page = 'old';
			$selectedPeriod = '2007-09';
		}
		
		// Get and set some vars
		$cur_year = floor(date("Y"));
		$cur_month = floor(date("n"));
		$year_data_start = 2000;
		$datetime = $selectedPeriod.'-00 00:00:00';

		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem(JText::_('USAGE_PERIOD_'.strtoupper($period)),'index.php?option='.$option.a.'task='.$task.a.'period='.$period);

		// Build the HTML
		$html  = $this->navlinks($option, $task, $period);
		$html .= '<form method="post" action="'. JRoute::_('index.php?option='.$option.a.'task='.$task.a.'period='.$period) .'">'.n;
		$html .= t.'<fieldset class="filters"><label>'.JText::_('USAGE_SHOW_DATA_FOR').': ';
		
		$html .= '<select name="selectedPeriod">'.n;
		switch ($period) 
		{
			case '12':
			case 'prior12':
			case 'nice':
				$option = 'prior12';
				$period = '12';
				
				$arrayMonths = array_values($months);
				for ($i = $cur_year; $i >= $year_data_start; $i--) 
				{
					foreach ($monthsReverse as $key => $month) 
					{
						if ($key == '12') {
							$nextmonth = 'Jan';
						} else {
							$nextmonth = $arrayMonths[floor(array_search($month, $arrayMonths))+1];
						}
						$value = $i . '-' . $key;
						if ($this->check_for_data($db, $value, 12)) {
							$html .= '<option value="'. $value .'"';
							if ($value == $selectedPeriod) {
								$html .= ' selected="selected"';
							}
							$html .= '>'. $nextmonth .' ';
							if ($key == 12) {
								$html .= $i;
							} else {
								$html .= $i - 1;
							}
						   	$html .= ' - '. $month .' '. $i .'</option>'.n;
						}
					}
				}
			break;
			
			case '1':
			case 'month':
				$option = 'month';
				$period = '1';
				
				for ($i = $cur_year; $i >= $year_data_start; $i--) 
				{
					foreach ($monthsReverse as $key => $month) 
					{
						$value = $i . '-' . $key;
						if ($this->check_for_data($db, $value, 1)) {
							$html .= '<option value="'. $value .'"';
							if ($value == $selectedPeriod) {
								$html .= ' selected="selected"';
							}
							$html .= '>'. $month .' '. $i .'</option>'.n;
						}
					}
				}
			break;
			
			case '3':
			case 'qtr':
				$option = 'qtr';
				$period = '3';
				
				$qtd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$qtd_found && $this->check_for_data($db, $value, 3)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>';
						if ($key <= 3) {
							$key = 0;
							$html .= 'Jan';
						} elseif($key <= 6) {
							$key = 3;
							$html .= 'Apr';
						} elseif($key <= 9) {
							$key = 6;
							$html .= 'Jul';
						} else {
							$key = 9;
							$html .= 'Oct';
						}
						$html .= ' '. $cur_year .' - '. $month .' '. $cur_year .'</option>'.n;
						$qtd_found = 1;
					}
				}
				$qtr_found = 0;
				for ($j = $cur_year; $j >= $year_data_start; $j--) 
				{
					for ($i = 12; $i > 0; $i = $i - 3) 
					{
						if ($qtr_found && $key) {
							$i = $key;
							$qtd_found = 0;
						}
						$value = $j . '-' . sprintf("%02d", $i);
						if ($this->check_for_data($db, $value, 3)) {
							$html .= '<option value="'. $value .'"';
							if ($value == $selectedPeriod) {
								$html .= ' selected="selected"';
							}
							$html .= '>';
							if ($i == 3) {
								$html .= 'Jan';
							} elseif ($i == 6) {
								$html .= 'Apr';
							} elseif ($i == 9) {
								$html .= 'Jul';
							} else {
								$html .= 'Oct';
							}
							$html .= ' '. $j .' - ';
							if ($i == 3) {
								$html .= 'Mar';
							} elseif ($i == 6) {
								$html .= 'Jun';
							} elseif ($i == 9) {
								$html .= 'Sep';
							} else {
								$html .= 'Dec';
							}
							$html .= ' '. $j .'</option>'.n;
						}
					}
				}
			break;
			
			case '13':
			case 'fiscal':
				$option = 'fiscal';
				$period = '13';
				
				$ytd_found = 0;
				$full_year = $cur_year - 1;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						if ($cur_month >= 9) {
							$html .= $cur_year;
							$full_year = $cur_year;
						} else {
							$html .= $cur_year - 1;
							$full_year = $cur_year - 1;
						}
						$html .= ' - '. $month .' '. $cur_year .'</option>'.n;
						$ytd_found = 1;
					}
				}
				for ($i = $full_year; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-09';
					if ($this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						$html .= $i - 1;
						$html .= ' - Sep '. $i .'</option>'.n;
					}
				}
			break;
			
			case '0':
			case 'year':
				$option = 'year';
				$period = '0';
				
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - '. $month .' '. $cur_year .'</option>'.n;
						$ytd_found = 1;
					}
				}
				for ($i = $cur_year - 1; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-12';
					if ($this->check_for_data($db, $value, 0)) {
						$html .= '<option value="'. $value .'"';
						if ($value == $selectedPeriod) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - Dec '. $i .'</option>'.n;
					}
				}
			break;
		}
		$html .= '</select></label> <input type="submit" value="'.JText::_('USAGE_VIEW').'" /></fieldset>'.n;
		$html .= '</form>'.n;

		//--------------------------------

		$html .= '<table summary="'.JText::_('A break-down of site visitors.').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 1: User statistics').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col" rowspan="2" colspan="2">'.JText::_('Users').'</th>'.n;
		$html .= t.t.t.'<th scope="col" rowspan="2" class="numerical-data">'.JText::_('Totals').'</th>'.n;
		$html .= t.t.t.'<th scope="colgroup" colspan="5">'.JText::_('Residence').'</th>'.n;
		$html .= t.t.t.'<th scope="colgroup" colspan="5">'.JText::_('Organization').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Identified').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('US').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Asia').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Europe').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Other').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data">'.JText::_('Identified').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data" abbr="'.JText::_('Education').'">'.JText::_('Edu.').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data" abbr="'.JText::_('Industry').'">'.JText::_('Ind.').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data" abbr="'.JText::_('Government').'">'.JText::_('Gov.').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data">'.JText::_('Other').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		$sql = "SELECT id, label, plot FROM summary_user WHERE id IN (1,2,3,4,5) ORDER BY id";
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		if ($results) {
			$i = 0;
			$cls = 'even';
			foreach ($results as $row) 
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';
				if ($i == 0) {
					$cls = 'summary';
				}
				
				$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
				
				$sparkline = $this->getSparkline($db, $row->id, $period, $datetime);
				
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
				if ($row->plot == '1') {
					$img = $config->get('charts_path').DS.substr($datetime,0,7)."-".$period."-u".$row->id;
					if (is_file(JPATH_ROOT.DS.$img.'.gif')) {
						$html .= t.t.t.'<td><a href="'.$img.'.gif" title="DOM:users1'.$i.'" class="fixedImgTip" rel="external"><img src="'.$img.'thumb.gif" alt="" /></a><br /><div style="display:none;" id="users1'.$i.'"><img src="'.$img.'.gif" alt="" /></div></td>'.n;
					} else if (isset($sparkline)) {
						$html .= t.t.t.'<td>'.$sparkline.'</td>'.n;
					} else {
						$html .= t.t.t.'<td>&nbsp;</td>'.n;
					}
				} else if (isset($sparkline)) {
					$html .= t.t.t.'<td>'.$sparkline.'</td>'.n;
				} else {
					$html .= t.t.t.'<td>&nbsp;</td>'.n;
				}
				$html .= $this->print_user_row($db, $row->id, $period, $datetime);
				$html .= t.t.'</tr>'.n;
				$i++;
	       	}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="13" class="textual-data">'.JText::_('No data found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		// Start simulation Usage
		$html .= '<table summary="'.JText::_('Simulation Usage').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 2: Simulation Usage').'</caption>'.n;
		$html .= t.'<tbody>'.n;
		$sql = "SELECT a.label,b.value,b.valfmt,a.plot,a.id FROM summary_simusage AS a, summary_simusage_vals AS b WHERE a.id=b.rowid AND b.period = '".$period."' AND b.datetime = '".$datetime."' ORDER BY a.id";
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		if ($results) {
			$cls = 'even';
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
				
				//$sparkline = $this->getSparkline($db, $row->id, $period, $datetime);
				
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
				$html .= t.t.t.'<td>'. $this->fmt_result($row->value,$row->valfmt) .'</td>'.n;
				if ($row->plot == '1') {
					$img = $config->get('charts_path').DS.substr($datetime,0,7)."-".$period."-s".$row->id;
					if (is_file(JPATH_ROOT.DS.$img.'.gif')) {
						$html .= t.t.t.'<td><a href="'.$img.'.gif" title="DOM:sim'.$i.'" class="fixedImgTip" rel="external"><img src="'.$img.'thumb.gif" alt="" /></a><br /><div style="display:none;" id="sim'.$i.'"><img src="'.$img.'.gif" alt="" /></div></td>'.n;
					//} else if (isset($sparkline)) {
					//	$html .= t.t.t.'<td>'.$sparkline.'</td>'.n;
					} else {
						$html .= t.t.t.'<td>&nbsp;</td>'.n;
					}
				//} else if (isset($sparkline)) {
				//	$html .= t.t.t.'<td>'.$sparkline.'</td>'.n;
				} else {
					$html .= t.t.t.'<td>&nbsp;</td>'.n;
				}
				$html .= t.t.'</tr>'.n;
				
				$i++;
	       	}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="2" class="textual-data">'.JText::_('No data found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		// Start miscellaneous
		$html .= '<table summary="'.JText::_('Miscellaneous Statistics').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 3: Miscellaneous Statistics').'</caption>'.n;
		$html .= t.'<tbody>'.n;
		$sql = "SELECT a.label,b.value,b.valfmt FROM summary_misc AS a, summary_misc_vals AS b WHERE a.id=b.rowid AND b.period = '".$period."' AND b.datetime = '".$datetime."' ORDER BY a.id";
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		if ($results) {
			$cls = 'even';
			foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				//$label = str_replace('{','<sup>',$row->label);
				//$label = str_replace('}','</sup>',$label);
				$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
				
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
				$html .= t.t.t.'<td>'. $this->fmt_result($row->value, $row->valfmt) .'</td>'.n;
				$html .= t.t.'</tr>'.n;
	       	}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="2" class="textual-data">'.JText::_('No data found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		
		
		// "and more" Usage
		$html .= '<table summary="'.JText::_('&quot;and more&quot; Usage').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 4: &quot;and more&quot; Usage').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col" rowspan="2">'.JText::_('Type').'</th>'.n;
		$html .= t.t.t.'<th scope="colgroup" colspan="2">'.JText::_('Users').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Interactive').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Download').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		$id = 1;
		$trows = '';
		$cls = 'even';
		while ($id < 9) 
		{
			$sql = "SELECT a.id, a.label, b.value,b.colid,b.valfmt FROM summary_andmore a, summary_andmore_vals b WHERE a.id=b.rowid AND period='".$period."' AND datetime='".$datetime."' AND a.id='".$id."' ORDER BY b.colid";
			$db->setQuery( $sql );
			$results = $db->loadObjectList();   	
			if ($results) {
				foreach ($results as $row)
				{
					//$label = str_replace('{','<sup>',$row->label);
					//$label = str_replace('}','</sup>',$label);
					$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
					
					$col = $row->colid;
					if ($col == 1) {
	               		$value1 = $this->fmt_result($row->value,$row->valfmt);
	                } else if ($col == 2) {
	                	$value2 = $this->fmt_result($row->value,$row->valfmt);
	                }
	            }
	            $cls = ($cls == 'even') ? 'odd' : 'even';

				$trows .= t.t.'<tr class="'.$cls.'">'.n;
	            $trows .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
	            $trows .= t.t.t.'<td>'.$value1.'</td>'.n;
	            $trows .= t.t.t.'<td>'.$value2.'</td>'.n;
	            $trows .= t.t.'</tr>'.n;
		    }
		    $id++;
		}
		$id = 9;
		while ($id < 13) 
		{
			$sql = "SELECT a.id, a.label, b.value,b.colid,b.valfmt FROM summary_andmore a, summary_andmore_vals b WHERE a.id=b.rowid AND period='".$period."' AND datetime='".$datetime."' AND a.id='".$id."' ORDER BY b.colid";
		    $db->setQuery( $sql );
			$results = $db->loadObjectList();
			if ($results) {
				foreach ($results as $row)
				{
					//$label = str_replace('{','<sup>',$row->label);
					//$label = str_replace('}','</sup>',$label);
					$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
					
					$col = $row->colid;
	                if ($col == 1) {
	                	$value1 = $this->fmt_result($row->value,$row->valfmt);
	                } else if ($col == 2) {
	                	$value2 = $this->fmt_result($row->value,$row->valfmt);
	                }
	            }
	            $cls = ($cls == 'even') ? 'odd' : 'even';

				$trows .= t.t.'<tr class="'.$cls.'">'.n;
	            $trows .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
	            $trows .= t.t.t.'<td>'.$value1.'</td>'.n;
	            $trows .= t.t.t.'<td>'.$value2.'</td>'.n;
	            $trows .= t.t.'</tr>'.n;
			}
		    $id++;
		}
		if ($trows) {
			$html .= $trows;
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="3" class="textual-data">'.JText::_('No data found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		// Collaboration Usage
		$html .= '<table summary="'.JText::_('Collaboration Usage').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 5: Collaboration Usage').'</caption>'.n;
		$html .= t.'<tbody>'.n;
		$sql = "SELECT a.label,b.value,b.valfmt FROM summary_collab AS a, summary_collab_vals AS b WHERE a.id=b.rowid AND b.period = '".$period."' AND b.datetime = '".$datetime."' ORDER BY a.id";
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		if ($results) {
			$cls = 'even';
	    	foreach ($results as $row)
			{
				$cls = ($cls == 'even') ? 'odd' : 'even';

				//$label = str_replace('{','<sup>',$row->label);
				//$label = str_replace('}','</sup>',$label);
				$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
				
	       		$html .= t.t.'<tr class="'.$cls.'">'.n;
	            $html .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
	            $html .= t.t.t.'<td>'. $this->fmt_result($row->value,$row->valfmt) .'</td>'.n;
	            $html .= t.t.'</tr>'.n;
			}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="2" class="textual-data">'.JText::_('No data found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;


		$html .= '<table summary="'.JText::_('User statistics by registered/unregistered').'">'.n;
		$html .= t.'<caption><a name="tot"></a>'.JText::_('Table 6: User statistics by registered/unregistered').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col" rowspan="2" colspan="2">'.JText::_('Users').'</th>'.n;
		$html .= t.t.t.'<th scope="col" rowspan="2" class="numerical-data">'.JText::_('Totals').'</th>'.n;
		$html .= t.t.t.'<th scope="colgroup" colspan="5">'.JText::_('Residence').'</th>'.n;
		$html .= t.t.t.'<th scope="colgroup" colspan="5">'.JText::_('Organization').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Identified').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('US').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Asia').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Europe').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="numerical-data">'.JText::_('Other').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data">'.JText::_('Identified').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data" abbr="'.JText::_('Education').'">'.JText::_('Edu.').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data" abbr="'.JText::_('Industry').'">'.JText::_('Ind.').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data" abbr="'.JText::_('Government').'">'.JText::_('Gov.').'</th>'.n;
		$html .= t.t.t.'<th scope="col" class="group numerical-data">'.JText::_('Other').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		$sql = "SELECT id, label, plot FROM summary_user WHERE id IN (1,6,7,8) ORDER BY id";
		$db->setQuery( $sql );
		$results = $db->loadObjectList();
		if ($results) {
			$i = 5;
			$cls = 'even';
			foreach ($results as $row)
			{
				//$label = str_replace('{','<sup>',$row->label);
				//$label = str_replace('}','</sup>',$label);
				$label = preg_replace('/\{(.*)\}/','<sup><a href="#fn\\1">\\1</a></sup>',$row->label);
				
				$cls = ($cls == 'even') ? 'odd' : 'even';
				if ($i == 5) {
					$cls = 'summary';
				}
				
				$sparkline = $this->getSparkline($db, $row->id, $period, $datetime);
				
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<th scope="row">'.trim($label).'</th>'.n;
				if ($row->plot == '1') {
					$img = $config->get('charts_path').DS.substr($datetime,0,7)."-".$period."-u".$row->id;
					if (is_file(JPATH_ROOT.DS.$img.'.gif')) {
						$html .= t.t.t.'<td><a href="'.$img.'.gif" title="DOM:users2'.$i.'" class="fixedImgTip" rel="external"><img src="'.$img.'thumb.gif" alt="" /></a><br /><div style="display:none;" id="users2'.$i.'"><img src="'.$img.'.gif" alt="" /></div></td>'.n;
					} else if (isset($sparkline)) {
						$html .= t.t.t.'<td>'.$sparkline.'</td>'.n;
					} else {
						$html .= t.t.t.'<td>&nbsp;</td>'.n;
					}
				} else if (isset($sparkline)) {
					$html .= t.t.t.'<td>'.$sparkline.'</td>'.n;
				} else {
					$html .= t.t.t.'<td>&nbsp;</td>';
				}
				$html .= $this->print_user_row($db,$row->id,$period,$datetime);
				$html .= t.t.'</tr>'.n;
				$i++;
			}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="13" class="textual-data">'.JText::_('No data found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		$html .= '<div class="footnotes">'.n;
		$html .= t.'<hr />'.n;
		$html .= t.'<ol>'.n;
		$html .= t.t.'<li id="fn1"><a name="fn1"></a>Sum of Registered Users<sup><a href="#fn2">2</a></sup>, Unregistered Interactive Users<sup><a href="#fn3">3</a></sup> and Unregistered Download Users<sup><a href="#fn4">4</a></sup></li>'.n;									
		$html .= t.t.'<li id="fn2"><a name="fn2"></a>Number of Users that logged in. User registration assigns a unique login to each individual user.</li>'.n;
		$html .= t.t.'<li id="fn3"><a name="fn3"></a>Number of Unregistered Users, identified by unique hosts/IPs, that had an active Session <sup><a href="#fn10">10</a></sup> without logging in. Does not include known web bots/crawlers.</li>'.n;
		$html .= t.t.'<li id="fn4"><a name="fn4"></a>Number of Unregistered users, identified by unique hosts/IPs, that had an active session of less than 15 minutes without logging in and downloaded a non-interactive resource such as PDF or podcast. Does not include web bots/crawlers.</li>'.n;	
		$html .= t.t.'<li id="fn5"><a name="fn5"></a>Number of Registered Users<sup><a href="#fn2">2</a></sup> that ran one or more simulation runs.</li>'.n;
		$html .= t.t.'<li id="fn6"><a name="fn6"></a>All Unregistered Users, identified by unique hosts/IPs, that had an active Session <sup><a href="#fn10">10</a></sup>. Does not include known web bots/crawlers.</li>'.n;
		$html .= t.t.'<li id="fn7"><a name="fn7"></a>All Unregistered users, identified by unique hosts/IPs that downloaded a non-interactive resource such as PDF or podcast. Does not include known web bots/crawlers.</li>'.n;					
		$html .= t.t.'<li id="fn8"><a name="fn8"></a>Sum of Simulation Users <sup><a href="#fn5">5</a></sup> + Unregistered Interactive Users <sup><a href="#fn3">3</a></sup> including web bots/crawlers.</li>'.n;
		$html .= t.t.'<li id="fn9"><a name="fn9"></a>Number of Simulation users that returned after a gap of 3 months.</li>'.n;						
		$html .= t.t.'<li id="fn10"><a name="fn10"></a>Begins when an IP is active on the site for at least 15 minutes. Ends when inactive for more than 30 minutes, including time spent viewing videos.</li>'.n;
		$html .= t.t.'<li id="fn11"><a name="fn11"></a> - </li>'.n;
		$html .= t.t.'<li id="fn12"><a name="fn12"></a>Based on MIT OCW metric of Visits: A visit is activity by a unique visitor delimitated by a 30 minute absence from the site on either side of the activity.<br />These visits correspond to unique visitors <sup><a href="#fn11">11</a></sup>. Does not include known web bots/crawlers</li>'.n;
		$html .= t.t.'<li id="fn13"><a name="fn13"></a>Number of Simulation sessions that were shared between 2 or more users.</li>'.n;
		$html .= t.'</ol>'.n;
		$html .= '</div><!-- / .footnotes -->'.n;

		return $html;
	}
}
?>
