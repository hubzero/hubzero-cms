<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
 * @method static bool  clean()
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
