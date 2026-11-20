<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

/**
 * Interface for event dispatchers.
 */
interface DispatcherInterface
{
    /**
     * Trigger an event.
     *
     * @param   mixed   $event  The event object or name.
     * @param   array<mixed>  $args
     * @return  array<mixed>  The event after being passed through all listeners.
     */
    public function trigger($event, $args = []);

    /**
     * Add a listener, for the events named or for those its methods are named after
     *
     * @param   object|\Closure  $listener  The listener
     * @param   array<mixed>|string     $events    Event names (as keys, with priorities), or one name
     * @return  mixed
     */
    public function addListener($listener, $events = []);
}
