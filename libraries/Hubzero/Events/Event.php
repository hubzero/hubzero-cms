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

/**
 * Default Event class.
 */
class Event extends AbstractEvent
{
	use ErrorBag;

	/**
	 * An array of listeners that were triggered
	 *
	 * @var    array
	 */
	protected $triggered = array();

	/**
	 * An array of error messages or Exception objects.
	 *
	 * @var    array
	 */
	protected $response = array();

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
	 * Add to the list of listeners that were triggered.
	 *
	 * @param   mixed   $listener
	 * @return  object  This method is chainable.
	 */
	public function isTriggering($listener)
	{
		if (is_object($listener))
		{
			if ($listener instanceof WrappedListener)
			{
				$listener = $listener->getWrappedListener();
			}
			$listener = get_class($listener);
		}

		$this->triggered[] = $listener;

		return $this;
	}

	/**
	 * Get the list of triggered lsiteners.
	 *
	 * @return  boolean
	 */
	public function getTriggered()
	{
		return $this->triggered;
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
	 * Stop the event propagation.
	 *
	 * @return  void
	 */
	public function stop()
	{
		$this->stopped = true;
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
}
