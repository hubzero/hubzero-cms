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

namespace Hubzero\Facades;

/**
 * Abstract face class
 */
abstract class Facade
{
	/**
	 * The application instance being facaded.
	 *
	 * @var \Hubzero\Base\Application
	 */
	protected static $app;

	/**
	 * Get the application instance behind the facade.
	 *
	 * @return  object  \Hubzero\Base\Application
	 */
	public static function getApplication()
	{
		return static::$app;
	}

	/**
	 * Set the application instance.
	 *
	 * @param   object  $app  \Hubzero\Base\Application
	 * @return  void
	 */
	public static function setApplication($app)
	{
		static::$app = $app;
	}

	/**
	 * Hotswap the underlying instance behind the facade.
	 *
	 * @param   mixed  $instance
	 * @return  void
	 */
	public static function swap($instance)
	{
		static::$app[static::getAccessor()] = $instance;
	}

	/**
	 * Get the root object behind the facade.
	 *
	 * @return  mixed
	 */
	public static function getRoot()
	{
		return static::$app->get(static::getAccessor());
	}

	/**
	 * Get the registered name of the component.
	 *
	 * @return  string
	 */
	protected static function getAccessor()
	{
		throw new \RuntimeException('Facade does not implement getAccessor method.');
	}

	/**
	 * Create aliases
	 *
	 * @param   array  $aliases
	 * @return  void
	 */
	public static function createAliases(array $aliases)
	{
		// Create autoloader that creates class aliases during runtime
		spl_autoload_register(function($class) use ($aliases)
		{
			if (array_key_exists($class, $aliases))
			{
				return class_alias($aliases[$class], $class);
			}

			// Allow calling facade in namespaced class 
			// without resetting to the root namespace
			$classPieces = explode('\\', $class);
			$classAlt    = array_pop($classPieces);
			if (array_key_exists($classAlt, $aliases))
			{
				return class_alias($aliases[$classAlt], $class);
			}
		});
	}

	/**
	 * Handle dynamic, static calls to the object.
	 *
	 * @param   string  $method
	 * @param   array   $args
	 * @return  mixed
	 */
	public static function __callStatic($method, $args)
	{
		$instance = static::$app->get(static::getAccessor());

		// Seems counter-intuitive but the switch here can
		// actually be faster than calling call_user_func_array
		// every time.
		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}
}