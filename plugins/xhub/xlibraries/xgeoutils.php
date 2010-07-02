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

//-------------------------------------------------------------
// Contains functions used by multiple components
//-------------------------------------------------------------

class GeoUtils
{
	// Get a list of existing application sessions.
	function getGODBO()
	{
		static $instance;

		if (!is_object($instance)) {
			$xhub =& XFactory::getHub();
			
			$options = array();
			$options['driver']   = $xhub->getCfg('ipDBDriver');
			$options['host']     = $xhub->getCfg('ipDBHost');
			$options['port']     = $xhub->getCfg('ipDBPort');
			$options['user']     = $xhub->getCfg('ipDBUsername');
			$options['password'] = $xhub->getCfg('ipDBPassword');
			$options['database'] = $xhub->getCfg('ipDBDatabase');
			$options['prefix']   = $xhub->getCfg('ipDBPrefix');

			$instance =& JDatabase::getInstance($options);
		}

		if (JError::isError($instance)) {
			return null;
		}

		return $instance;
	}

	//-----------
	
	public function getcountries() 
	{
		$countries = array();

		$gdb =& GeoUtils::getGODBO();

		$sql = "SELECT code, name FROM countries ORDER BY name";
		$gdb->setQuery( $sql );
		$results = $gdb->loadObjectList();

		if ($results) {
			foreach ($results as $row) 
			{
				if ($row->code <> "-" && $row->name <> "-") {
					array_push($countries, array('code' => strtolower($row->code), 'id' => $row->code, 'name' => $row->name));
				}
			}
		}

		return $countries;
	}
	
	//-----------
	
	public function getCountriesByContinent($continent='') 
	{
		if (!$continent) {
			return array();
		}
		
		$gdb =& GeoUtils::getGODBO();

		$sql = 'SELECT DISTINCT country FROM country_continent WHERE LOWER(continent) = "'.strtolower($continent).'"';
		$gdb->setQuery( $sql );
		return $gdb->loadResultArray();
	}

	//-----------

	public function getcountry($code='') 
	{
		$name = '';
		if ($code) {
			$gdb =& GeoUtils::getGODBO();
			
			$sql = "SELECT name FROM countries WHERE code = '" . $code . "'";
			$gdb->setQuery( $sql );
			$name = stripslashes($gdb->loadResult());
		}
		return $name;
	}

	//-----------

	public function ipcountry($ip='') 
	{
		$country = '';
		if ($ip) {
			$gdb =& GeoUtils::getGODBO();

			$sql = "SELECT LOWER(countrySHORT) FROM ipcountry WHERE ipFROM <= INET_ATON('" . $ip . "') AND ipTO >= INET_ATON('" . $ip . "')";
			$gdb->setQuery( $sql );
			$country = stripslashes($gdb->loadResult());
		}
		return $country;
	}

	//-----------

	public function is_d1nation($country) 
	{
		$d1nation = false;
		if ($country) {
			$gdb =& GeoUtils::getGODBO();
			
			$sql = "SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER('" . $country . "') AND countrygroup = 'D1'";
			$gdb->setQuery( $sql );
			$c = $gdb->loadResult();
			if ($c > 0) {
				$d1nation = true;
			}
		}
		return $d1nation;
	}

	//-----------

	public function is_iplocation($ip, $location) 
	{
		$iplocation = false;
		if ($ip && $location) {
			$gdb =& GeoUtils::getGODBO();
			
			$sql = "SELECT COUNT(*) FROM iplocation WHERE ipfrom <= INET_ATON('" . $ip . "') AND ipto >= INET_ATON('" . $ip . "') AND LOWER(location) = LOWER('" . $location . "')";
			$gdb->setQuery( $sql );
			$c = $gdb->loadResult();
			if ($c > 0) {
				$iplocation = true;
			}
		}
		return $iplocation;
	}
}
?>