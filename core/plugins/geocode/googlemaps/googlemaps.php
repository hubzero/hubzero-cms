<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * GoogleMaps plugin for geocode
 *
 * The GoogleMapsProvider is able to geocode and reverse geocode
 * street addresses.
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgGeocodeGooglemaps extends \Hubzero\Plugin\Plugin
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
        if ($context != 'geocode.locate' && $context != 'geocode.address') {
            return;
        }

        return new \Geocoder\Provider\GoogleMaps\GoogleMaps(
            $adapter
        );
    }
}
