<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * OIORest plugin for geocode
 *
 * The OIORestProvider is able to geocode and reverse geocode
 * street addresses, exclusively in Denmark.
 */
class plgGeocodeOiorest extends \Hubzero\Plugin\Plugin
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

		return new \Geocoder\Provider\OIORestProvider(
			$adapter
		);
	}
}
