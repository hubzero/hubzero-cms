<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Component loader facade
 *
 * @method static bool   isEnabled(string $option, bool $strict = false)
 * @method static object params(string $option, bool $strict = false)
 * @method static string path(string $option)
 * @method static string canonical(string $option)
 * @method static string render(string $option, array $params = [])
 * @method static object router(string $option, string $client = null, string $version = null)
 * @method static object load(string $option, bool $strict = false)
 *
 * @codeCoverageIgnore
 */
class Component extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'component';
    }
}
