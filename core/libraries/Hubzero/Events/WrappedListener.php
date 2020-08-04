<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

use InvalidArgumentException;

/**
 * A listener delegating its methods to an other listener.
 */
final class WrappedListener
{
	/**
	 * Listener class
	 *
	 * @var  object
	 */
	private $listener;

	/**
	 * Has the listener been called
	 *
	 * @var  boolean
	 */
	private $called;

	/**
	 * Constructor.
	 *
	 * @param   object  $listener
	 * @return  void
	 */
	public function __construct($listener)
	{
		$this->listener = $listener;
		$this->called   = false;
	}

	/**
	 * Get the listener
	 *
	 * @return  object
	 */
	public function getWrappedListener()
	{
		return $this->listener;
	}

	/**
	 * Load the given listener group.
	 *
	 * @param   string  $method
	 * @param   array   $arguments
	 * @return  mixed
	 */
	public function __call($method, $arguments)
	{
		$this->called = true;

		$event = isset($arguments[0]) ? $arguments[0] : new Event($method);

		$this->listener->event = $event;
		$args = $event->getArguments();

		// A bit ugly but we need to take into account cases where
		// arguments have named indices (i.e., associative array)
		if (count($args) && !isset($args[0]))
		{
			$args = array_values($args);
		}

		switch (count($args))
		{
			case 0:
				return $this->listener->$method();

			case 1:
				return $this->listener->$method($args[0]);

			case 2:
				return $this->listener->$method($args[0], $args[1]);

			case 3:
				return $this->listener->$method($args[0], $args[1], $args[2]);

			case 4:
				return $this->listener->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($this->listener, $method), $args);
		}
	}
}
