<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Abstract face class
 *
 * Heavily influenced by Laravel
 * http://laravel.com
 */
abstract class Facade
{
	/**
	 * The application instance being facaded.
	 *
	 * @var  object  \Hubzero\Base\Application
	 */
	protected static $app;

	/**
	 * The application aliases being loaded
	 *
	 * @var  array
	 */
	protected static $aliases;

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
		static::$aliases = $aliases;

		// Create autoloader that creates class aliases during runtime
		spl_autoload_register(__NAMESPACE__ . '\Facade::loadAliases');
	}

	/**
	 * Load aliases
	 *
	 * @param   string  $class  The class being loaded
	 * @return  void
	 */
	public static function loadAliases($class)
	{
		$aliases = static::$aliases;

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
		$instance = static::getRoot();

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
