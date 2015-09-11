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
