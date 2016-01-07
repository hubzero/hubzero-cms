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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

if ( ! function_exists('app'))
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

if ( ! function_exists('config'))
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

if ( ! function_exists('ddie'))
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

if ( ! function_exists('dlog'))
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

if ( ! function_exists('dump'))
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

if ( ! function_exists('with'))
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

if ( ! function_exists('classExists'))
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
