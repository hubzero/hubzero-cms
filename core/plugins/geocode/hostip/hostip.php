<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * HostIp plugin for geocode
 *
 * The HostIpProvider is able to geocode IPv4 addresses only.
 */

namespace Plugins\Geocode\Hostip;

use Hubzero\Plugin\Plugin;

class Hostip extends Plugin
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

        return new \Geocoder\Provider\HostIp\HostIp(
            $adapter,
        );
    }
}
