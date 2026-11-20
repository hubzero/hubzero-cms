<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @param  \Http\Discovery\Psr18Client  $adapter
     * @param  boolean $ip
     * @return \Geocoder\Provider\HostIp\HostIp|null
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
