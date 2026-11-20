<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * MaxMind plugin for geocode
 *
 * The MaxMindProvider named maxmind is able to geocode IPv4 and
 * IPv6 addresses only. A valid City/ISP/Org or Omni service's api
 * key is required. This provider provides two constants
 * CITY_EXTENDED_SERVICE by default and OMNI_SERVICE.
 */

namespace Plugins\Geocode\Maxmind;

use Hubzero\Plugin\Plugin;

class Maxmind extends Plugin
{
    /**
     * Return a geocode provider
     *
     * @param  string  $context
     * @param  \Http\Discovery\Psr18Client  $adapter
     * @param  boolean $ip
     * @return \Geocoder\Provider\MaxMind\MaxMind|null
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate') {
            return null;
        }

        if (!$this->params->get('apiKey') || !$ip) {
            return null;
        }

        return new \Geocoder\Provider\MaxMind\MaxMind(
            $adapter,
            $this->params->get('apiKey'),
        );
    }
}
