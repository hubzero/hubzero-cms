<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * ArcGISOnline plugin for geocode
 *
 * The ArcGISOnlineProvider is able to geocode and reverse geocode
 * street addresses. It's possible to specify a sourceCountry to
 * restrict result to this specific country thus reducing request
 * time (note that this doesn't work on reverse geocoding).
 * This provider also supports SSL.
 */
class plgGeocodeArcgisonline extends \Hubzero\Plugin\Plugin
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

		return new \Geocoder\Provider\ArcGISOnlineProvider(
			$adapter, $this->params->get('sourceCountry', null), $this->params->get('useSsl', false)
		);
	}
}
