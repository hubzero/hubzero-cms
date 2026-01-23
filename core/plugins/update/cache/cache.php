<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

/**
 * Update plugin for handling/cleaning cached data
 */
// phpcs:ignore Squiz.Classes.ValidClassName.NotCamelCaps
class plgUpdateCache extends \Hubzero\Plugin\Plugin
{
    /**
     * Trash all expired cache data
     *
     * @return  void
     */
    public function onAfterRepositoryUpdate()
    {
        if (!Config::get('caching')) {
            return;
        }

        Cache::gc();
    }
}
