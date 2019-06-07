<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * GoogleMapsBusiness plugin for geocode
 *
 * The GoogleMapsBusinessProvider is able to geocode and reverse geocode
 * street addresses. A valid Client ID is required. The private key is optional.
 */
class plgGeocodeGooglemapsbusiness extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('clientId'))
		{
			return;
		}

		return new \Geocoder\Provider\GoogleMapsBusinessProvider(
			$adapter,
			$this->params->get('clientId'),
			$this->params->get('privateKey')
		);
	}
}
