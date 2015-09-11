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

namespace Hubzero\Events;

use InvalidArgumentException;
use Closure;

/**
 * Implementation of a DispatcherInterface supporting
 * prioritized listeners.
 *
 * Inspired by the Joomla Framework Event package,
 * and the Laravel event handler.
 */
class Dispatcher implements DispatcherInterface
{
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
				$loaded += $loader->loadListeners($listeners);
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
	public function addListener($listener, $events = array())
	{
		if (!is_object($listener))
		{
			throw new InvalidArgumentException('The given listener is not an object.');
		}

		if (is_string($events))
		{
			$events = array($events => Priority::NORMAL);
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
				$name = (strstr($name, '.') ? ltrim(strstr($name, '.'), '.') : $name);

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
		if (!($listener instanceof ListenerInterface))
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
	 * Thsi is an alias for addListener()
	 *
	 * @param   mixed  $listener
	 * @param   array  $events
	 * @throws  InvalidArgumentException
	 */
	public function listen($listener, $events = array())
	{
		return $this->addListener($listener, $events);
	}

	/**
	 * [!] Compatibility
	 *
	 * @param   object  $listener  The listener.
	 * @return  void
	 */
	public function attach($listener)
	{
		return $this->addListener($listener);
	}

	/**
	 * Get the priority of the given listener for the given event.
	 *
	 * @param   object  $listener  The listener.
	 * @param   mixed   $event     The event object or name.
	 * @return  mixed   The listener priority or null if the listener doesn't exist.
	 */
	public function getListenerPriority($listener, $event)
	{
		$event = $this->resolveEventName($event);

		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getPriority($listener);
		}

		return null;
	}

	/**
	 * Get the listeners registered to the given event.
	 *
	 * @param   mixed  $event  The event object or name.
	 * @return  array  An array of registered listeners sorted according to their priorities.
	 */
	public function getListeners($event = null)
	{
		$event = $this->resolveEventName($event);

		if ($event)
		{
			if (isset($this->listeners[$event]))
			{
				return $this->listeners[$event]->getAll();
			}
			return array();
		}

		return $this->listeners;
	}

	/**
	 * Tell if the given listener has been added.
	 * If an event is specified, it will tell if the listener is registered for that event.
	 *
	 * @param   object   $listener  The listener.
	 * @param   mixed    $event     The event object or name.
	 * @return  boolean  True if the listener is registered, false otherwise.
	 */
	public function hasListener($listener, $event = null)
	{
		if ($event)
		{
			$event = $this->resolveEventName($event);

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
	 * @param   object  $listener  The listener to remove.
	 * @param   mixed   $event     The event object or name.
	 * @return  object  This method is chainable.
	 */
	public function removeListener($listener, $event = null)
	{
		if ($event)
		{
			$event = $this->resolveEventName($event);

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
	 * This is an alias for removeListener()
	 *
	 * @param   object  $listener  The listener to remove.
	 * @param   mixed   $event     The event object or name.
	 * @return  object  This method is chainable.
	 */
	public function forget($listener, $event = null)
	{
		return $this->removeListener($listener, $event);
	}

	/**
	 * Clear the listeners in this dispatcher.
	 * If an event is specified, the listeners will be cleared only for that event.
	 *
	 * @param   mixed   $event  The event object or name.
	 * @return  object  This method is chainable.
	 */
	public function clearListeners($event = null)
	{
		if ($event)
		{
			$event = $this->resolveEventName($event);

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
		$event = $this->resolveEventName($event);

		return isset($this->listeners[$event]) ? count($this->listeners[$event]) : 0;
	}

	/**
	 * Trigger an event.
	 *
	 * @param   mixed  $event  The event object or name.
	 * @return  array  The event after being passed through all listeners.
	 */
	public function trigger($event, $args = array())
	{
		if (!($event instanceof Event))
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

		// If a listener group was specified
		// lazy load the listeners.
		if ($group = $event->getGroup())
		{
			$this->addListeners($group);
		}

		// Attach any incoming aruments
		foreach ((array) $args as $name => $arg)
		{
			$event->addArgument($name, $arg);
		}

		// Are there any listeners for this event?
		if (isset($this->listeners[$event->getName()]))
		{
			foreach ($this->listeners[$event->getName()] as $listener)
			{
				// Call the event listener
				if ($listener instanceof Closure)
				{
					$response = call_user_func($listener, $event);
				}
				else
				{
					$response = call_user_func(array($listener, $event->getName()), $event);
				}

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
		}

		return $event->getResponse();
	}

	/**
	 * Trigger an event.
	 *
	 * @param   mixed  $event  The event object or name.
	 * @return  array  The event after being passed through all listeners.
	 */
	protected function resolveEventName($event)
	{
		if ($event instanceof Event)
		{
			$event = $event->getName();
		}
		return $event;
	}
}
