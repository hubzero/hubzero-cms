<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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

namespace Plugins\Geocode\Yandex;

use Hubzero\Plugin\Plugin;

class Yandex extends Plugin
{
    /**
     * Return a geocode provider
     *
     * @param  string  $context
     * @param  \Http\Discovery\Psr18Client  $adapter
     * @param  boolean $ip
     * @return \Geocoder\Provider\Yandex\Yandex|null
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate' && $context != 'geocode.address') {
            return null;
        }

        if ($ip) {
            return null;
        }

        return new \Geocoder\Provider\Yandex\Yandex(
            $adapter,
        );
    }
}
