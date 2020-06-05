<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * GeoIPs plugin for geocode
 *
 * The GeoIPsProvider named geo_ips is able to geocode
 * IPv4 addresses only. A valid api key is required.
 */
class plgGeocodeGeoips extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('apiKey') || !$ip)
		{
			return;
		}

		return new \Geocoder\Provider\GeoIPsProvider(
			$adapter,
			$this->params->get('apiKey')
		);
	}
}
