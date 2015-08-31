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

use Hubzero\Base\Traits\ErrorBag;
use InvalidArgumentException;
use Serializable;
use ArrayAccess;
use Countable;

/**
 * Default Event class.
 *
 * Based on work by the Joomla Framework
 */
class Event implements ArrayAccess, Serializable, Countable
{
	use ErrorBag;

	/**
	 * The event name.
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * The event group name.
	 *
	 * @var  string
	 */
	protected $group;

	/**
	 * The event arguments.
	 *
	 * @var  array
	 */
	protected $arguments;

	/**
	 * A flag to see if the event propagation is stopped.
	 *
	 * @var  boolean
	 */
	protected $stopped = false;

	/**
	 * An array of error messages or Exception objects.
	 *
	 * @var    array
	 */
	protected $response = array();

	/**
	 * Constructor.
	 *
	 * @param  string  $name       The event name.
	 * @param  array   $arguments  The event arguments.
	 */
	public function __construct($name, array $arguments = array())
	{
		if (strstr($name, '.'))
		{
			$this->group = strstr($name, '.', true);
			$name = ltrim(strstr($name, '.'), '.');
		}
		$this->name      = $name;
		$this->arguments = $arguments;
	}

	/**
	 * Get the event group name.
	 *
	 * @return  string  The event group name.
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Add an event argument, only if it is not existing.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 * @return  object  This method is chainable.
	 */
	public function addArgument($name, $value)
	{
		if (!isset($this->arguments[$name]))
		{
			$this->arguments[$name] = $value;
		}

		return $this;
	}

	/**
	 * Set the value of an event argument.
	 * If the argument already exists, it will be overridden.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 * @return  object  This method is chainable.
	 */
	public function setArgument($name, $value)
	{
		$this->arguments[$name] = $value;

		return $this;
	}

	/**
	 * Remove an event argument.
	 *
	 * @param   string  $name  The argument name.
	 * @return  object  This method is chainable.
	 */
	public function removeArgument($name)
	{
		if (isset($this->arguments[$name]))
		{
			unset($this->arguments[$name]);
		}

		return $this;
	}

	/**
	 * Clear all event arguments.
	 *
	 * @return  array  The old arguments.
	 */
	public function clearArguments()
	{
		$this->arguments = array();

		return $this;
	}

	/**
	 * Get an event argument value.
	 *
	 * @param   string  $name     The argument name.
	 * @param   mixed   $default  The default value if not found.
	 * @return  mixed  The argument value or the default value.
	 */
	public function getArgument($name, $default = null)
	{
		if (isset($this->arguments[$name]))
		{
			return $this->arguments[$name];
		}

		return $default;
	}

	/**
	 * Tell if the given event argument exists.
	 *
	 * @param   string  $name  The argument name.
	 * @return  boolean  True if it exists, false otherwise.
	 */
	public function hasArgument($name)
	{
		return isset($this->arguments[$name]);
	}

	/**
	 * Get all event arguments.
	 *
	 * @return  array  An associative array of argument names as keys
	 *                 and their values as values.
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * Count the number of arguments.
	 *
	 * @return  integer  The number of arguments.
	 */
	public function count()
	{
		return count($this->arguments);
	}

	/**
	 * Serialize the event.
	 *
	 * @return  string  The serialized event.
	 */
	public function serialize()
	{
		return serialize(array($this->name, $this->arguments, $this->stopped));
	}

	/**
	 * Unserialize the event.
	 *
	 * @param   string  $serialized  The serialized event.
	 * @return  void
	 */
	public function unserialize($serialized)
	{
		list($this->name, $this->arguments, $this->stopped) = unserialize($serialized);
	}

	/**
	 * Add an error message.
	 *
	 * @param   string  $error  Error message.
	 * @param   string  $key    Specific key to set the value to
	 * @return  object  This method is chainable.
	 */
	public function addResponse($data)
	{
		array_push($this->response, $data);

		return $this;
	}

	/**
	 * Get the list of responses from triggered listeners.
	 *
	 * @return  array
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Stop the event propagation.
	 *
	 * @return  void
	 */
	public function stop()
	{
		$this->stopped = true;
	}

	/**
	 * Resume the event propagation.
	 *
	 * @return  void
	 */
	public function resume()
	{
		$this->stopped = false;
	}

	/**
	 * Tell if the event propagation is stopped.
	 *
	 * @return  boolean  True if stopped, false otherwise.
	 */
	public function isStopped()
	{
		return true === $this->stopped;
	}

	/**
	 * Set the value of an event argument.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 * @return  void
	 * @throws  InvalidArgumentException  If the argument name is null.
	 */
	public function offsetSet($name, $value)
	{
		if (is_null($name))
		{
			throw new InvalidArgumentException('The argument name cannot be null.');
		}

		$this->setArgument($name, $value);
	}

	/**
	 * Remove an event argument.
	 *
	 * @param   string  $name  The argument name.
	 * @return  void
	 */
	public function offsetUnset($name)
	{
		$this->removeArgument($name);
	}

		/**
	 * Tell if the given event argument exists.
	 *
	 * @param   string  $name  The argument name.
	 * @return  boolean  True if it exists, false otherwise.
	 */
	public function offsetExists($name)
	{
		return $this->hasArgument($name);
	}

	/**
	 * Get an event argument value.
	 *
	 * @param   string  $name  The argument name.
	 * @return  mixed  The argument value or null if not existing.
	 */
	public function offsetGet($name)
	{
		return $this->getArgument($name);
	}
}
