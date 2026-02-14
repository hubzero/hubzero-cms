<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Test observer that tracks which events were called
 */
class TestObserver
{
    protected static $called = [];
    protected static $models = [];
    protected static $callTimes = [];
    protected static $callCounts = [];
    protected static $callSequence = 0;
    protected static $callOrder = [];

    public static function reset()
    {
        static::$called = [];
        static::$models = [];
        static::$callTimes = [];
        static::$callCounts = [];
        self::$callSequence = 0;
        static::$callOrder = [];
    }

    public static function wasCalled($event)
    {
        return isset(static::$called[$event]) && static::$called[$event];
    }

    public static function getModel($event)
    {
        return static::$models[$event] ?? null;
    }

    public static function getCallTime($event)
    {
        return static::$callTimes[$event] ?? null;
    }

    public static function getCallOrder($event)
    {
        return static::$callOrder[$event] ?? null;
    }

    public static function getCallCount($event)
    {
        return static::$callCounts[$event] ?? 0;
    }

    protected function recordCall($event, $model)
    {
        static::$called[$event] = true;
        static::$models[$event] = $model;
        static::$callTimes[$event] = microtime(true);
        static::$callCounts[$event] = (static::$callCounts[$event] ?? 0) + 1;
        self::$callSequence++;
        static::$callOrder[$event] = self::$callSequence;
    }

    public function creating($model)
    {
        $this->recordCall('creating', $model);
    }

    public function created($model)
    {
        $this->recordCall('created', $model);
    }

    public function updating($model)
    {
        $this->recordCall('updating', $model);
    }

    public function updated($model)
    {
        $this->recordCall('updated', $model);
    }

    public function saving($model)
    {
        $this->recordCall('saving', $model);
    }

    public function saved($model)
    {
        $this->recordCall('saved', $model);
    }

    public function deleting($model)
    {
        $this->recordCall('deleting', $model);
    }

    public function deleted($model)
    {
        $this->recordCall('deleted', $model);
    }
}
