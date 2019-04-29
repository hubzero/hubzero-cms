<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * TomTom plugin for geocode
 *
 * The TomTomProvider named tomtom is able to geocode and reverse
 * geocode street addresses. The default langage-locale is en,
 * you can choose between de, es, fr, it, nl, pl, pt and sv.
 * A valid api key is required.
 */
class plgGeocodeTomTom extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('apiKey') || $ip)
		{
			return;
		}

		return new \Geocoder\Provider\TomTomProvider(
			$adapter,
			$this->params->get('apiKey')
		);
	}
}
