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

namespace Hubzero\Events\Debug;

use Hubzero\Events\DispatcherInterface;
use Hubzero\Events\WrappedListener;
use Hubzero\Events\Event;
use Hubzero\Stopwatch\Stopwatch;
use Hubzero\Log\Writer as Logger;

/**
 * Collects some data about event listeners.
 *
 * This event dispatcher delegates the dispatching to another one.
 */
class TraceableDispatcher implements DispatcherInterface
{
	/**
	 * Logger
	 *
	 * @var  object
	 */
	protected $logger;

	/**
	 * Timer
	 *
	 * @var  object
	 */
	protected $stopwatch;

	/**
	 * An array of triggered events.
	 *
	 * @var  array
	 */
	private $called;

	/**
	 * The event dispatcher to be traced
	 *
	 * @var  object
	 */
	private $dispatcher;

	/**
	 * Constructor.
	 *
	 * @param EventDispatcherInterface $dispatcher An EventDispatcherInterface instance
	 * @param Stopwatch                $stopwatch  A Stopwatch instance
	 * @param LoggerInterface          $logger     A LoggerInterface instance
	 */
	public function __construct(DispatcherInterface $dispatcher, $stopwatch = null, Logger $logger = null)
	{
		$this->dispatcher = $dispatcher;
		$this->stopwatch = $stopwatch;
		$this->logger = $logger;
		$this->called = array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function addListener($listener, array $events = array())
	{
		$this->dispatcher->addListener($listener, $events);
	}

	/**
	 * {@inheritdoc}
	 */
	public function removeListener($listener, $event = null)
	{
		return $this->dispatcher->removeListener($listener, $event);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getListeners($event)
	{
		return $this->dispatcher->getListeners($event);
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasListener($listener, $event)
	{
		return $this->dispatcher->hasListener($listener, $event);
	}

	/**
	 * {@inheritdoc}
	 */
	public function trigger($event, $args = array())
	{
		if (!($event instanceof Event))
		{
			$event = new Event($event);
		}

		// If a listener group was specified
		// lazy load the listeners.
		if ($group = $event->getGroup())
		{
			$this->dispatcher->addListeners($group);
		}

		$this->preProcess($event);
		$this->preTrigger($event);

		//$e = $this->stopwatch->start($event->getName(), 'section');

		//$responses = $this->dispatcher->trigger($event, $args);

		/*if ($e->isStarted())
		{
			$e->stop();
		}*/

		// Attach any incoming aruments
		foreach ((array) $args as $name => $arg)
		{
			$event->addArgument($name, $arg);
		}

		foreach ($this->dispatcher->getListeners($event->getName()) as $listener)
		{
			// Call the event listener
			$response = $listener->trigger($event);

			// Attach response
			if (!is_null($response))
			{
				$event->addResponse($response);
			}

			// Is propagation stopped?
			if ($event->isStopped())
			{
				break;
			}
		}

		$this->postTrigger($event);
		$this->postProcess($event);

		return $event->getResponse();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCalledListeners()
	{
		$called = array();

		foreach ($this->called as $eventName => $listeners)
		{
			foreach ($listeners as $listener)
			{
				$info = $this->getListenerInfo($listener->getWrappedListener(), $eventName);
				$called[$eventName . '.' . $info['pretty']] = $info;
			}
		}

		return $called;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getNotCalledListeners()
	{
		try
		{
			$allListeners = $this->getListeners();
		}
		catch (\Exception $e)
		{
			if (null !== $this->logger)
			{
				$this->logger->info(sprintf('An exception was thrown while getting the uncalled listeners (%s)', $e->getMessage()), array('exception' => $e));
			}

			// unable to retrieve the uncalled listeners
			return array();
		}

		$notCalled = array();
		foreach ($allListeners as $eventName => $listeners)
		{
			foreach ($listeners as $listener)
			{
				$called = false;
				if (isset($this->called[$eventName]))
				{
					foreach ($this->called[$eventName] as $l)
					{
						if ($l->getWrappedListener() === $listener)
						{
							$called = true;

							break;
						}
					}
				}

				if (!$called)
				{
					$info = $this->getListenerInfo($listener, $eventName);
					$notCalled[$eventName . '.' . $info['pretty']] = $info;
				}
			}
		}

		return $notCalled;
	}

	/**
	 * Proxies all method calls to the original event dispatcher.
	 *
	 * @param   string  $method     The method name
	 * @param   array   $arguments  The method arguments
	 * @return  mixed
	 */
	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->dispatcher, $method), $arguments);
	}

	/**
	 * Called before dispatching the event.
	 *
	 * @param Event  $event     The event
	 */
	protected function preTrigger(Event $event)
	{
	}

	/**
	 * Called after dispatching the event.
	 *
	 * @param   object  $event  The event
	 * @return  void
	 */
	protected function postTrigger(Event $event)
	{
	}

	/**
	 * Called before preTrigger
	 *
	 * @param   object  $event  The event
	 * @return  void
	 */
	private function preProcess(Event $event)
	{
		foreach ($this->dispatcher->getListeners($event) as $listener)
		{
			$priority = $this->dispatcher->getListenerPriority($listener, $event);

			//$this->dispatcher->removeListener($listener, $event);

			$info = $this->getListenerInfo($listener, $event->getName());
			$name = isset($info['class']) ? $info['class'] : $info['type'];

			$this->dispatcher->getListeners()[$event->getName()]->remove($listener);
			$this->dispatcher->getListeners()[$event->getName()]->add(new TraceableListener($listener, $name, $this->stopwatch), $priority);

			//$this->dispatcher->addListener(new TraceableListener($listener, $name, $this->stopwatch), array($event->getName() => $priority));
		}
	}

	/**
	 * Called after postTrigger
	 *
	 * @param   object  $event  The event
	 * @return  void
	 */
	private function postProcess(Event $event)
	{
		$skipped = false;

		foreach ($this->dispatcher->getListeners($event) as $listener)
		{
			if (!($listener instanceof TraceableListener))
			{
				// A new listener was added during dispatch.
				continue;
			}

			// Unwrap listener
			//$this->dispatcher->removeListener($listener, $event);
			//$this->dispatcher->addListener($listener->getWrappedListener(), $event);
			$priority = $this->dispatcher->getListenerPriority($listener, $event);

			$this->dispatcher->getListeners()[$event->getName()]->remove($listener);
			$this->dispatcher->getListeners()[$event->getName()]->add($listener->getWrappedListener(), $priority);

			$info = $this->getListenerInfo($listener->getWrappedListener(), $event->getName());

			$eventName = $event->getName();

			if ($listener->wasCalled())
			{
				if (null !== $this->logger)
				{
					$this->logger->debug(sprintf('Notified event "%s" to listener "%s".', $eventName, $info['pretty']));
				}

				if (!isset($this->called[$eventName]))
				{
					$this->called[$eventName] = new \SplObjectStorage();
				}

				$this->called[$eventName]->attach($listener);
			}

			if (null !== $this->logger && $skipped)
			{
				$this->logger->debug(sprintf('Listener "%s" was not called for event "%s".', $info['pretty'], $eventName));
			}

			if ($listener->stoppedPropagation())
			{
				if (null !== $this->logger)
				{
					$this->logger->debug(sprintf('Listener "%s" stopped propagation of the event "%s".', $info['pretty'], $eventName));
				}

				$skipped = true;
			}
		}
	}

	/**
	 * Returns information about the listener
	 *
	 * @param   object  $listener   The listener
	 * @param   string  $eventName  The event name
	 * @return  array   Information about the listener
	 */
	private function getListenerInfo($listener, $event)
	{
		$info = array(
			'event' => $event,
		);

		if ($listener instanceof \Closure)
		{
			$info += array(
				'type'   => 'Closure',
				'pretty' => 'closure',
			);
		}
		elseif (is_string($listener))
		{
			try
			{
				$r = new \ReflectionFunction($listener);
				$file = $r->getFileName();
				$line = $r->getStartLine();
			}
			catch (\ReflectionException $e)
			{
				$file = null;
				$line = null;
			}
			$info += array(
				'type'     => 'Function',
				'function' => $listener,
				'file'     => $file,
				'line'     => $line,
				'pretty'   => $listener,
			);
		}
		elseif (is_array($listener) || (is_object($listener))) // && is_callable($listener)))
		{
			if ($listener instanceof WrappedListener)
			{
				$listener = $listener->getWrappedListener();
			}

			if (!is_array($listener))
			{
				$listener = array($listener, $event);
			}

			$class = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];
			try
			{
				$r = new \ReflectionMethod($class, $listener[1]);
				$file = $r->getFileName();
				$line = $r->getStartLine();
			}
			catch (\ReflectionException $e)
			{
				$file = null;
				$line = null;
			}
			$info += array(
				'type'   => 'Method',
				'class'  => $class,
				'method' => $listener[1],
				'file'   => $file,
				'line'   => $line,
				'pretty' => $class . '::' . $listener[1],
			);
		}

		return $info;
	}
}
