<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Partial observer that only implements some events
 */
class PartialObserver
{
    /**
     * @var  array<mixed>
     */
    protected static $called = [];

    /**
     * @return  void
     */
    public static function reset()
    {
        static::$called = [];
    }

    /**
     * @param   string  $event
     * @return  bool
     */
    public static function wasCalled($event)
    {
        return isset(static::$called[$event]);
    }

    // Only implementing creating - other events should not cause errors
    /**
     * @param   mixed  $model
     * @return  void
     */
    public function creating($model)
    {
        static::$called['creating'] = true;
    }
}
