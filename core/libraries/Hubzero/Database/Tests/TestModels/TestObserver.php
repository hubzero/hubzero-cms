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
    /**
     * @var  array<mixed>
     */
    protected static $called = [];
    /**
     * @var  array<mixed>
     */
    protected static $models = [];
    /**
     * @var  array<mixed>
     */
    protected static $callTimes = [];
    /**
     * @var  array<mixed>
     */
    protected static $callCounts = [];
    /**
     * @var  int
     */
    protected static $callSequence = 0;
    /**
     * @var  array<mixed>
     */
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
     * @param   string  $event
     * @return  bool
     */
    public static function wasCalled($event)
    {
        return isset(static::$called[$event]) && static::$called[$event];
    }

    /**
     * @param   string  $event
     * @return  mixed
     */
    public static function getModel($event)
    {
        return static::$models[$event] ?? null;
    }

    /**
     * @param   mixed  $event
     * @return  mixed
     */
    public static function getCallTime($event)
    {
        return static::$callTimes[$event] ?? null;
    }

    /**
     * @param   string  $event
     * @return  mixed
     */
    public static function getCallOrder($event)
    {
        return static::$callOrder[$event] ?? null;
    }

    /**
     * @param   string  $event
     * @return  mixed
     */
    public static function getCallCount($event)
    {
        return static::$callCounts[$event] ?? 0;
    }

    /**
     * @param   string  $event
     * @param   mixed  $model
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
     * @param   mixed  $model
     * @return  void
     */
    public function creating($model)
    {
        $this->recordCall('creating', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function created($model)
    {
        $this->recordCall('created', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function updating($model)
    {
        $this->recordCall('updating', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function updated($model)
    {
        $this->recordCall('updated', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function saving($model)
    {
        $this->recordCall('saving', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function saved($model)
    {
        $this->recordCall('saved', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function deleting($model)
    {
        $this->recordCall('deleting', $model);
    }

    /**
     * @param   mixed  $model
     * @return  void
     */
    public function deleted($model)
    {
        $this->recordCall('deleted', $model);
    }
}
