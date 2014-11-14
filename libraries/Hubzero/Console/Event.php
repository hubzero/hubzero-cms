<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

use Hubzero\Console\Config;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Simple event system for command line hooks
 **/
class Event
{
	/**
	 * The registered events
	 *
	 * @var array
	 **/
	private static $events = array();

	/**
	 * Registers an event callback
	 *
	 * @param  string $name the name of the hook to register the closure under
	 * @param  closure $callback the callback to fire when that hook is poked
	 * @return void
	 **/
	public static function register($name, \Closure $callback)
	{
		self::$events[$name][] = $callback;
	}

	/**
	 * Fires registered event callbacks for a given event name
	 *
	 * @param  string $name the event hook to fire
	 * @return void
	 **/
	public static function fire($name)
	{
		$args = func_get_args();
		unset($args[0]);

		if (isset(self::$events[$name]))
		{
			foreach (self::$events[$name] as $event)
			{
				if (is_callable($event))
				{
					call_user_func_array($event, $args);
				}
			}
		}
	}
}