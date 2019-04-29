<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * GeocoderUS plugin for geocode
 *
 * The GeocoderUsProvider is able to geocode street addresses
 * only, exclusively in USA.
 */
class plgGeocodeGeocoderus extends \Hubzero\Plugin\Plugin
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

		return new \Geocoder\Provider\GeocoderUsProvider(
			$adapter
		);
	}
}
