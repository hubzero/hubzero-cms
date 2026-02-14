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
    protected static $eventsToCancel = [];
    protected static $called = [];

    public static function reset()
    {
        static::$eventsToCancel = [];
        static::$called = [];
    }

    public static function cancelEvent($event)
    {
        static::$eventsToCancel[$event] = true;
    }

    public static function wasCalled($event)
    {
        return isset(static::$called[$event]);
    }

    protected function handleEvent($event, $model)
    {
        static::$called[$event] = true;

        if (isset(static::$eventsToCancel[$event])) {
            return false;
        }
    }

    public function creating($model)
    {
        return $this->handleEvent('creating', $model);
    }

    public function updating($model)
    {
        return $this->handleEvent('updating', $model);
    }

    public function saving($model)
    {
        return $this->handleEvent('saving', $model);
    }

    public function deleting($model)
    {
        return $this->handleEvent('deleting', $model);
    }
}
