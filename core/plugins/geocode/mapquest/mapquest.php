<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * MapQuest plugin for geocode
 *
 * The MapQuestProvider is able to geocode and reverse geocode
 * street addresses. A valid api key is required.
 */

namespace Plugins\Geocode\Mapquest;

use Hubzero\Plugin\Plugin;

class Mapquest extends Plugin
{
    /**
     * Return a geocode provider
     *
     * @param  string  $context
     * @param  \Http\Discovery\Psr18Client  $adapter
     * @param  boolean $ip
     * @return \Geocoder\Provider\MapQuest\MapQuest|null
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate' && $context != 'geocode.address') {
            return null;
        }

        if (!$this->params->get('apiKey') || $ip) {
            return null;
        }

        return new \Geocoder\Provider\MapQuest\MapQuest(
            $adapter,
            $this->params->get('apiKey'),
        );
    }
}
