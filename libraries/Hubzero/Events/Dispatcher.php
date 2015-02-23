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

use InvalidArgumentException;
use Closure;

/**
 * Implementation of a DispatcherInterface supporting
 * prioritized listeners.
 */
class Dispatcher implements DispatcherInterface
{
	/**
	 * An array of registered events indexed by
	 * the event names.
	 *
	 * @var  array
	 */
	protected $events = array();

	/**
	 * An array of ListenersPriorityQueue indexed
	 * by the event names.
	 *
	 * @var  array
	 */
	protected $listeners = array();

	/**
	 * An array of ListenersPriorityQueue indexed
	 * by the event names.
	 *
	 * @var  array
	 */
	protected $loaders = array();

	/**
	 * Set an event to the dispatcher.
	 * It will replace any event with the same name.
	 *
	 * @param   object  $event  The event.
	 * @return  object  This method is chainable.
	 */
	public function setEvent(EventInterface $event)
	{
		$this->events[$event->getName()] = $event;

		return $this;
	}

	/**
	 * Add an event to this dispatcher, only if it is not existing.
	 *
	 * @param   object  $event  The event.
	 * @return  object  This method is chainable.
	 */
	public function addEvent(EventInterface $event)
	{
		if (!isset($this->events[$event->getName()]))
		{
			$this->events[$event->getName()] = $event;
		}

		return $this;
	}

	/**
	 * Tell if the given event has been added to this dispatcher.
	 *
	 * @param   mixed    $event  The event object or name.
	 * @return  boolean  True if the listener has the given event, false otherwise.
	 */
	public function hasEvent($event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		return isset($this->events[$event]);
	}

	/**
	 * Get the event object identified by the given name.
	 *
	 * @param   string  $name     The event name.
	 * @param   mixed   $default  The default value if the event was not registered.
	 * @return  EventInterface|mixed  The event of the default value.
	 */
	public function getEvent($name, $default = null)
	{
		if (isset($this->events[$name]))
		{
			return $this->events[$name];
		}

		return $default;
	}

	/**
	 * Remove an event from this dispatcher.
	 * The registered listeners will remain.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 * @return  Dispatcher  This method is chainable.
	 */
	public function removeEvent($event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		if (isset($this->events[$event]))
		{
			unset($this->events[$event]);
		}

		return $this;
	}

	/**
	 * Get the registered events.
	 *
	 * @return  array  The registered event.
	 */
	public function getEvents()
	{
		return $this->events;
	}

	/**
	 * Clear all events.
	 *
	 * @return  object
	 */
	public function clearEvents()
	{
		$this->events = array();

		return $this;
	}

	/**
	 * Count the number of registered event.
	 *
	 * @return  integer  The numer of registered events.
	 */
	public function countEvents()
	{
		return count($this->events);
	}

	/**
	 * Count the number of registered event.
	 *
	 * @return  integer  The numer of registered events.
	 */
	public function addListenerLoader(LoaderInterface $loader)
	{
		if (!isset($this->loaders[$loader->getName()]))
		{
			//$loader->setDispatcher($this);

			$this->loaders[$loader->getName()] = $loader;
		}

		return $this;
	}

	/**
	 * Remove a listener laoder from this dispatcher.
	 *
	 * @param   LoaderInterface|string  $event  The event object or name.
	 * @return  Dispatcher  This method is chainable.
	 */
	public function removeListenerLoader($loader)
	{
		if ($loader instanceof LoaderInterface)
		{
			$loader = $loader->getName();
		}

		if (isset($this->loaders[$loader]))
		{
			unset($this->loaders[$loader]);
		}

		return $this;
	}

	/**
	 * Remove a listener laoder from this dispatcher.
	 *
	 * @return  array
	 */
	public function getListenerLoaders()
	{
		return $this->loaders;
	}

	/**
	 * Add multiple listeners to this dispatcher. If a string is passed, it will loop
	 * through the list of listener loaders.
	 *
	 * @param   array|string  $listeners  The listener
	 * @param   array         $events     An associative array of event names as keys
	 *                                    and the corresponding listener priority as values.
	 * @return  Dispatcher    This method is chainable.
	 */
	public function addListeners($listeners, array $events = array())
	{
		if (is_string($listeners))
		{
			$loaded = array();
			foreach ($this->getListenerLoaders() as $loader)
			{
				$loaded += $loader->load($listeners);
			}
			$listeners = $loaded;
		}

		foreach ($listeners as $listener)
		{
			if (!$this->hasListener($listener))
			{
				$this->addListener($listener, $events);
			}
		}

		return $this;
	}

	/**
	 * Add a listener to this dispatcher, only if not already registered to these events.
	 * If no events are specified, it will be registered to all events matching it's methods name.
	 * In the case of a closure, you must specify at least one event name.
	 *
	 * @param   object|Closure  $listener  The listener
	 * @param   array           $events    An associative array of event names as keys
	 *                                     and the corresponding listener priority as values.
	 * @return  Dispatcher  This method is chainable.
	 * @throws  InvalidArgumentException
	 */
	public function addListener($listener, array $events = array())
	{
		if (!is_object($listener))
		{
			throw new InvalidArgumentException('The given listener is not an object.');
		}

		// We deal with a closure.
		if ($listener instanceof Closure)
		{
			if (empty($events))
			{
				throw new InvalidArgumentException('No event name(s) and priority specified for the Closure listener.');
			}

			foreach ($events as $name => $priority)
			{
				if (!isset($this->listeners[$name]))
				{
					$this->listeners[$name] = new ListenersPriorityQueue;
				}

				$this->listeners[$name]->add($listener, $priority);
			}

			return $this;
		}

		// We deal with a "normal" object.
		$methods = get_class_methods($listener);

		if (!empty($events))
		{
			$methods = array_intersect($methods, array_keys($events));
		}

		// Backwards compatibility
		if ($listener instanceof \JPlugin)
		{
			$listener = new WrappedListener($listener);
		}

		foreach ($methods as $event)
		{
			if (!isset($this->listeners[$event]))
			{
				$this->listeners[$event] = new ListenersPriorityQueue;
			}

			$priority = isset($events[$event]) ? $events[$event] : Priority::NORMAL;

			$this->listeners[$event]->add($listener, $priority);
		}

		return $this;
	}

	/**
	 * [!JOOMLA]
	 *
	 * @param   object  $listener  The listener.
	 * @return  void
	 */
	public function attach($listener)
	{
	}

	/**
	 * Get the priority of the given listener for the given event.
	 *
	 * @param   object|Closure         $listener  The listener.
	 * @param   EventInterface|string  $event     The event object or name.
	 * @return  mixed  The listener priority or null if the listener doesn't exist.
	 */
	public function getListenerPriority($listener, $event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getPriority($listener);
		}

		return null;
	}

	/**
	 * Get the listeners registered to the given event.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 * @return  object[]  An array of registered listeners sorted according to their priorities.
	 */
	public function getListeners($event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getAll();
		}

		return array();
	}

	/**
	 * Tell if the given listener has been added.
	 * If an event is specified, it will tell if the listener is registered for that event.
	 *
	 * @param   object|Closure         $listener  The listener.
	 * @param   EventInterface|string  $event     The event object or name.
	 * @return  boolean  True if the listener is registered, false otherwise.
	 */
	public function hasListener($listener, $event = null)
	{
		if ($event)
		{
			if ($event instanceof EventInterface)
			{
				$event = $event->getName();
			}

			if (isset($this->listeners[$event]))
			{
				return $this->listeners[$event]->has($listener);
			}
		}
		else
		{
			foreach ($this->listeners as $queue)
			{
				if ($queue->has($listener))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Remove the given listener from this dispatcher.
	 * If no event is specified, it will be removed from all events it is listening to.
	 *
	 * @param   object|Closure         $listener  The listener to remove.
	 * @param   EventInterface|string  $event     The event object or name.
	 * @return  Dispatcher  This method is chainable.
	 */
	public function removeListener($listener, $event = null)
	{
		if ($event)
		{
			if ($event instanceof EventInterface)
			{
				$event = $event->getName();
			}

			if (isset($this->listeners[$event]))
			{
				$this->listeners[$event]->remove($listener);
			}
		}

		else
		{
			foreach ($this->listeners as $queue)
			{
				$queue->remove($listener);
			}
		}

		return $this;
	}

	/**
	 * Clear the listeners in this dispatcher.
	 * If an event is specified, the listeners will be cleared only for that event.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 * @return  Dispatcher  This method is chainable.
	 */
	public function clearListeners($event = null)
	{
		if ($event)
		{
			if ($event instanceof EventInterface)
			{
				$event = $event->getName();
			}

			if (isset($this->listeners[$event]))
			{
				unset($this->listeners[$event]);
			}
		}

		else
		{
			$this->listeners = array();
		}

		return $this;
	}

	/**
	 * Count the number of registered listeners for the given event.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 * @return  integer  The number of registered listeners for the given event.
	 */
	public function countListeners($event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		return isset($this->listeners[$event]) ? count($this->listeners[$event]) : 0;
	}

	/**
	 * Trigger an event.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 * @return  EventInterface  The event after being passed through all listeners.
	 */
	public function trigger($event, $args = array())
	{
		if (!($event instanceof EventInterface))
		{
			if (isset($this->events[$event]))
			{
				$event = $this->events[$event];
			}
			else
			{
				$event = new Event($event);
			}
		}

		if ($group = $event->getGroup())
		{
			$this->addListeners($group);
		}

		foreach ($args as $name => $arg)
		{
			$event->addArgument($name, $arg);
		}

		if (isset($this->listeners[$event->getName()]))
		{
			foreach ($this->listeners[$event->getName()] as $listener)
			{
				if ($event->isStopped())
				{
					return $event;
				}

				$event->isTriggering($listener);

				if ($listener instanceof Closure)
				{

					$response = call_user_func($listener, $event);
				}
				else
				{
					$response = call_user_func(array($listener, $event->getName()), $event);
				}

				if (!is_null($response))
				{
					$event->addResponse($response);
				}
			}
		}

		return $event;
	}
}
