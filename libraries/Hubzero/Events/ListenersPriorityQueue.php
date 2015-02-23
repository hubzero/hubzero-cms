<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Events;

use SplPriorityQueue;
use SplObjectStorage;
use IteratorAggregate;
use Countable;

/**
 * A class containing an inner listeners priority queue that can be iterated multiple times.
 * One instance of ListenersPriorityQueue is used per Event in the Dispatcher.
 */
class ListenersPriorityQueue implements IteratorAggregate, Countable
{
	/**
	 * The inner priority queue.
	 *
	 * @var  object  SplPriorityQueue
	 */
	protected $queue;

	/**
	 * A copy of the listeners contained in the queue
	 * that is used when detaching them to
	 * recreate the queue or to see if the queue contains
	 * a given listener.
	 *
	 * @var  object  SplObjectStorage
	 */
	protected $storage;

	/**
	 * A decreasing counter used to compute
	 * the internal priority as an array because
	 * SplPriorityQueue dequeues elements with the same priority.
	 *
	 * @var  integer
	 */
	private $counter = PHP_INT_MAX;

	/**
	 * Constructor.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->queue   = new SplPriorityQueue;
		$this->storage = new SplObjectStorage;
	}

	/**
	 * Add a listener with the given priority only if not already present.
	 *
	 * @param   object   $listener  The listener.
	 * @param   integer  $priority  The listener priority.
	 * @return  object   This method is chainable.
	 */
	public function add($listener, $priority)
	{
		if (!$this->storage->contains($listener))
		{
			// Compute the internal priority as an array.
			$priority = array($priority, $this->counter--);

			$this->storage->attach($listener, $priority);
			$this->queue->insert($listener, $priority);
		}

		return $this;
	}

	/**
	 * Remove a listener from the queue.
	 *
	 * @param   object  $listener  The listener.
	 * @return  object  This method is chainable.
	 */
	public function remove($listener)
	{
		if ($this->storage->contains($listener))
		{
			$this->storage->detach($listener);
			$this->storage->rewind();

			$this->queue = new SplPriorityQueue;

			foreach ($this->storage as $listener)
			{
				$priority = $this->storage->getInfo();
				$this->queue->insert($listener, $priority);
			}
		}

		return $this;
	}

	/**
	 * Tell if the listener exists in the queue.
	 *
	 * @param   object   $listener  The listener.
	 * @return  boolean  True if it exists, false otherwise.
	 */
	public function has($listener)
	{
		return $this->storage->contains($listener);
	}

	/**
	 * Get the priority of the given listener.
	 *
	 * @param   object  $listener  The listener.
	 * @param   mixed   $default   The default value to return if the listener doesn't exist.
	 * @return  mixed   The listener priority if it exists, null otherwise.
	 */
	public function getPriority($listener, $default = null)
	{
		if ($this->storage->contains($listener))
		{
			return $this->storage[$listener][0];
		}

		return $default;
	}

	/**
	 * Get all listeners contained in this queue, sorted according to their priority.
	 *
	 * @return  array  An array of listeners.
	 */
	public function getAll()
	{
		$listeners = array();

		// Get a clone of the queue.
		$queue = $this->getIterator();

		foreach ($queue as $listener)
		{
			$listeners[] = $listener;
		}

		return $listeners;
	}

	/**
	 * Get the inner queue with its cursor on top of the heap.
	 *
	 * @return  object  The inner queue.
	 */
	public function getIterator()
	{
		// SplPriorityQueue queue is a heap.
		$queue = clone $this->queue;

		if (!$queue->isEmpty())
		{
			$queue->top();
		}

		return $queue;
	}

	/**
	 * Count the number of listeners in the queue.
	 *
	 * @return  integer  The number of listeners in the queue.
	 */
	public function count()
	{
		return count($this->queue);
	}
}
