<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Geocode plugin for Hubzero
 */
class plgGeocodeLocal extends \Hubzero\Plugin\Plugin
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
		switch ($context)
		{
			case 'geocode.countries':
				$provider = 'countries';
			break;

			case 'geocode.country':
				$provider = 'country';
			break;

			case 'geocode.continent':
				$provider = 'continent';
			break;

			default:
				return;
			break;
		}

		include_once __DIR__ . '/LocalProvider.php';

		return new \Plugins\Geocode\LocalProvider(
			$adapter,
			$provider
		);
	}
}
