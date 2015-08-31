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
