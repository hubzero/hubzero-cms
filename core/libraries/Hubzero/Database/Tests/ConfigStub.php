<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

/**
 * Stands in for the Config facade with settings a test sets
 */
class ConfigStub
{
    public static array $values = [];

    public static function get($key, $default = null)
    {
        return self::$values[$key] ?? $default;
    }
}
