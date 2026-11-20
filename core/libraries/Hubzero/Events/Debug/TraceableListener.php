<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events\Debug;

//use Hubzero\Stopwatch\Stopwatch;
use Hubzero\Events\Event;
use Closure;

/**
 * Wrapper for event listeners to provide some extra
 * infor for debugging purposes.
 */
class TraceableListener
{
    /**
     * Listener
     *
     * @var  object
     */
    private $listener;

    /**
     * Called status
     *
     * @var  boolean
     */
    private $called;

    /**
     * Propagation stopper status
     *
     * @var  boolean
     */
    private $stoppedPropagation;

    /**
     * Constructor
     *
     * @param   object  $listener
     * @return  void
     */
    public function __construct($listener)
    {
        $this->listener = $listener;
        $this->called = false;
        $this->stoppedPropagation = false;
    }

    /**
     * Return the underlying lsitener
     *
     * @return  object
     */
    public function getWrappedListener()
    {
        return $this->listener;
    }

    /**
     * Was this listener called?
     *
     * @return  boolean
     */
    public function wasCalled()
    {
        return $this->called;
    }

    /**
     * Did this listener stop propagation?
     *
     * @return  boolean
     */
    public function stoppedPropagation()
    {
        return $this->stoppedPropagation;
    }

    /**
     * Did this listener stop propagation?
     *
     * @return  mixed
     */
    public function trigger(Event $event)
    {
        $this->called = true;

        //$e = $this->stopwatch->start($this->name, 'event_listener');

        // Registered only for events it handles, so it can always be called
        $handler  = $this->listener instanceof Closure ? $this->listener : [$this->listener, $event->getName()];
        $response = is_callable($handler) ? call_user_func($handler, $event) : null;

        /*if ($e->isStarted())
        {
            $e->stop();
        }*/

        if ($event->isStopped()) {
            $this->stoppedPropagation = true;
        }

        return $response;
    }
}
