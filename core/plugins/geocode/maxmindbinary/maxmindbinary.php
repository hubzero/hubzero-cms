<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * MaxMindBinary plugin for geocode
 */
namespace Plugins\Geocode\Maxmindbinary;

use Hubzero\Plugin\Plugin;

class Maxmindbinary extends Plugin
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

        return new \Geocoder\Provider\MaxMindBinary\MaxMindBinary(
            $adapter,
            $this->params->get('apiKey')
        );
    }
}
