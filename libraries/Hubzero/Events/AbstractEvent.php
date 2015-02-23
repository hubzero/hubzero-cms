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

use Serializable;
use ArrayAccess;
use Countable;

/**
 * Implementation of EventInterface.
 */
abstract class AbstractEvent implements EventInterface, ArrayAccess, Serializable, Countable
{
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
	 * Tell if the event propagation is stopped.
	 *
	 * @return  boolean  True if stopped, false otherwise.
	 */
	public function isStopped()
	{
		return true === $this->stopped;
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
