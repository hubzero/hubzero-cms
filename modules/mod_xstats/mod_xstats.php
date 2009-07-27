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

//----------------------------------------------------------

class modXStats 
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------
	
	public function getUDBO()
	{
		static $instance;

		if (!is_object($instance)) {
			$xhub =& XFactory::getHub();
			$options['driver']   = $xhub->getCfg('statsDBDriver');
			$options['host']     = $xhub->getCfg('statsDBHost');
			$options['port']     = $xhub->getCfg('statsDBPort');
			$options['user']     = $xhub->getCfg('statsDBUsername');
			$options['password'] = $xhub->getCfg('statsDBPassword');
			$options['database'] = $xhub->getCfg('statsDBDatabase');
			$options['prefix']   = $xhub->getCfg('statsDBPrefix');

			$instance = &JDatabase::getInstance($options);
		}

		if (JError::isError($instance))
			return null;

		return $instance;
	}

	//-----------
	
	private function valformat($value, $format) 
	{
		if ($format == 1) {
			return(number_format($value));
		} elseif ($format == 2 || $format == 3) {
			if ($format == 2) {
				$min = round($value / 60);
			} else {
				$min = floor($value / 60);
				$sec = $value - ($min * 60);
			}
			$hr = floor($min / 60);
			$min -= ($hr * 60);
			$day = floor($hr / 24);
			$hr -= ($day * 24);
			if ($day == 1) {
				$day = '1 day, ';
			} elseif ($day > 1) {
				$day = number_format($day) . ' days, ';
			} else {
				$day = '';
			}
			if ($format == 2) {
				return(sprintf("%s%d:%02d", $day, $hr, $min));
			} else {
				return(sprintf("%s%d:%02d:%02d", $day, $hr, $min, $sec));
			}
		} else {
			return($value);
		}
	}
	
	//-----------
	
	private function getCurrent($totalid=11, $period=1) 
	{
		$udb =& modXStats::getUDBO();
		
		// Set hub...
		$hub = 1;
		
		// An array for storing totals
		$total = array();
		
		if ($udb) {
			$sql = "SELECT totals.valfmt, totals.name, totalvals.value 
					FROM totals, totalvals 
					WHERE totals.total = totalvals.total AND totalvals.hub = '" . $hub . "' 
					AND totalvals.total = '" . $totalid . "' 
					AND totalvals.period = '" . $period . "' 
					ORDER BY totalvals.datetime DESC LIMIT 1";
			$udb->setQuery( $sql );
			$results = $udb->loadObjectList();
			
			if ($results && count($results) > 0) {
				foreach ($results as $result) 
				{
					$total['name'] = preg_replace("/\\$\{([0-9]+)\}/", "", $result->name);
					$total['value'] = $result->value;
					$total['text'] = $this->valformat($result->value, $result->valfmt);
				}
			}
		}
		
		return $total;
	}
	
	//-----------
	
	public function display()
	{
		$stats = $this->getCurrent();

		if (isset($stats['value'])) {
			$hits = $stats['value'];
		} else {
			$hits = NULL;
		}
	
		$content = ' <span class="usage"> - <a href="/usage/">';
		if ($hits == NULL) {
			$content .= JText::_('MODXSTATS_VISITORS') .' ? '. JText::_('MODXSTATS_HITS_STAT');
		} else {
			$content .= JText::_('MODXSTATS_VISITORS') . $hits .' '. JText::_('MODXSTATS_HITS_STAT');
		}
		$content .= '</a></span>';

		return $content;
	}
}

$modxstats = new modXStats();
$modxstats->params = $params;

require( JModuleHelper::getLayoutPath('mod_xstats') );
?>
