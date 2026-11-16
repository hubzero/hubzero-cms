<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Observer that can cancel events
 */
class CancelingObserver
{
    /**
     * @var  array<mixed>
     */
    protected static $eventsToCancel = [];
    /**
     * @var  array<mixed>
     */
    protected static $called = [];

    /**
     * @return  void
     */
    public static function reset()
    {
        static::$eventsToCancel = [];
        static::$called = [];
    }

    /**
     * @param   string  $event
     * @return  void
     */
    public static function cancelEvent($event)
    {
        static::$eventsToCancel[$event] = true;
    }

    /**
     * @param   mixed  $event
     * @return  bool
     */
    public static function wasCalled($event)
    {
        return isset(static::$called[$event]);
    }

    /**
     * @param   string  $event
     * @param   mixed  $model
     * @return  false|null
     */
    protected function handleEvent($event, $model)
    {
        static::$called[$event] = true;

        if (isset(static::$eventsToCancel[$event])) {
            return false;
        }

        return null;
    }

    /**
     * @param   mixed  $model
     * @return  mixed
     */
    public function creating($model)
    {
        return $this->handleEvent('creating', $model);
    }

    /**
     * @param   mixed  $model
     * @return  mixed
     */
    public function updating($model)
    {
        return $this->handleEvent('updating', $model);
    }

    /**
     * @param   mixed  $model
     * @return  mixed
     */
    public function saving($model)
    {
        return $this->handleEvent('saving', $model);
    }

    /**
     * @param   mixed  $model
     * @return  mixed
     */
    public function deleting($model)
    {
        return $this->handleEvent('deleting', $model);
    }
}
