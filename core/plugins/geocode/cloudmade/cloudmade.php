<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * CloudMade plugin for geocode
 *
 * The CloudMadeProvider is able to geocode and reverse geocode
 * street addresses. A valid api key is required.
 */
class plgGeocodeCloudmade extends \Hubzero\Plugin\Plugin
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

		// No API key or address is an IP?
		if (!$this->params->get('apiKey') || $ip)
		{
			return;
		}

		return new \Geocoder\Provider\CloudMadeProvider(
			$adapter, $this->params->get('apiKey')
		);
	}
}
