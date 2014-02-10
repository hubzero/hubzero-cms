<?php

use Hubzero\Content\Migration;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140114091331PlgGeocode extends Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "SELECT COUNT(*) FROM `#__extensions` WHERE `type`='plugin' AND `folder`='geocode';";

		$db->setQuery($query);

		if (!$db->loadResult())
		{
			$plugins = array(
				'arcgisonline', 
				'baidu', 
				'bingmaps',
				'cloudmade',
				'datasciencetoolkit',
				'freegeoip',
				'geocoderca',
				'geocoderus',
				'geoip',
				'geoips',
				'geonames',
				'geoplugin',
				'googlemaps',
				'googlemapsbusiness',
				'hostip',
				'ignopenls',
				'ipgeobase',
				'ipinfodb',
				'local',
				'mapquest',
				'maxmind',
				'maxmindbinary',
				'nominatim',
				'oiorest',
				'openstreetmap',
				'tomtom',
				'yandex'
			);

			foreach ($plugins as $plugin)
			{
				$enabled = 0;
				if ($plugin == 'local')
				{
					$enabled = 1;
				}
				self::addPluginEntry('geocode', $plugin, $enabled);
			}
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "SELECT COUNT(*) FROM `#__extensions` WHERE `type`='plugin' AND `folder`='geocode';";

		$db->setQuery($query);

		if ($db->loadResult())
		{
			self::deletePluginEntry('geocode');
		}
	}
}