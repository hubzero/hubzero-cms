<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * MaxMind plugin for geocode
 *
 * The MaxMindProvider named maxmind is able to geocode IPv4 and
 * IPv6 addresses only. A valid City/ISP/Org or Omni service's api
 * key is required. This provider provides two constants
 * CITY_EXTENDED_SERVICE by default and OMNI_SERVICE.
 */
class plgGeocodeMaxmind extends \Hubzero\Plugin\Plugin
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

		return new \Geocoder\Provider\MaxMindProvider(
			$adapter,
			$this->params->get('apiKey')
		);
	}
}
