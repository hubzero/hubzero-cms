<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Console\Config;

/**
 * Simple event system for command line hooks
 **/
class Event
{
	/**
	 * The registered events
	 *
	 * @var  array
	 **/
	private static $events = array();

	/**
	 * Registers an event callback
	 *
	 * @param   string   $name      The name of the hook to register the closure under
	 * @param   closure  $callback  The callback to fire when that hook is poked
	 * @return  void
	 **/
	public static function register($name, \Closure $callback)
	{
		self::$events[$name][] = $callback;
	}

	/**
	 * Fires registered event callbacks for a given event name
	 *
	 * @param   string  $name  The event hook to fire
	 * @return  void
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