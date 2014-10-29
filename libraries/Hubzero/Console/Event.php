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
	 * @return void
	 **/
	public static function register($name, \Closure $callback)
	{
		self::$events[$name][] = $callback;
	}

	/**
	 * Fires registered event callbacks for a given event name
	 *
	 * @return void
	 **/
	public static function fire($name, Output $output)
	{
		if (isset(self::$events[$name]))
		{
			foreach (self::$events[$name] as $event)
			{
				if (is_callable($event))
				{
					$event();
				}
			}
		}
	}
}