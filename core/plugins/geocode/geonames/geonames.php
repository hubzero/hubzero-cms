<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Geonames plugin for geocode
 *
 * The GeonamesProvider named geonames is able to geocode and
 * reverse geocode places. A valid username is required.
 */
class plgGeocodeGeonames extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('username') || $ip)
		{
			return;
		}

		return new \Geocoder\Provider\GeonamesProvider(
			$adapter,
			$this->params->get('username')
		);
	}
}
