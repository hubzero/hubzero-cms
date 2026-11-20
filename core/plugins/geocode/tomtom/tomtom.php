<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * TomTom plugin for geocode
 *
 * The TomTomProvider named tomtom is able to geocode and reverse
 * geocode street addresses. The default langage-locale is en,
 * you can choose between de, es, fr, it, nl, pl, pt and sv.
 * A valid api key is required.
 */

namespace Plugins\Geocode\Tomtom;

use Hubzero\Plugin\Plugin;

class Tomtom extends Plugin
{
    /**
     * Return a geocode provider
     *
     * @param  string  $context
     * @param  \Http\Discovery\Psr18Client  $adapter
     * @param  boolean $ip
     * @return \Geocoder\Provider\TomTom\TomTom|null
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate' && $context != 'geocode.address') {
            return null;
        }

        if (!$this->params->get('apiKey') || $ip) {
            return null;
        }

        return new \Geocoder\Provider\TomTom\TomTom(
            $adapter,
            $this->params->get('apiKey'),
        );
    }
}
