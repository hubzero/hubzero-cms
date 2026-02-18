<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * IpInfoDb plugin for geocode
 *
 * The IpInfoDbProvider is able to geocode IPv4 addresses
 * only. A valid api key is required.
 */
namespace Plugins\Geocode\Ipinfodb;

use Hubzero\Plugin\Plugin;

class Ipinfodb extends Plugin
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

        if (!$this->params->get('apiKey')) {
            return;
        }

        return new \Geocoder\Provider\IpInfoDb\IpInfoDb(
            $adapter,
            $this->params->get('apiKey')
        );
    }
}
