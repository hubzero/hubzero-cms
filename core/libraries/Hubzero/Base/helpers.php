<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (! function_exists('app'))
{
	/**
	 * Get the root Facade application instance.
	 *
	 * Inspired by Laravel (http://laravel.com)
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	function app($key = null)
	{
		if (!is_null($key))
		{
			return app()->get($key);
		}

		return \Hubzero\Facades\Facade::getApplication();
	}
}

if (! function_exists('config'))
{
	/**
	 * Get the specified configuration value.
	 *
	 * Inspired by Laravel (http://laravel.com)
	 *
	 * @param   mixed  $key      array|string
	 * @param   mixed  $default  Default value if key isn't found
	 * @return  mixed
	 */
	function config($key = null, $default = null)
	{
		if (is_null($key))
		{
			return app('config');
		}

		return app('config')->get($key, $default);
	}
}

if (! function_exists('ddie'))
{
	/**
	 * Dump the passed variables and end the script.
	 *
	 * @param   mixed
	 * @return  void
	 */
	function ddie($var)
	{
		foreach (func_get_args() as $var)
		{
			\Hubzero\Debug\Dumper::dump($var);
		}
		die();
	}
}

if (! function_exists('dlog'))
{
	/**
	 * Dump the passed variables to the debug bar.
	 *
	 * @param   mixed
	 * @return  void
	 */
	function dlog()
	{
		foreach (func_get_args() as $var)
		{
			\Hubzero\Debug\Dumper::log($var);
		}
	}
}

if (! function_exists('dump'))
{
	/**
	 * Dump the passed variables.
	 *
	 * @param   mixed
	 * @return  void
	 */
	function dump($var)
	{
		foreach (func_get_args() as $var)
		{
			\Hubzero\Debug\Dumper::dump($var);
		}
	}
}

if (! function_exists('with'))
{
	/**
	 * Return the given object. Useful for chaining.
	 *
	 * Inspired by Laravel (http://laravel.com)
	 *
	 * @param   mixed  $object
	 * @return  mixed
	 */
	function with($object)
	{
		return $object;
	}
}

if (! function_exists('classExists'))
{
	/**
	 * Checks for the existence of the provided class without
	 * diving into the HUBzero Facade autoloader.
	 *
	 * @param   string  $classname  The classname to look for
	 * @return  bool
	 **/
	function classExists($classname)
	{
		$result = false;

		foreach (spl_autoload_functions() as $loader)
		{
			if (is_array($loader) && isset($loader[0]) && $loader[0] == 'Hubzero\Facades\Facade')
			{
				$autoloader = $loader;
				break;
			}
		}

		if (isset($autoloader))
		{
			spl_autoload_unregister($autoloader);

			$result = class_exists($classname);

			spl_autoload_register($autoloader);
		}

		return $result;
	}
}
