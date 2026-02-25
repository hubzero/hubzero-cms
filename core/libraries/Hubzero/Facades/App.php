<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * App facade
 *
 * @method static mixed  get(string $key, mixed $default = null)
 * @method static void   set(string $key, mixed $value)
 * @method static bool   has(string $key)
 * @method static bool   bound(string $key)
 * @method static bool   isSite()
 * @method static bool   isAdmin()
 * @method static bool   isApi()
 * @method static bool   isCli()
 * @method static string version()
 * @method static object register(mixed $provider, array $options = [])
 * @method static void   redirect(string $url, int $code = 303)
 * @method static void   abort(int $code, string $text = '')
 * @method static void   close(int $code = 0)
 *
 * @codeCoverageIgnore
 */
class App extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'app';
    }
}
