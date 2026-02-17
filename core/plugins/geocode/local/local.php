<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
/**
 * Geocode plugin for Hubzero
 */
namespace Plugins\Geocode\Local;

use Hubzero\Plugin\Plugin;

class Local extends Plugin
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
        switch ($context) {
            case 'geocode.countries':
                $provider = 'countries';
                break;

            case 'geocode.country':
                $provider = 'country';
                break;

            case 'geocode.continent':
                $provider = 'continent';
                break;

            default:
                return;
            break;
        }

        include_once __DIR__ . '/LocalProvider.php';

        return new \Plugins\Geocode\LocalProvider(
            $adapter,
            $provider
        );
    }
}
