<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

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
        $count = $this->db->getQuery(true)
            ->from('#__extensions')
            ->where('type', '=', 'plugin')
            ->where('folder', '=', 'geocode')
            ->count();

        if (!$count) {
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

            foreach ($plugins as $plugin) {
                $enabled = 0;
                if ($plugin == 'local') {
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
        $count = $this->db->getQuery(true)
            ->from('#__extensions')
            ->where('type', '=', 'plugin')
            ->where('folder', '=', 'geocode')
            ->count();

        if ($count) {
            $this->deletePluginEntry('geocode');
        }
    }
}
