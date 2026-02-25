<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Router facade
 *
 * @method static string url(string $url, bool $xhtml = true, int $ssl = null)
 * @method static object build(string $uri)
 * @method static object parse(string $url)
 * @method static string urlForClient(string $client, string $url, bool $xhtml = true, int $ssl = null)
 * @method static object client(string $client = null)
 * @method static object rules(string $type)
 * @method static void   bind(array $vars)
 * @method static void   flush()
 * @method static mixed  getVar(string $key, mixed $default = null)
 * @method static void   setVar(string $key, mixed $value = null)
 *
 * @codeCoverageIgnore
 */
class Route extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getAccessor()
    {
        return 'router';
    }
}
