<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

/**
 * Class loader for non PSR-4 classes
 *
 * This can autoload PSR-4 classes or cases where the class
 * name maps to a lowercase path:
 *
 *    Components\Example\Models\Entry
 *    -> /components/example/models/entry.php
 *
 * Inspired by Laravel 4's autoloader
 */
class ClassLoader
{
	/**
	 * The registered directories.
	 *
	 * @var  array
	 */
	protected static $directories = array();

	/**
	 * Indicates if a ClassLoader has been registered.
	 *
	 * @var  bool
	 */
	protected static $registered = false;

	/**
	 * Load the given class file.
	 *
	 * @param   string  $class
	 * @return  bool
	 */
	public static function load($class)
	{
		$class = static::normalizeClass($class);

		foreach (static::$directories as $directory)
		{
			if (file_exists($path = $directory . DIRECTORY_SEPARATOR . $class))
			{
				require_once $path;

				return true;
			}

			if (file_exists($path = $directory . DIRECTORY_SEPARATOR . strtolower($class)))
			{
				require_once $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * Get the normal file name for a class.
	 *
	 * @param   string  $class
	 * @return  string
	 */
	public static function normalizeClass($class)
	{
		if ($class[0] == '\\')
		{
			$class = substr($class, 1);
		}

		return str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
	}

	/**
	 * Register the given class loader on the auto-loader stack.
	 *
	 * @return  void
	 */
	public static function register()
	{
		if (!static::$registered)
		{
			static::$registered = spl_autoload_register(array('\Hubzero\Base\ClassLoader', 'load'));
		}
	}

	/**
	 * Add directories to the class loader.
	 *
	 * @param   string|array  $directories
	 * @return  void
	 */
	public static function addDirectories($directories)
	{
		static::$directories = array_unique(array_merge(static::$directories, (array) $directories));
	}

	/**
	 * Remove directories from the class loader.
	 *
	 * @param   string|array  $directories
	 * @return  void
	 */
	public static function removeDirectories($directories = null)
	{
		if (is_null($directories))
		{
			static::$directories = array();
		}
		else
		{
			static::$directories = array_diff(static::$directories, (array) $directories);
		}
	}

	/**
	 * Gets all the directories registered with the loader.
	 *
	 * @return  array
	 */
	public static function getDirectories()
	{
		return static::$directories;
	}
}
