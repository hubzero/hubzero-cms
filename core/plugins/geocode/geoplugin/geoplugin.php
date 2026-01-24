<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * Geoplugin plugin for geocode
 *
 * The GeoPluginProvider named geo_plugin is able to geocode
 * IPv4 addresses and IPv6 addresses only.
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgGeocodeGeoplugin extends \Hubzero\Plugin\Plugin
{
    /**
     * Return a geocode provider
     *
     * @param  string  $context
     * @param  object  $adapter
     * @param  boolean $ip
     * @return object
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate') {
            return;
        }

        if (!$ip) {
            return;
        }

        return new \Geocoder\Provider\GeoPlugin\GeoPlugin(
            $adapter
        );
    }
}
