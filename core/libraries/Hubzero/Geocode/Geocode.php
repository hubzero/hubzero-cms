<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Geocode;

/**
 * Helper class for getting geolocation information
 */
class Geocode
{
	/**
	 * Get a list of countries and their country code
	 *
	 * @var  array
	 */
	public static $countries = array();

	/**
	 * Get a list of countries and their coutnry code
	 *
	 * @param   string  $continent  Continent to filter by
	 * @return  array
	 */
	public static function countries($continent='all')
	{
		if (isset(self::$countries[$continent]))
		{
			return self::$countries[$continent];
		}

		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		// Get a list of providers
		//
		// Each provider has an associated plugin. If the provider supports
		// the desired data look-up, it (the provider) will be returned by
		// the plugin. Otherwise, the plugin returns nothing.
		if ($providers = \Event::trigger('geocode.onGeocodeProvider', array('geocode.countries', $adapter)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		if (!count($p))
		{
			return self::$countries;
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		$geocoder->setResultFactory(new Result\CountriesResultFactory());

		$countries = array();

		if ($data = $geocoder->geocode($continent))
		{
			foreach ($data as $item)
			{
				$country = new \stdClass();
				$country->code      = $item->getCountryCode();
				$country->name      = $item->getCountry();
				$country->continent = $item->getRegion();

				$countries[] = $country;
			}
		}

		self::$countries[$continent] = $countries;

		return self::$countries[$continent];
	}

	/**
	 * Get country based on short code
	 *
	 * @param   string  $code  Short code (ex: us, de, fr, jp)
	 * @return  string
	 */
	public static function country($code)
	{
		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		// Get a list of providers
		if ($providers = \Event::trigger('geocode.onGeocodeProvider', array('geocode.country', $adapter)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		if (!count($p))
		{
			return '';
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		$geocoder->setResultFactory(new Result\CountryResultFactory());

		$country = $code;
		if ($data = $geocoder->geocode($code))
		{
			if (is_array($data))
			{
				$country = $data[0]->getCountry();
			}
			else
			{
				$country = $data->getCountry();
			}
		}
		return $country;
	}

	/**
	 * Geo-locate an address
	 *
	 * @param   string  $address
	 * @return  array
	 */
	public static function locate($address)
	{
		$ip = false;
		if (filter_var($address, FILTER_VALIDATE_IP))
		{
			$ip = true;
		}

		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		// Get a list of providers
		if ($providers = \Event::trigger('geocode.onGeocodeProvider', array('geocode.locate', $adapter, $ip)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		if (!count($p))
		{
			return '';
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		return $geocoder->geocode($address);
	}

	/**
	 * Get the address (reverse locate)
	 *
	 * @param   array  $coordinates  array(latitude, longitude)
	 * @return  array
	 */
	public static function address($coordinates)
	{
		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		// Get a list of providers
		if ($providers = \Event::trigger('geocode.onGeocodeProvider', array('geocode.address', $adapter)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		if (!count($p))
		{
			return '';
		}

		$latitude =  isset($coordinates['latitude'])  ? $coordinates['latitude']  : $coordinates[0];
		$longitude = isset($coordinates['longitude']) ? $coordinates['longitude'] : $coordinates[1];

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		return $geocoder->reverse($latitude, $longitude);
	}

	/**
	 * Get the geo database
	 *
	 * @return  mixed  Database object upon success, null if error
	 */
	public static function getGeoDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			$geodb_params = \Component::params('com_system');

			$options = array();
			$options['driver']   = $geodb_params->get('geodb_driver', 'pdo');
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
				$instance = \Hubzero\Database\Driver::getInstance($options);
			}
			catch (\Exception $e)
			{
				$instance = false;
			}
		}

		if (!$instance || ($instance instanceof Exception) || !$instance->getConnection())
		{
			return null;
		}

		return $instance;
	}

	/**
	 * Get a list of countries and their coutnry code
	 *
	 * @return  array
	 */
	public static function getcountries()
	{
		$countries = array();

		if (!($gdb = self::getGeoDBO()))
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
	 * @param   string  $continent
	 * @return  array
	 */
	public static function getCountriesByContinent($continent='')
	{
		if (!$continent || !($gdb = self::getGeoDBO()))
		{
			return array();
		}

		$gdb->setQuery("SELECT DISTINCT country FROM country_continent WHERE LOWER(continent) =" . $gdb->quote(strtolower($continent)));
		return $gdb->loadColumn();
	}

	/**
	 * Get continent by the country
	 *
	 * @param   string  $country
	 * @return  array
	 */
	public static function getContinentByCountry($country='')
	{
		if (!$country || !($gdb = self::getGeoDBO()))
		{
			return array();
		}

		$gdb->setQuery("SELECT DISTINCT continent FROM country_continent WHERE LOWER(country) ='" . strtolower($country) . "'");
		return $gdb->loadColumn();
	}

	/**
	 * Get a list of country codes by names
	 *
	 * @param   array  $names  List of country names
	 * @return  array
	 */
	public static function getCodesByNames($names=array())
	{
		if (!($gdb = self::getGeoDBO()))
		{
			return array();
		}

		$names = array_map('strtolower', $names);
		foreach ($names as $k => $name)
		{
			$names[$k] = $gdb->quote($name);
		}

		$gdb->setQuery("SELECT DISTINCT name, code FROM countries WHERE LOWER(name) IN (" . implode(",", $names) . ")");
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
	 * @param   string  $code  Short code (ex: us, de, fr, jp)
	 * @return  string
	 */
	public static function getCodeByName($name='')
	{
		$code = '';
		if ($name)
		{
			if (!($gdb = self::getGeoDBO()))
			{
				return $code;
			}

			$gdb->setQuery("SELECT code FROM countries WHERE LOWER(name) = " . $gdb->quote(strtolower($name)));
			$code = stripslashes($gdb->loadResult());
		}
		return $code;
	}

	/**
	 * Get country based on short code
	 *
	 * @param   string  $code  Short code (ex: us, de, fr, jp)
	 * @return  string
	 */
	public static function getcountry($code='')
	{
		$name = '';
		if ($code)
		{
			if (!($gdb = self::getGeoDBO()))
			{
				return $name;
			}

			$gdb->setQuery("SELECT name FROM countries WHERE code = " . $gdb->quote($code));
			$name = stripslashes($gdb->loadResult());
		}
		return $name;
	}

	/**
	 * Get the country based on IP address
	 *
	 * @param   string  $ip  IP address to look up
	 * @return  string
	 */
	public static function ipcountry($ip='')
	{
		$country = '';
		if ($ip)
		{
			if (!($gdb = self::getGeoDBO()))
			{
				return $country;
			}

			$sql = "SELECT LOWER(countrySHORT) FROM ipcountry WHERE ipFROM <= INET_ATON(" . $gdb->quote($ip) . ") AND ipTO >= INET_ATON(" . $gdb->quote($ip) . ")";
			$gdb->setQuery($sql);
			$country = stripslashes($gdb->loadResult());
		}
		return $country;
	}

	/**
	 * Is a country an D1 nation?
	 *
	 * @param   string   $country  Country to check
	 * @return  boolean  True if D1
	 */
	public static function is_d1nation($country)
	{
		$d1nation = false;
		if ($country)
		{
			if (!($gdb = self::getGeoDBO()))
			{
				return $d1nation;
			}

			$gdb->setQuery("SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER(" . $gdb->quote($country) . ") AND countrygroup = 'D1'");
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
	 * @param   string   $country  Country to check
	 * @return  boolean  True if E1
	 */
	public static function is_e1nation($country)
	{
		$e1nation = false;
		if ($country)
		{
			if (!($gdb = self::getGeoDBO()))
			{
				return $e1nation;
			}

			$gdb->setQuery("SELECT COUNT(*) FROM countrygroup WHERE LOWER(countrycode) = LOWER(" . $gdb->quote($country) . ") AND countrygroup = 'E1'");
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
	 * @param   string   $ip        IP address to check
	 * @param   string   $location  Location to check in
	 * @return  boolean  True if IP is in the location
	 */
	public static function is_iplocation($ip, $location)
	{
		$iplocation = false;
		if ($ip && $location)
		{
			if (!($gdb = self::getGeoDBO()))
			{
				return $iplocation;
			}

			$sql = "SELECT COUNT(*) FROM iplocation WHERE ipfrom <= INET_ATON(" . $gdb->quote($ip) . ") AND ipto >= INET_ATON(" . $gdb->quote($ip) . ") AND LOWER(location) = LOWER(" . $gdb->quote($location) . ")";
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
