<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * IpGeoBase plugin for geocode
 *
 * The IpGeoBaseProvider named ip_geo_base is able to geocode
 * IPv4 addresses only, very accurate in Russia.
 */
class plgGeocodeIpgeobase extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a geocode provider
	 *
	 * @param  string  $context
	 * @param  object  $adapter
	 * @param  boolean $ip
	 * @return object
	 */
	public function onGeocodeProvider($context, $adapter, $ip=false)
	{
		if ($context != 'geocode.locate')
		{
			return;
		}

		return new \Geocoder\Provider\IpGeoBaseProvider(
			$adapter
		);
	}
}
