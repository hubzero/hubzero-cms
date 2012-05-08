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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Geo'
 * 
 * Long description (if any) ...
 */
class Hubzero_Geo
{
	// Get a list of existing application sessions.

	/**
	 * Short description for 'getGODBO'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function getGODBO()
	{
		static $instance;

		if (!is_object($instance)) {
			$xhub =& Hubzero_Factory::getHub();

			$options = array();
			$options['driver']   = $xhub->getCfg('ipDBDriver');
			$options['host']     = $xhub->getCfg('ipDBHost');
			$options['port']     = $xhub->getCfg('ipDBPort');
			$options['user']     = $xhub->getCfg('ipDBUsername');
			$options['password'] = $xhub->getCfg('ipDBPassword');
			$options['database'] = $xhub->getCfg('ipDBDatabase');
			$options['prefix']   = $xhub->getCfg('ipDBPrefix');

			if (!($options['user'] = $xhub->getCfg('ipDBUsername')))
				return null;

			$instance =& JDatabase::getInstance($options);
		}

		if (JError::isError($instance)) {
			return null;
		}

		return $instance;
	}

	/**
	 * Short description for 'getcountries'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function getcountries()
	{
		$countries = array();

		if (!($gdb = Hubzero_Geo::getGODBO()))
			return $countries;

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

	/**
	 * Short description for 'getCountriesByContinent'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $continent Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getCountriesByContinent($continent='')
	{
		if (!$continent) {
			return array();
		}

		$gdb =& Hubzero_Geo::getGODBO();

		$sql = 'SELECT DISTINCT country FROM country_continent WHERE LOWER(continent) = "'.strtolower($continent).'"';
		$gdb->setQuery( $sql );
		return $gdb->loadResultArray();
	}

	/**
	 * Short description for 'getcountry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $code Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getcountry($code='')
	{
		$name = '';
		if ($code) {
			$gdb =& Hubzero_Geo::getGODBO();

			$sql = "SELECT name FROM countries WHERE code = '" . $code . "'";
			$gdb->setQuery( $sql );
			$name = stripslashes($gdb->loadResult());
		}
		return $name;
	}

	/**
	 * Short description for 'ipcountry'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $ip Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function ipcountry($ip='')
	{
		$country = '';
		if ($ip) {
			$gdb =& Hubzero_Geo::getGODBO();

			if (empty($gdb)) {
				return '' '' '';
			}

			$sql = "SELECT LOWER(countrySHORT) FROM ipcountry WHERE ipFROM <= INET_ATON('" . $ip . "') AND ipTO >= INET_ATON('" . $ip . "')";
			$gdb->setQuery( $sql );
			$country = stripslashes($gdb->loadResult());
		}
		return $country;
	}

	/**
	 * Short description for 'is_d1nation'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $country Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_d1nation($country)
	{
		$d1nation = false;
		if ($country) {
			$gdb =& Hubzero_Geo::getGODBO();

			if (empty($gdb))
				return false;

			$sql = "SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER('" . $country . "') AND countrygroup = 'D1'";
			$gdb->setQuery( $sql );
			$c = $gdb->loadResult();
			if ($c > 0) {
				$d1nation = true;
			}
		}
		return $d1nation;
	}

	/**
	 * Short description for 'is_e1nation'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $country Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_e1nation($country)
	{
		$e1nation = false;
		if ($country) {
			$gdb =& Hubzero_Geo::getGODBO();

			if (empty($gdb))
				return false;

			$sql = "SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER('" . $country . "') AND countrygroup = 'E1'";
			$gdb->setQuery( $sql );
			$c = $gdb->loadResult();
			if ($c > 0) {
				$e1nation = true;
			}
		}
		return $e1nation;
	}

	/**
	 * Short description for 'is_iplocation'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $ip Parameter description (if any) ...
	 * @param      string $location Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function is_iplocation($ip, $location)
	{
		$iplocation = false;
		if ($ip && $location) {
			$gdb =& Hubzero_Geo::getGODBO();

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

