<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Yandex plugin for geocode
 *
 * The YandexProvider is able to geocode and reverse geocode
 * street addresses. The default language-locale is ru-RU, you can choose
 * between uk-UA, be-BY, en-US, en-BR and tr-TR. This provider can also
 * reverse information based on coordinates (latitude, longitude). It's
 * possible to precise the toponym to get more accurate result for reverse
 * geocoding: house, street, metro, district and locality.
 */
class plgGeocodeYandex extends \Hubzero\Plugin\Plugin
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

		return new \Geocoder\Provider\YandexProvider(
			$adapter
		);
	}
}
