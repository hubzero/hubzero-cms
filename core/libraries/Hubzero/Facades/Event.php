<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Event dispatcher facade
 *
 * @method static array  trigger(mixed $event, array $args = [])
 * @method static object addListener(object|\Closure $listener, array $events = [])
 * @method static object listen(mixed $listener, array $events = [])
 * @method static object removeListener(object $listener, mixed $event = null)
 * @method static object forget(object $listener, mixed $event = null)
 * @method static array  getListeners(mixed $event = null)
 * @method static bool   hasListener(object $listener, mixed $event = null)
 * @method static int    countListeners(mixed $event)
 */
class Event extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'dispatcher';
    }
}
