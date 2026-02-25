<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Session facade
 *
 * @method static mixed  get(string $name, mixed $default = null, string $namespace = 'default')
 * @method static mixed  set(string $name, mixed $value = null, string $namespace = 'default')
 * @method static bool   has(string $name, string $namespace = 'default')
 * @method static mixed  clear(string $name, string $namespace = 'default')
 * @method static string getId()
 * @method static string getName()
 * @method static string getToken(bool $forceNew = false)
 * @method static bool   hasToken(string $tCheck, bool $forceExpire = true)
 * @method static string getFormToken(bool $forceNew = false)
 * @method static bool   checkToken(string $method = 'post', bool $capture = false)
 * @method static string getState()
 * @method static int    getExpire()
 * @method static bool   isNew()
 * @method static bool   destroy()
 * @method static bool   restart()
 * @method static void   close()
 *
 * @codeCoverageIgnore
 */
class Session extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'session';
    }
}
