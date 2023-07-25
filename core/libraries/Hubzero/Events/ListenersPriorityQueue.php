<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

use SplPriorityQueue;
use SplObjectStorage;
use IteratorAggregate;
use Countable;

/**
 * A class containing an inner listeners priority queue that can be iterated multiple times.
 * One instance of ListenersPriorityQueue is used per Event in the Dispatcher.
 *
 * Based on work by the Joomla Framework
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

	#[\ReturnTypeWillChange]
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

	#[\ReturnTypeWillChange]
	public function count()
	{
		return count($this->queue);
	}
}
