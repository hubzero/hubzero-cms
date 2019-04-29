<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * GeocoderCA plugin for geocode
 *
 * The GeocoderCaProvider is able to geocode and reverse
 * geocode street addresses, exclusively in USA & Canada.
 */
class plgGeocodeGeocoderca extends \Hubzero\Plugin\Plugin
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
		if ($context != 'geocode.locate' && $context != 'geocode.address')
		{
			return;
		}

		if ($ip)
		{
			return;
		}

		return new \Geocoder\Provider\GeocoderCaProvider(
			$adapter
		);
	}
}
