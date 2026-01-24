<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * Geocode plugin for Hubzero
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgGeocodeLocal extends \Hubzero\Plugin\Plugin
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
