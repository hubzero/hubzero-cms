<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events\Debug;

//use Hubzero\Stopwatch\Stopwatch;
use Hubzero\Events\DispatcherInterface;
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
	 * Listener name
	 *
	 * @var  string
	 */
	private $name;

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
	 * Timer
	 *
	 * @var  object
	 */
	private $stopwatch;

	/**
	 * Constructor
	 *
	 * @param   object  $listener
	 * @param   string  $name
	 * @param   object  $stopwatch
	 * @return  void
	 */
	public function __construct($listener, $name, $stopwatch)
	{
		$this->listener = $listener;
		$this->name = $name;
		$this->stopwatch = $stopwatch;
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

		if ($this->listener instanceof Closure)
		{
			$response = call_user_func($this->listener, $event);
		}
		else
		{
			$response = call_user_func(array($this->listener, $event->getName()), $event);
		}

		/*if ($e->isStarted())
		{
			$e->stop();
		}*/

		if ($event->isStopped())
		{
			$this->stoppedPropagation = true;
		}

		return $response;
	}
}
