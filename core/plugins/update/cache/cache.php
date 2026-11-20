<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

        \Cache::gc();
    }
}
