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
 * @method static bool    setVar(string $name, mixed $value = null, string $hash = 'method', bool $overwrite = true)
 * @method static int     getInt(string $key, mixed $default = 0, string $hash = 'input')
 * @method static int     getUInt(string $name, int $default = 0, string $hash = 'input')
 * @method static float   getFloat(string $key, float $default = 0.0, string $hash = 'input')
 * @method static bool    getBool(string $key = null, bool $default = null, string $hash = 'input')
 * @method static string  getWord(string $key, mixed $default = null, string $hash = 'input')
 * @method static string  getCmd(string $key = null, mixed $default = null, string $hash = 'input')
 * @method static string  getString(string $key, mixed $default = null, string $hash = 'input')
 * @method static array   getArray(string $key = null, mixed $default = [], string $hash = 'input')
 * @method static array   getSimpleArray(string $key = null, array $default = [], string $hash = 'input')
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
 * @method static mixed   query(string $key = null, mixed $default = null, string $type = null)
 * @method static mixed   request(string $key = null, mixed $default = null, string $type = null)
 * @method static mixed   cookie(string $key = null, mixed $default = null, string $type = null)
 * @method static mixed   header(string $key = null, mixed $default = null, string $type = null)
 * @method static mixed   file(string $key = null, mixed $default = null)
 * @method static mixed   get(string $key, mixed $default = null)
 * @method static string  getMethod()
 * @method static string  getHost()
 * @method static string  getUri()
 * @method static ?string getQueryString()
 * @method static string  getSchemeAndHttpHost()
 * @method static bool    checkToken(string|array $method = 'post')
 * @method static bool    checkHoneypot(string $name = null, int $delay = 3)
 * @method static mixed   getState(string $key, string $request, mixed $default = null, string $type = 'none')
 * @method static bool   isSecure()
 * @method static string getHttpHost()
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

    /**
     * Get a request variable
     *
     * Declared here rather than as a tag because its signature does not fit
     * on one line, and a tag cannot be wrapped.
     *
     * @param   string   $key      Request key
     * @param   mixed    $default  Default value
     * @param   string   $hash     Where the variable should come from (POST, GET, FILES, COOKIE, METHOD)
     * @param   string   $type     Return type for the variable (deprecated, Joomla legacy)
     * @param   integer  $mask     Filter mask for the variable (deprecated, Joomla legacy)
     * @return  mixed
     */
    public static function getVar($key, $default = null, $hash = 'input', $type = 'none', $mask = 0)
    {
        return static::getRoot()->getVar(...func_get_args());
    }
}
