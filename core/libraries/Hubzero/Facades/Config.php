<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Config helper facade
 *
 * @method static mixed  get(string $path, mixed $default = null)
 * @method static mixed  set(string $path, mixed $value, string $separator = null)
 * @method static mixed  def(string $key, mixed $default)
 * @method static bool   has(string $path)
 * @method static void   remove(string $path)
 * @method static object merge(mixed $array)
 * @method static array  toArray()
 *
 * @codeCoverageIgnore
 */
class Config extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'config';
    }
}
