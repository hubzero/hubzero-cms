<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * DataScienceToolkit plugin for geocode
 *
 * The DataScienceToolkitProvider is able to geocode IPv4
 * addresses and street adresses, exclusively in USA & Canada.
 */
class plgGeocodeDatasciencetoolkit extends \Hubzero\Plugin\Plugin
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

		return new \Geocoder\Provider\DataScienceToolkitProvider(
			$adapter
		);
	}
}
