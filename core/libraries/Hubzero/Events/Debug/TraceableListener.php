<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
