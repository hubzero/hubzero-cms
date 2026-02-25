<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Request facade
 *
 * @method static mixed   getVar(string $key, mixed $default = null, string $hash = 'input', string $type = 'none', int $mask = 0)
 * @method static bool    setVar(string $name, string $value = null, string $hash = 'method', bool $overwrite = true)
 * @method static int     getInt(string $key, int $default = 0, string $hash = 'input')
 * @method static float   getFloat(string $key, float $default = 0.0, string $hash = 'input')
 * @method static bool    getBool(string $key = null, bool $default = null, string $hash = 'input')
 * @method static string  getWord(string $key, mixed $default = null, string $hash = 'input')
 * @method static string  getCmd(string $key = null, mixed $default = null, string $hash = 'input')
 * @method static string  getString(string $key, mixed $default = null, string $hash = 'input')
 * @method static array   getArray(string $key = null, array $default = [], string $hash = 'input')
 * @method static string  method()
 * @method static string  root(bool $pathonly = false)
 * @method static string  current(bool $query = false)
 * @method static string  path()
 * @method static string  base(bool $pathonly = false)
 * @method static string  scheme()
 * @method static string  host()
 * @method static string  ip()
 * @method static string  segment(int $index, mixed $default = null)
 * @method static array   segments()
 * @method static bool    ajax()
 * @method static bool    secure()
 * @method static bool    has(mixed $key)
 * @method static mixed   input(string $key = null, mixed $default = null)
 * @method static bool    checkToken(string $method = 'post')
 * @method static bool    checkHoneypot(string $name = null, int $delay = 3)
 * @method static mixed   getState(string $key, string $request, string $default = null, string $type = 'none')
 *
 * @codeCoverageIgnore
 */
class Request extends Facade
{
    /**
     * Get the registered name.
     *
     * @return string
     */
    protected static function getAccessor()
    {
        return 'request';
    }
}
