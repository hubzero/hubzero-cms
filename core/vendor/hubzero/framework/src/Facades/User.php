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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * User facade
 */
class User extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getAccessor()
	{
		return 'user';
	}

	/**
	 * Is the current user a guest (logged out) or not?
	 *
	 * @return  boolean
	 */
	public static function isGuest()
	{
		return static::getRoot()->get('guest');
	}

	/**
	 * Get the root object behind the facade.
	 *
	 * @return  mixed
	 */
	public static function getRoot()
	{
		return \JFactory::getUser();
	}

	/**
	 * Gets a user state.
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 * @return  mixed   The user state or null.
	 */
	public static function getState($key, $default = null)
	{
		$session  = self::$app->get('session');
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			return $registry->get($key, $default);
		}

		return $default;
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  $key    The path of the state.
	 * @param   string  $value  The value of the variable.
	 * @return  mixed   The previous state, if one existed.
	 */
	public static function setState($key, $value)
	{
		$session  = self::$app->get('session');
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			return $registry->set($key, $value);
		}

		return null;
	}
}