<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Stub Event class for testing
 * The SoftDeletes trait fires events via \Event, but in test context we don't need them
 */
class EventStub
{
    /**
     * Trigger an event (no-op for testing)
     *
     * @param  string $event Event name
     * @param  array  $args  Event arguments
     * @return array
     */
    public static function trigger($event, $args = [])
    {
        return [];
    }
}
