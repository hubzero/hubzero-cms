<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Config helper facade
 *
 * @method static mixed  get(string $path, mixed $default = null)
 * @method static mixed  set(string $path, mixed $value, string $separator = null)
 * @method static mixed  def(string $key, mixed $default = '')
 * @method static bool   has(string $path)
 * @method static object merge(mixed $source, bool $recursive = false)
 * @method static array<mixed>  toArray()
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
