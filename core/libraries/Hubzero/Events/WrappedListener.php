<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
