<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Update plugin for handling/cleaning cached data
 */
namespace Plugins\Update\Cache;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Config;

class Cache extends Plugin
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

        \Hubzero\Facades\Cache::gc();
    }
}
