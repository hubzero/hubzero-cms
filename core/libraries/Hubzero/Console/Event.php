<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
