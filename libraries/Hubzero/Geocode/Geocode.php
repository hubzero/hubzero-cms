<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @var array
	 */
	public static $countries = array();

	/**
	 * Get a list of countries and their coutnry code
	 * 
	 * @param  string $continent Continent to filter by
	 * @return array
	 */
	public static function countries($continent='all')
	{
		if (isset(self::$countries[$continent]))
		{
			return self::$countries[$continent];
		}

		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		\JPluginHelper::importPlugin('geocode');

		// Get a list of providers
		// 
		// Each provider has an associated plugin. If the provider supports
		// the desired data look-up, it (the provider) will be returned by 
		// the plugin. Otherwise, the plugin returns nothing.
		if ($providers = \JDispatcher::getInstance()->trigger('onGeocodeProvider', array('geocode.countries', $adapter)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		try 
		{
			$geocoder->setResultFactory(new Result\CountriesResultFactory());

			$countries = array();

			if ($data = $geocoder->geocode($continent))
			{
				foreach ($data as $item)
				{
					$country = new stdClass();
					$country->code      = $item->getCountryCode();
					$country->name      = $item->getCountry();
					$country->continent = $item->getRegion();

					$countries[] = $country;
				}
			}

			self::$countries[$continent] = $countries;

			return self::$countries[$continent];
		} 
		catch (\Exception $e) 
		{
			echo $e->getMessage();
		}
	}

	/**
	 * Get country based on short code
	 * 
	 * @param  string $code Short code (ex: us, de, fr, jp)
	 * @return string 
	 */
	public static function country($code)
	{
		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		\JPluginHelper::importPlugin('geocode');

		// Get a list of providers
		if ($providers = \JDispatcher::getInstance()->trigger('onGeocodeProvider', array('geocode.country', $adapter)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		try 
		{
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
		catch (\Exception $e) 
		{
			echo $e->getMessage();
		}
	}

	/**
	 * Geo-locate an address
	 * 
	 * @param  string $address
	 * @return array
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

		\JPluginHelper::importPlugin('geocode');

		// Get a list of providers
		if ($providers = \JDispatcher::getInstance()->trigger('onGeocodeProvider', array('geocode.locate', $adapter, $ip)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		try 
		{
			return $geocoder->geocode($address);
		} 
		catch (\Exception $e) 
		{
			echo $e->getMessage();
		}
	}

	/**
	 * Get the address (reverse locate)
	 * 
	 * @param  array $coordinates array(longitude, latitude)
	 * @return array
	 */
	public static function address($coordinates)
	{
		$adapter  = new \Geocoder\HttpAdapter\CurlHttpAdapter();

		$p = array();

		\JPluginHelper::importPlugin('geocode');

		// Get a list of providers
		if ($providers = \JDispatcher::getInstance()->trigger('onGeocodeProvider', array('geocode.reverse', $adapter)))
		{
			foreach ($providers as $provider)
			{
				if ($provider)
				{
					$p[] = $provider;
				}
			}
		}

		// Instantiate the Geocoder service and pass it the list of providers
		$geocoder = new \Geocoder\Geocoder();
		$geocoder->registerProvider(new \Geocoder\Provider\ChainProvider($p));

		// Try to get some data...
		try 
		{
			return $geocoder->geocode($address);
		} 
		catch (\Exception $e) 
		{
			echo $e->getMessage();
		}
	}
}

