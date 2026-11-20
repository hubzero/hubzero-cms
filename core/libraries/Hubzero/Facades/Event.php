<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Event dispatcher facade
 *
 * @method static array<mixed>  trigger(mixed $event, array<mixed> $args = [])
 * @method static object addListener(object|\Closure $listener, array<mixed>|string $events = [])
 * @method static object listen(mixed $listener, array<mixed>|string $events = [])
 * @method static object removeListener(object $listener, mixed $event = null)
 * @method static object forget(object $listener, mixed $event = null)
 * @method static array<mixed>  getListeners(mixed $event = null)
 * @method static bool   hasListener(object $listener, mixed $event = null)
 * @method static int    countListeners(mixed $event)
 * @method static array<mixed>  getCalledListeners() Only while debugging, when the dispatcher is traceable
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
