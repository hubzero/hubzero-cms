<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Plugin loader facade
 *
 * @method static bool   import(string $type, string $plugin = null, bool $autocreate = true, object $dispatcher = null)
 * @method static mixed  byType(string $type, string $plugin = null)
 * @method static bool   isEnabled(string $type, string $plugin = null)
 * @method static object params(string $type, string $plugin)
 * @method static string path(string $type, string $plugin = null)
 * @method static bool   language(string $extension, string $basePath = '')
 * @method static array  all()
 *
 * @codeCoverageIgnore
 */
class Plugin extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'plugin';
    }
}
