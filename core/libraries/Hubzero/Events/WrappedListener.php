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

/**
 * A listener delegating its methods to an other listener.
 */
final class WrappedListener
{
	/**
	 * Listener class
	 *
	 * @var  object
	 */
	private $listener;

	/**
	 * Has the listener been called
	 *
	 * @var  boolean
	 */
	private $called;

	/**
	 * Constructor.
	 *
	 * @param   object  $listener
	 * @return  void
	 */
	public function __construct($listener)
	{
		$this->listener = $listener;
		$this->called   = false;
	}

	/**
	 * Get the listener
	 *
	 * @return  object
	 */
	public function getWrappedListener()
	{
		return $this->listener;
	}

	/**
	 * Load the given listener group.
	 *
	 * @param   string  $method
	 * @param   array   $arguments
	 * @return  mixed
	 * @since   2.0
	 */
	public function __call($method, $arguments)
	{
		$this->called = true;

		$event = isset($arguments[0]) ? $arguments[0] : new Event($method);

		$this->listener->event = $event;
		$args = $event->getArguments();

		switch (count($args))
		{
			case 0:
				return $this->listener->$method();

			case 1:
				return $this->listener->$method($args[0]);

			case 2:
				return $this->listener->$method($args[0], $args[1]);

			case 3:
				return $this->listener->$method($args[0], $args[1], $args[2]);

			case 4:
				return $this->listener->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($this->listener, $method), $args);
		}
	}
}
