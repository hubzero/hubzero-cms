<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * IGNOpenLS plugin for geocode
 *
 * The IGNOpenLSProvider is able to geocode street addresses only,
 * exclusively in France. A valid OpenLS api key is required.
 */
class plgGeocodeIgnopenls extends \Hubzero\Plugin\Plugin
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

		if (!$this->params->get('apiKey'))
		{
			return;
		}

		return new \Geocoder\Provider\IGNOpenLSProvider(
			$adapter,
			$this->params->get('apiKey')
		);
	}
}
