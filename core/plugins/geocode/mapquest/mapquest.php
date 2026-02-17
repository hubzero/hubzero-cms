<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     * @param  object  $adapter
     * @param  boolean $ip
     * @return object
     */
    public function onGeocodeProvider($context, $adapter, $ip = false)
    {
        if ($context != 'geocode.locate' && $context != 'geocode.address') {
            return;
        }

        if (!$this->params->get('apiKey') || $ip) {
            return;
        }

        return new \Geocoder\Provider\MapQuest\MapQuest(
            $adapter,
            $this->params->get('apiKey')
        );
    }
}
