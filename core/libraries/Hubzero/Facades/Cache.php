<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Cache helper facade
 *
 * @method static mixed get(string $key)
 * @method static bool  put(string $key, mixed $value, int $minutes)
 * @method static bool  add(string $key, mixed $value, int $minutes)
 * @method static bool  has(string $key)
 * @method static bool  forget(string $key)
 * @method static void  clean(string $group = null)
 * @method static bool  gc()
 *
 * @codeCoverageIgnore
 */
class Cache extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'cache.store';
    }
}
