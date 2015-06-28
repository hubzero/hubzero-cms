<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding geocode plugins
 **/
class Migration20140114091331PlgGeocode extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT COUNT(*) FROM `#__extensions` WHERE `type`='plugin' AND `folder`='geocode';";

		$this->db->setQuery($query);

		if (!$this->db->loadResult())
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
				$this->addPluginEntry('geocode', $plugin, $enabled);
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT COUNT(*) FROM `#__extensions` WHERE `type`='plugin' AND `folder`='geocode';";

		$this->db->setQuery($query);

		if ($this->db->loadResult())
		{
			$this->deletePluginEntry('geocode');
		}
	}
}