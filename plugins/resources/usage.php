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
	public function plgResourcesUsage(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'usage' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onResourcesAreas( $resource ) 
	{
		if ($resource->_type->_params->get('plg_usage')) {
			$areas = array(
				'usage' => JText::_('PLG_RESOURCES_USAGE')
			);
		} else {
			$areas = array();
		}
		return $areas;
	}

	//-----------

	public function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
			
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		// Display only for tools
		if ($resource->type != 7) {
			return $arr;
		}

		// Check if we have a needed database table
		$database =& JFactory::getDBO();
		
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'resource_stats_tools';

		if ($resource->alias) {
			$url = JRoute::_('index.php?option='.$option.'&alias='.$resource->alias.'&active=usage');
		} else {
			$url = JRoute::_('index.php?option='.$option.'&id='.$resource->id.'&active=usage');
		}

		if (!in_array($table,$tables)) {
			$arr['html'] = '<p class="error">'. JText::_('PLG_RESOURCES_USAGE_MISSING_TABLE') .'</p>';
			$arr['metadata'] = '<p class="usage"><a href="'.$url.'">'.JText::_('PLG_RESOURCES_USAGE_DETAILED').'</a></p>';
			return $arr;
		}
		
		// Get/set some variables
		$dthis = JRequest::getVar('dthis',date('Y').'-'.date('m'));
		$period = JRequest::getInt('period', $this->_params->get('period',14));

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'stats.php' );
		$stats = new ResourcesStatsTools( $database );
		$stats->loadStats( $resource->id, $period, $dthis );

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html') {
			ximport('Hubzero_Document');
			Hubzero_Document::addComponentStylesheet('com_usage');
			
			// Instantiate a view
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'resources',
					'element'=>'usage',
					'name'=>'browse'
				)
			);

			// Pass the view some info
			$view->option = $option;
			$view->resource = $resource;
			$view->stats = $stats;
			$view->chart_path = $this->_params->get('chart_path','');
			$view->map_path = $this->_params->get('map_path','');
			$view->dthis = $dthis;
			$view->period = $period;
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		if ($rtrn == 'all' || $rtrn == 'metadata') {
			$arr['metadata'] = '<p class="usage"><a href="'.$url.'">'.JText::sprintf('PLG_RESOURCES_USAGE_NUM_USERS',$stats->users).'</a></p>';
		}

		return $arr;
	}
	
	//-----------

	public function timeUnits($time) 
	{
		if ($time < 60) {
			$data = round($time,2).' '.JText::_('PLG_RESOURCES_USAGE_SECONDS');
		} else if ($time > 60 && $time < 3600) {
			$data = round(($time/60), 2).' '.JText::_('PLG_RESOURCES_USAGE_MINUTES');
		} else if ($time >= 3600 && $time < 86400) {
			$data = round(($time/3600), 2).' '.JText::_('PLG_RESOURCES_USAGE_HOURS');
		} else if ($time >= 86400) {
			$data = round(($time/86400),2).' '.JText::_('PLG_RESOURCES_USAGE_DAYS');
		}

		return $data;
	}
	
	//-----------
	
	public function dropDownDates(&$db, $period, $s_top, $dthis) 
	{
		$months = array( "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec");
		$monthsReverse = array_reverse($months, TRUE);
		$cur_year = floor(date("Y"));
		$cur_month = floor(date("n"));
		$year_data_start = 2000;

		$html = '<select name="dthis">'."\n";
		switch ($period) 
		{
			case '3':
				$qtd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$qtd_found && plgResourcesUsage::checkForData($value, 3)) {
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
						$html .= ' ' . $cur_year . ' - ' . $month . ' ' . $cur_year . '</option>'."\n";
						$qtd_found = 1;
					}
				}
				for ($j = $cur_year; $j >= $year_data_start; $j--) 
				{
					for ($i = 12; $i > 0; $i = $i - 3) 
					{
						$value = $j . '-' . sprintf("%02d", $i);
						if (plgResourcesUsage::checkForData($value, 3)) {
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
							$html .= ' ' . $j . '</option>'."\n";
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
						if (plgResourcesUsage::checkForData($value, 12)) {
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
						   	$html .= ' - ' . $month . ' ' . $i . '</option>'."\n";
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
						if (plgResourcesUsage::checkForData($value, 1)) {
							$html .= '<option value="' . $value . '"';
							if ($value == $dthis) {
								$html .= ' selected="selected"';
							}
							$html .= '>' . $month . ' ' . $i . '</option>'."\n";
						}
					}
				}
			break;
			
			case '0':
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && plgResourcesUsage::checkForData($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - ' . $month . ' ' . $cur_year . '</option>'."\n";
						$ytd_found = 1;
					}
				}
				for ($i = $cur_year - 1; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-12';
					if (plgResourcesUsage::checkForData($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Jan - Dec ' . $i . '</option>'."\n";
					}
				}
			break;
			
			case '13':
				$ytd_found = 0;
				foreach ($monthsReverse as $key => $month) 
				{
					$value = $cur_year . '-' . $key;
					if (!$ytd_found && plgResourcesUsage::checkForData($value, 0)) {
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
						$html .= ' - ' . $month . ' ' . $cur_year . '</option>'."\n";
						$ytd_found = 1;
					}
				}
				for ($i = $full_year; $i >= $year_data_start; $i--) 
				{
					$value = $i . '-09';
					if (plgResourcesUsage::checkForData($value, 0)) {
						$html .= '<option value="' . $value . '"';
						if ($value == $dthis) {
							$html .= ' selected="selected"';
						}
						$html .= '>Oct ';
						$html .= $i - 1;
						$html .= ' - Sep ' . $i . '</option>'."\n";
					}
				}
			break;
		}
		$html .= '</select>'."\n";
		
		return $html;
	}
	
	//-----------
	
	public function checkForData($yearmonth, $period) 
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
}
