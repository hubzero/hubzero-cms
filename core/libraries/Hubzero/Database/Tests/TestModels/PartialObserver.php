<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Partial observer that only implements some events
 */
class PartialObserver
{
    protected static $called = [];

    public static function reset()
    {
        static::$called = [];
    }

    public static function wasCalled($event)
    {
        return isset(static::$called[$event]);
    }

    // Only implementing creating - other events should not cause errors
    public function creating($model)
    {
        static::$called['creating'] = true;
    }
}
