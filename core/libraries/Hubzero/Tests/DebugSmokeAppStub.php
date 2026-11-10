<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class DebugSmokeAppStub
{
    private static array $state = [];

    public static function reset(): void
    {
        self::$state = [];
    }

    public static function get(string $key)
    {
        if (array_key_exists($key, self::$state)) {
            return self::$state[$key];
        }

        // This stub is aliased over the global App for the whole run, not
        // just this file, so fall through to the real container for keys it
        // does not define. Returning null unconditionally would break every
        // later test that resolves a service through the App facade.
        $app = \Hubzero\Facades\Facade::getApplication();

        return ($app && isset($app[$key])) ? $app[$key] : null;
    }

    public static function set(string $key, $value): void
    {
        self::$state[$key] = $value;
    }
}
