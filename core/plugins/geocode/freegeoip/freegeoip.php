<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * FreeGeoIp plugin for geocode
 *
 * The FreeGeoIpProvider is able to geocode IPv4 and IPv6 addresses only.
 */
class plgGeocodeFreegeoip extends \Hubzero\Plugin\Plugin
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

		// If not an IP address...
		if (!$ip)
		{
			return;
		}

		return new \Geocoder\Provider\FreeGeoIpProvider(
			$adapter
		);
	}
}
