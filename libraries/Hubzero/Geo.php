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
 * Helper class for getting geolocation information
 */
class Hubzero_Geo
{
	/**
	 * Get the geo database
	 * 
	 * @return     mixed JDatabase object upon success, null if error
	 */
	public function getGeoDBO()
	{
		static $instance;

		if (!is_object($instance)) 
		{
			$geodb_params = JComponentHelper::getParams('com_system');

			$options = array();
			$options['driver']   = $geodb_params->get('geodb_driver', 'mysql');
			$options['host']     = $geodb_params->get('geodb_host', 'localhost');
			$options['port']     = $geodb_params->get('geodb_port', '');
			$options['user']     = $geodb_params->get('geodb_user', '');
			$options['password'] = $geodb_params->get('geodb_password', '');
			$options['database'] = $geodb_params->get('geodb_database', '');
			$options['prefix']   = $geodb_params->get('geodb_prefix', '');

			if (empty($options['database']) || empty($options['user']) || empty($options['password']))
			{
				return null;
			}

			try
			{
				$instance =& JDatabase::getInstance($options);
			}
			catch (Exception $e)
			{
				$instance = false;
			}
		}

		if (JError::isError($instance)) 
		{
			return null;
		}

		return $instance;
	}

	/**
	 * Get a list of countries and their coutnry code
	 * 
	 * @return     array
	 */
	public function getcountries()
	{
		$countries = array();

		if (!($gdb = Hubzero_Geo::getGeoDBO()))
		{
			return $countries;
		}

		$gdb->setQuery("SELECT code, name FROM countries ORDER BY name");
		$results = $gdb->loadObjectList();

		if ($results) 
		{
			foreach ($results as $row)
			{
				if ($row->code != '-' && $row->name != '-') 
				{
					array_push($countries, array(
						'code' => strtolower($row->code), 
						'id'   => $row->code, 
						'name' => $row->name
					));
				}
			}
		}

		return $countries;
	}

	/**
	 * Get a list of countries by continent
	 * 
	 * @param      string $continent Parameter description (if any) ...
	 * @return     array
	 */
	public function getCountriesByContinent($continent='')
	{
		if (!$continent || !($gdb = Hubzero_Geo::getGeoDBO())) 
		{
			return array();
		}

		$gdb->setQuery("SELECT DISTINCT country FROM country_continent WHERE LOWER(continent) ='" . strtolower($continent) . "'");
		return $gdb->loadResultArray();
	}

	/**
	 * Get a list of countries by continent
	 * 
	 * @param      array $names Parameter description (if any) ...
	 * @return     array
	 */
	public function getCodesByNames($names=array())
	{
		if (!($gdb = Hubzero_Geo::getGeoDBO())) 
		{
			return array();
		}
		$names = array_map('strtolower', $names);

		$gdb->setQuery("SELECT DISTINCT name, code FROM countries WHERE LOWER(name) IN ('" . implode("','", $names) . "')");
		$values = $gdb->loadAssocList('name');
		if (!is_array($values))
		{
			$values = array();
		}
		return $values;
	}

	/**
	 * Get country based on short code
	 * 
	 * @param      string $code Short code (ex: us, de, fr, jp)
	 * @return     string 
	 */
	public function getCodeByName($name='')
	{
		$code = '';
		if ($name) 
		{
			if (!($gdb = Hubzero_Geo::getGeoDBO()))
			{
				return $code;
			}

			$gdb->setQuery("SELECT code FROM countries WHERE LOWER(name) = " . $gdb->Quote(strtolower($name)));
			$code = stripslashes($gdb->loadResult());
		}
		return $code;
	}

	/**
	 * Get country based on short code
	 * 
	 * @param      string $code Short code (ex: us, de, fr, jp)
	 * @return     string 
	 */
	public function getcountry($code='')
	{
		$name = '';
		if ($code) 
		{
			if (!($gdb = Hubzero_Geo::getGeoDBO()))
			{
				return $name;
			}

			$gdb->setQuery("SELECT name FROM countries WHERE code = '" . $code . "'");
			$name = stripslashes($gdb->loadResult());
		}
		return $name;
	}

	/**
	 * Get the country based on IP address
	 * 
	 * @param      string $ip IP address to look up
	 * @return     string 
	 */
	public function ipcountry($ip='')
	{
		$country = '';
		if ($ip) 
		{
			if (!($gdb = Hubzero_Geo::getGeoDBO()))
			{
				return $country;
			}

			$sql = "SELECT LOWER(countrySHORT) FROM ipcountry WHERE ipFROM <= INET_ATON('" . $ip . "') AND ipTO >= INET_ATON('" . $ip . "')";
			$gdb->setQuery($sql);
			$country = stripslashes($gdb->loadResult());
		}
		return $country;
	}

	/**
	 * Is a country an D1 nation?
	 * 
	 * @param      string $country Country to check
	 * @return     boolean True if D1
	 */
	public function is_d1nation($country)
	{
		$d1nation = false;
		if ($country) 
		{
			if (!($gdb = Hubzero_Geo::getGeoDBO()))
			{
				return $d1nation;
			}

			$gdb->setQuery("SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER('" . $country . "') AND countrygroup = 'D1'");
			$c = $gdb->loadResult();
			if ($c > 0) 
			{
				$d1nation = true;
			}
		}
		return $d1nation;
	}

	/**
	 * Is a country an E1 nation?
	 * 
	 * @param      string $country Country to check
	 * @return     boolean True if E1
	 */
	public function is_e1nation($country)
	{
		$e1nation = false;
		if ($country) 
		{
			if (!($gdb = Hubzero_Geo::getGeoDBO()))
			{
				return $e1nation;
			}

			$gdb->setQuery("SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER('" . $country . "') AND countrygroup = 'E1'");
			$c = $gdb->loadResult();
			if ($c > 0) 
			{
				$e1nation = true;
			}
		}
		return $e1nation;
	}

	/**
	 * Check if an IP is in a certain location
	 * 
	 * @param      string $ip       IP address to check
	 * @param      string $location Location to check in
	 * @return     boolean True if IP is in the location
	 */
	public function is_iplocation($ip, $location)
	{
		$iplocation = false;
		if ($ip && $location) 
		{
			if (!($gdb = Hubzero_Geo::getGeoDBO()))
			{
				return $iplocation;
			}

			$sql = "SELECT COUNT(*) FROM iplocation WHERE ipfrom <= INET_ATON('" . $ip . "') AND ipto >= INET_ATON('" . $ip . "') AND LOWER(location) = LOWER('" . $location . "')";
			$gdb->setQuery($sql);
			$c = $gdb->loadResult();
			if ($c > 0) 
			{
				$iplocation = true;
			}
		}
		return $iplocation;
	}
}

