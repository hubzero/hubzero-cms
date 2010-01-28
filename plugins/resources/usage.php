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
JPlugin::loadLanguage( 'plg_resources_usage' );
	
//-----------

class plgResourcesUsage extends JPlugin
{
	function plgResourcesUsage(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'usage' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesAreas( $resource ) 
	{
		if ($resource->type != 7) {
			$areas = array();
		} else {
			$areas = array(
				'usage' => JText::_('USAGE')
			);
		}
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools
		if ($resource->type != 7) {
			return array('html'=>'','metadata'=>'');
		}

		$database =& JFactory::getDBO();
		
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'resource_stats_tools';

		if ($resource->alias) {
			$url = JRoute::_('index.php?option='.$option.a.'alias='.$resource->alias.a.'active=usage');
		} else {
			$url = JRoute::_('index.php?option='.$option.a.'id='.$resource->id.a.'active=usage');
		}

		if (!in_array($table,$tables)) {
			$arr['html'] = ResourcesHtml::error( JText::_('Required database table not found.') );
			$arr['metadata'] = '<p class="usage"><a href="'.$url.'">'.JText::_('DETAILED_USAGE').'</a></p>'.n;
			return $arr;
		}
		
		// Get/set some variables
		$dthis = JRequest::getVar('dthis',date('Y').'-'.date('m'));
		$period = JRequest::getInt('period', $this->_params->get('period',14));
		$chart_path = $this->_params->get('chart_path','');
		$map_path = $this->_params->get('map_path','');
		$cls = 'even';

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'resources.stats.php' );
		$stats = new ResourcesStatsTools( $database );
		$stats->loadStats( $resource->id, $period, $dthis );

		$html = '';
		if ($rtrn == 'all' || $rtrn == 'html') {
			ximport('xdocument');
			XDocument::addComponentStylesheet('com_usage');
			
			$img1 = $chart_path.$dthis.'-'.$period.'-'.$resource->id.'-Users.gif';
			$img2 = $chart_path.$dthis.'-'.$period.'-'.$resource->id.'-Jobs.gif';
			
			$sbjt  = '<div id="statistics">'.n;
			if ((is_file(JPATH_ROOT.$img1) && !is_file(JPATH_ROOT.$img2)) || (!is_file(JPATH_ROOT.$img1) && is_file(JPATH_ROOT.$img2))) {
				$sbjt .= t.'<div class="two columns first">'.n;
			}
			$sbjt .= t.'<table summary="'.JText::_('USAGE_TBL_1_CAPTION').'">'.n;
			$sbjt .= t.t.'<caption>'.JText::_('USAGE_TBL_1_CAPTION').'</caption>'.n;
			$sbjt .= t.t.'<thead>'.n;
			$sbjt .= t.t.t.'<tr>'.n;
			$sbjt .= t.t.t.t.'<th scope="col" class="textual-data">'.JText::_('USAGE_COL_ITEM').'</th>'.n;
			$sbjt .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('USAGE_COL_AVERAGE').'</th>'.n;
			$sbjt .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('USAGE_COL_TOTAL').'</th>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$sbjt .= t.t.'</thead>'.n;
			$sbjt .= t.t.'<tbody>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_SIMULATION_USERS').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>-</td>'.n;
			$sbjt .= t.t.t.t.'<td>'.number_format($stats->users).'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_INTERACTIVE_SESSIONS').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>-</td>'.n;
			$sbjt .= t.t.t.t.'<td>'.number_format($stats->sessions).'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$i = 0;
			$img = $chart_path.$dthis.'-'.$period.'-'.$resource->id.'-Simulations.gif';
			if ($stats->simulations == $stats->jobs) {
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
				if (is_file(JPATH_ROOT.$img)) {
					$sbjt .= t.t.t.t.'<th scope="row"><a href="'.$img.'" title="DOM:users1'.$i.'" class="fixedResourceTip" rel="external">'.JText::_('USAGE_SIMULATION_SESSIONS').':</a><div style="display:none;" id="users1'.$i.'"><img src="'.$img.'" alt="" /></div></th>'.n;
				} else {
					$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_SIMULATION_SESSIONS').':</th>'.n;
				}
				$sbjt .= t.t.t.t.'<td>-</td>'.n;
				$sbjt .= t.t.t.t.'<td>'.number_format($stats->simulations).'</td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
			} else {
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
				if (is_file(JPATH_ROOT.$img)) {
					$sbjt .= t.t.t.t.'<th scope="row"><a href="'.$img.'" title="DOM:users1'.$i.'" class="fixedResourceTip" rel="external">'.JText::_('USAGE_SIMULATION_SESSIONS').':</a><div style="display:none;" id="users1'.$i.'"><img src="'.$img.'" alt="" /></div></th>'.n;
				} else {
					$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_SIMULATION_SESSIONS').':</th>'.n;
				}
				$sbjt .= t.t.t.t.'<td>-</td>'.n;
				$sbjt .= t.t.t.t.'<td>'.number_format($stats->simulations).'</td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
				$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_SIMULATION_RUNS').':</th>'.n;
				$sbjt .= t.t.t.t.'<td>-</td>'.n;
				$sbjt .= t.t.t.t.'<td>'.number_format($stats->jobs).'</td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
			}
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_WALL_TIME').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->avg_wall).'</td>'.n;
			$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->tot_wall).'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_CPU_TIME').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->avg_cpu).'</td>'.n;
			$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->tot_cpu).'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('USAGE_INTERACTION_TIME').':</th>'.n;
			$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->avg_view).'</td>'.n;
			$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->tot_view).'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
			/*if ($stats->tot_wait) {
				$sbjt .= t.t.t.'<tr>'.n;
				$sbjt .= t.t.t.t.'<th scope="row">'.JText::_('Wait Time').':</th>'.n;
				$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->avg_wait).'</td>'.n;
				$sbjt .= t.t.t.t.'<td>'.$this->time_units($stats->tot_wait).'</td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
			}*/
			$sbjt .= t.t.'</tbody>'.n;
			$sbjt .= t.'</table>'.n;
			
			if (is_file(JPATH_ROOT.$img1) && is_file(JPATH_ROOT.$img2)) {
				$sbjt .= t.'<div class="two columns first">'.n;
				$sbjt .= t.t.'<p style="text-align: center;"><img src="'.$img1.'" alt="" /></p>'.n;
				$sbjt .= t.'</div>'.n;
				$sbjt .= t.'<div class="two columns second">'.n;
				$sbjt .= t.t.'<p style="text-align: center;"><img src="'.$img2.'" alt="" /></p>'.n;
				$sbjt .= t.'</div>'.n;
			} else if ((is_file(JPATH_ROOT.$img1) && !is_file(JPATH_ROOT.$img2)) || (!is_file(JPATH_ROOT.$img1) && is_file(JPATH_ROOT.$img2))) {
				$sbjt .= t.'</div>'.n;
				$sbjt .= t.'<div class="two columns second">'.n;
				if (is_file(JPATH_ROOT.$img1)) {
					$sbjt .= t.t.'<p style="text-align: center;"><img src="'.$img1.'" alt="" /></p>'.n;
				} else {
					$sbjt .= t.t.'<p style="text-align: center;"><img src="'.$img2.'" alt="" /></p>'.n;
				}
				$sbjt .= t.'</div>'.n;
			}
			$sbjt .= t.'<div class="clear"></div>'.n;

			$topvals = new ResourcesStatsToolsTopvals( $database );

			$toporgs = $topvals->getTopCountryRes( $stats->id, 3 );
			$topdoms = $topvals->getTopCountryRes( $stats->id, 2 );
			$topcountries = $topvals->getTopCountryRes( $stats->id, 1 );

			$sbjt .= $this->table( $toporgs, 'USAGE_COL_TYPE', 'USAGE_TBL_2_CAPTION' );
			$sbjt .= $this->table( $topcountries, 'USAGE_COL_COUNTRY', 'USAGE_TBL_3_CAPTION' );
			$sbjt .= $this->table( $topdoms, 'USAGE_COL_DOMAINS', 'USAGE_TBL_4_CAPTION' );
			
			$juser =& JFactory::getUser();
			if (!$juser->get('guest')) {
				// Check if they're a site admin (from Joomla)
				if ($juser->authorize($option, 'manage')) {
					$topvalsusers = new ResourcesStatsToolsUsers($database);
					$topusers = $topvalsusers->getTopUsersRes($resource->id, $dthis, $period, '3');
					
					$sbjt .= t.'<table summary="'.JText::_('USAGE_TBL_5_CAPTION').'">'.n;
					$sbjt .= t.t.'<caption>'.JText::_('USAGE_TBL_5_CAPTION').'</caption>'.n;
					$sbjt .= t.t.'<thead>'.n;
					$sbjt .= t.t.t.'<tr>'.n;
					$sbjt .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('USAGE_COL_NUM').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_USER').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_ORGANIZATION').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_EMAIL').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_INTERACTIVE_SESSIONS').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_SIMULATION_SESSIONS').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_SIMULATION_RUNS').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_TOTAL_WALL_TIME').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_TOTAL_CPU_TIME').'</th>'.n;
					$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('USAGE_COL_TOTAL_INTERACTION_TIME').'</th>'.n;
					$sbjt .= t.t.t.'</tr>'.n;
					$sbjt .= t.t.'</thead>'.n;
					$sbjt .= t.t.'<tbody>'.n;
					if ($topusers) {
						$cls = 'even';
						$rank = 1;
						foreach ($topusers as $row) 
						{
							if ($row->name == '?') {
								$row->name = JText::_('USAGE_UNIDENTIFIED');
							}

							$cls = ($cls == 'even') ? 'odd' : 'even';

							$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
							$sbjt .= t.t.t.t.'<td>'.$rank.'</td>'.n;
							$sbjt .= t.t.t.t.'<td class="textual-data">'.$row->name.' ('.$row->user.')</td>'.n;
							$sbjt .= t.t.t.t.'<td class="textual-data">'.$row->organization.'</td>'.n;
							$sbjt .= t.t.t.t.'<td class="textual-data">'.$row->email.'</td>'.n;
							$sbjt .= t.t.t.t.'<td>'.$row->sessions.'</td>'.n;
							$sbjt .= t.t.t.t.'<td>'.number_format($row->simulations).'</td>'.n;
							$sbjt .= t.t.t.t.'<td>'.number_format($row->jobs).'</td>'.n;
							$sbjt .= t.t.t.t.'<td>'.$this->time_units($row->tot_wall).'</td>'.n;
							$sbjt .= t.t.t.t.'<td>'.$this->time_units($row->tot_cpu).'</td>'.n;
							$sbjt .= t.t.t.t.'<td>'.$this->time_units($row->tot_view).'</td>'.n;
							$sbjt .= t.t.t.'</tr>'.n;
							$rank++;
						}
					} else {
						$sbjt .= t.t.t.'<tr class="odd">'.n;
						$sbjt .= t.t.t.t.'<td colspan="10" class="textual-data">'.JText::_('USAGE_NO_DATA_AVAILABLE').'</td>'.n;
						$sbjt .= t.t.t.'</tr>'.n;
					}
					$sbjt .= t.t.'</tbody>'.n;
					$sbjt .= t.'</table>'.n;
				}
			}

			$tool_map = $map_path.$resource->id;
			if (file_exists(JPATH_ROOT.$tool_map.'.gif')) {
				$sbjt .= '<p>'.JText::sprintf('USAGE_MAP_EXPLANATION',$resource->title).'</p>'.n;
				$sbjt .= '<p><a href="'.$tool_map.'.png" title="'.JText::_('USAGE_MAP_LARGER').'"><img src="'.$tool_map.'.gif" alt="'.JText::_('USAGE_MAP').'" /></a></p>'.n;
			}
			$sbjt .= '</div>'.n;
			
			$aside  = '<fieldset>'.n;
			$aside .= t.'<label>'.n;
			$aside .= t.t.JText::_('Time Period').n;
			$aside .= t.t.$this->drop_down_dates( $database, $period, $resource->id, $dthis );
			$aside .= t.'</label>'.n;
			$aside .= t.'<input type="submit" value="'.JText::_('Go').'" />'.n;
			$aside .= '</fieldset>'.n;
			//$aside = '<p class="info">'.JText::_('USAGE_EXPLANATION').'</p>';
			$html  = ResourcesHtml::hed(3,'<a name="usage"></a>'.JText::_('USAGE')).n;
			$html .= $this->navlinks($period, $dthis, $option, $resource);
			$html .= '<form method="get" action="'.$url.'">'.n;
			$html .= ResourcesHtml::div($aside, 'timeperiod');
			$html .= ResourcesHtml::div($sbjt);
			$html .= '</form>'.n;
		}

		$metadata = '';
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			$metadata  = '<p class="usage"><a href="'.$url.'">'.JText::sprintf('NUM_USERS',$stats->users).'</a></p>'.n;
		}
		
		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
	
	//-----------

	function table( $rows, $th, $caption )
	{
		$tot = '';
		$total = '';
		$cls = 'even';

		$html  = t.'<table summary="'.JText::_($caption).'">'.n;
		$html .= t.t.'<caption>'.JText::_($caption).'</caption>'.n;
		$html .= t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('USAGE_COL_NUM').'</th>'.n;
		$html .= t.t.t.t.'<th scope="col">'.JText::_($th).'</th>'.n;
		$html .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('USAGE_COL_USERS').'</th>'.n;
		$html .= t.t.t.t.'<th scope="col" class="numerical-data">'.JText::_('USAGE_COL_PERCENT').'</th>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.'</thead>'.n;
		$html .= t.t.'<tbody>'.n;
		if ($rows) {
			foreach ($rows as $row) 
			{
				if ($row->name == '?') {
					$row->name = JText::_('USAGE_UNIDENTIFIED');
				}

				if ($row->rank == '0') {
					$total = $row->value;
					if ($total) {
						$tot .= t.t.t.'<tr class="summary">'.n;
						$tot .= t.t.t.t.'<td> </td>'.n;
						$tot .= t.t.t.t.'<td class="textual-data">'.$row->name.'</td>'.n;
						$tot .= t.t.t.t.'<td>'.number_format($row->value).'</td>'.n;
						$tot .= t.t.t.t.'<td>'.round((($row->value/$total)*100),2).'</td>'.n;
						$tot .= t.t.t.'</tr>'.n;
					}
				} else {
					$cls = ($cls == 'even') ? 'odd' : 'even';

					$html .= t.t.t.'<tr class="'.$cls.'">'.n;
					$html .= t.t.t.t.'<td>'.$row->rank.'</td>'.n;
					$html .= t.t.t.t.'<td class="textual-data">'.$row->name.'</td>'.n;
					$html .= t.t.t.t.'<td>'.number_format($row->value).'</td>'.n;
					$html .= t.t.t.t.'<td>'.round((($row->value/$total)*100),2).'</td>'.n;
					$html .= t.t.t.'</tr>'.n;
				}
			}
		}
		$html .= $tot;
		$html .= t.t.'</tbody>'.n;
		$html .= t.'</table>'.n;

		return $html;
	}
	
	//-----------

	function time_units($time) 
	{
		if ($time < 60) {
			$data = round($time,2).' '.JText::_('SECONDS');
		} else if ($time > 60 && $time < 3600) {
			$data = round(($time/60), 2).' '.JText::_('MINUTES');
		} else if ($time >= 3600 && $time < 86400) {
			$data = round(($time/3600), 2).' '.JText::_('HOURS');
		} else if ($time >= 86400) {
			$data = round(($time/86400),2).' '.JText::_('DAYS');
		}

		return $data;
	}
	
	private function drop_down_dates(&$db, $period, $s_top, $dthis) 
	{
		$months = array( "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
		$monthsReverse = array_reverse($months, TRUE);
		$cur_year = floor(date("Y"));
		$cur_month = floor(date("n"));
		$year_data_start = 2000;

		$html = '<select name="dthis">'.n;
		switch ($period) 
		{
			case '3':
				$qtd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$qtd_found && $this->check_for_data($value, 3)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>';
						if ($key <= 3) {
							$key = 0;
							$html .= 'Jan';
						} elseif ($key <= 6) {
							$key = 3;
							$html .= 'Apr';
						} elseif ($key <= 9) {
							$key = 6;
							$html .= 'Jul';
						} else {
							$key = 9;
							$html .= 'Oct';
						}
						$html .= ' ' . $cur_year . ' - ' . $month . ' ' . $cur_year . '</option>'.n;
						$qtd_found = 1;
					}
				}
				for ($j = $cur_year; $j >= $year_data_start; $j--) 
				{
					for ($i = 12; $i > 0; $i = $i - 3) 
					{
						$value = $j . '-' . sprintf("%02d", $i);
						if ($this->check_for_data($value, 3)) {
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis) {
								$html .= ' selected="selected"';
							}
							$html .= '>';
							switch ($i) 
							{
								case 3:  $html .= 'Jan'; break;
								case 6:  $html .= 'Apr'; break;
								case 9:  $html .= 'Jul'; break;
								default: $html .= 'Oct'; break;
							}
							$html .= ' ' . $j . ' - ';
							switch ($i) 
							{
								case 3:  $html .= 'Mar'; break;
								case 6:  $html .= 'Jun'; break;
								case 9:  $html .= 'Sep'; break;
								default: $html .= 'Dec'; break;
							}
							$html .= ' ' . $j . '</option>'.n;
						}
					}
				}
			break;
			
			case '12':
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
						if ($this->check_for_data($value, 12)) {
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis) {
								$html .= ' selected="selected"';
							}
							$html .= '>' . $nextmonth . ' ';
							if ($key == 12) {
								$html .= $i;
							} else {
								$html .= $i - 1;
							}
						   	$html .= ' - ' . $month . ' ' . $i . '</option>'.n;
						}
					}
				}
			break;
			
			case '1':
			case '14':
				for ($i = $cur_year; $i >= $year_data_start; $i--) 
				{
					foreach ($monthsReverse as $key => $month) 
					{
						$value = $i . '-' . $key;
						if ($this->check_for_data($value, 1)) {
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis) {
								$html .= ' selected="selected"';
							}
							$html .= '>' . $month . ' ' . $i . '</option>'.n;
						}
					}
				}
			break;
			
			case '0':
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - ' . $month . ' ' . $cur_year . '</option>'.n;
						$ytd_found = 1;
					}
				}
				for ($i = $cur_year - 1; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-12';
					if ($this->check_for_data($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - Dec ' . $i . '</option>'.n;
					}
				}
			break;
			
			case '13':
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && $this->check_for_data($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
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
						$html .= ' - ' . $month . ' ' . $cur_year . '</option>'.n;
						$ytd_found = 1;
					}
				}
				for ($i = $full_year; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-09';
					if ($this->check_for_data($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						$html .= $i - 1;
						$html .= ' - Sep ' . $i . '</option>'.n;
					}
				}
			break;
		}
		$html .= '</select>'.n;
		
		return $html;
	}
	
	private function check_for_data($yearmonth, $period) 
	{
		$database =& JFactory::getDBO();
		
	    $sql = "SELECT COUNT(datetime) AS cnt FROM #__resource_stats_tools WHERE datetime LIKE '" . mysql_escape_string($yearmonth) . "-%' AND period = '" . mysql_escape_string($period) . "'";
		$database->setQuery( $sql );
		$result = $database->loadResult();
		
		if ($result && $result > 0) {
			return(true);
		}
	
		return(false);
	}
	
	//-----------

	private function navlinks($period='12', $dthis='', $option, $resource) 
	{
		if ($resource->alias) {
			$url = 'index.php?option='.$option.a.'alias='.$resource->alias.a.'active=usage';
		} else {
			$url = 'index.php?option='.$option.a.'id='.$resource->id.a.'active=usage';
		}
		$html  = '<div id="sub-sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$html .= t.t.'<li';  
		if ($period == '14') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($url.a.'period=14'.a.'dthis='.$dthis).'"><span>'.JText::_('USAGE_PERIOD_OVERALL').'</span></a></li>'.n;
		$html .= t.t.'<li';    
		if ($period == 'prior12' || $period == '12') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($url.a.'period=12'.a.'dthis='.$dthis).'"><span>'.JText::_('USAGE_PERIOD_PRIOR12').'</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($period == 'month' || $period == '1') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($url.a.'period=1'.a.'dthis='.$dthis).'"><span>'.JText::_('USAGE_PERIOD_MONTH').'</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($period == 'qtr' || $period == '3') {
			$html .= ' class="active"';
		}
	    $html .= '><a href="'.JRoute::_($url.a.'period=3'.a.'dthis='.$dthis).'"><span>'.JText::_('USAGE_PERIOD_QTR').'</span></a></li>'.n;
		$html .= t.t.'<li';  
		if ($period == 'year' || $period == '0') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($url.a.'period=0'.a.'dthis='.$dthis).'"><span>'.JText::_('USAGE_PERIOD_YEAR').'</span></a></li>'.n;
		$html .= t.t.'<li';  
		if ($period == 'fiscal' || $period == '13') {
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_($url.a.'period=13'.a.'dthis='.$dthis).'"><span>'.JText::_('USAGE_PERIOD_FISCAL').'</span></a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div>'.n;

	    return $html;
	}
}
