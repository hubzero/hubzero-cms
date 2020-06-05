<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Geoip plugin for geocode
 *
 * The GeoipProvider is able to geocode IPv4 and IPv6 addresses only.
 * No need to use an HttpAdapter as it uses a local database. See the
 * MaxMind page for more information.
 */
class plgGeocodeGeoip extends \Hubzero\Plugin\Plugin
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

		if ($ip)
		{
			return;
		}

		return new \Geocoder\Provider\GeoipProvider(
			$adapter
		);
	}
}
