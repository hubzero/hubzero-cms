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
    /**
     * @var  int
     */
    protected static $callSequence = 0;
    protected static $callOrder = [];

    /**
     * @return  void
     */
    public static function reset()
    {
        static::$called = [];
        static::$models = [];
        static::$callTimes = [];
        static::$callCounts = [];
        self::$callSequence = 0;
        static::$callOrder = [];
    }

    /**
     * @return  bool
     */
    public static function wasCalled($event)
    {
        return isset(static::$called[$event]) && static::$called[$event];
    }

    /**
     * @return  mixed
     */
    public static function getModel($event)
    {
        return static::$models[$event] ?? null;
    }

    /**
     * @return  mixed
     */
    public static function getCallTime($event)
    {
        return static::$callTimes[$event] ?? null;
    }

    /**
     * @return  mixed
     */
    public static function getCallOrder($event)
    {
        return static::$callOrder[$event] ?? null;
    }

    /**
     * @return  mixed
     */
    public static function getCallCount($event)
    {
        return static::$callCounts[$event] ?? 0;
    }

    /**
     * @return  void
     */
    protected function recordCall($event, $model)
    {
        static::$called[$event] = true;
        static::$models[$event] = $model;
        static::$callTimes[$event] = microtime(true);
        static::$callCounts[$event] = (static::$callCounts[$event] ?? 0) + 1;
        self::$callSequence++;
        static::$callOrder[$event] = self::$callSequence;
    }

    /**
     * @return  void
     */
    public function creating($model)
    {
        $this->recordCall('creating', $model);
    }

    /**
     * @return  void
     */
    public function created($model)
    {
        $this->recordCall('created', $model);
    }

    /**
     * @return  void
     */
    public function updating($model)
    {
        $this->recordCall('updating', $model);
    }

    /**
     * @return  void
     */
    public function updated($model)
    {
        $this->recordCall('updated', $model);
    }

    /**
     * @return  void
     */
    public function saving($model)
    {
        $this->recordCall('saving', $model);
    }

    /**
     * @return  void
     */
    public function saved($model)
    {
        $this->recordCall('saved', $model);
    }

    /**
     * @return  void
     */
    public function deleting($model)
    {
        $this->recordCall('deleting', $model);
    }

    /**
     * @return  void
     */
    public function deleted($model)
    {
        $this->recordCall('deleted', $model);
    }
}
