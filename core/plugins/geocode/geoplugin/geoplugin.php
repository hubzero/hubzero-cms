<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Geoplugin plugin for geocode
 *
 * The GeoPluginProvider named geo_plugin is able to geocode
 * IPv4 addresses and IPv6 addresses only.
 */

namespace Plugins\Geocode\Geoplugin;

use Hubzero\Plugin\Plugin;

class Geoplugin extends Plugin
{
    /**
     * Return a geocode provider
     *
     * @param  string  $context
     * @param  object  $adapter
     * @param  boolean $ip
     * @return object|null
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate') {
            return null;
        }

        if (!$ip) {
            return null;
        }

        return new \Geocoder\Provider\GeoPlugin\GeoPlugin(
            $adapter,
        );
    }
}
