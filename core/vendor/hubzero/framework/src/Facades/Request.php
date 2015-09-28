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
 * Request facade
 */
class Request extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return string
	 */
	protected static function getAccessor()
	{
		return 'request';
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable. Optional.
	 * @return  The request user state.
	 */
	public static function getState($key, $request, $default = null, $type = 'none')
	{
		$cur_state = \User::getState($key, $default);
		$new_state = self::getVar($request, null, 'default', $type);

		// Save the new value only if it was set in this request.
		if ($new_state !== null)
		{
			switch ($type)
			{
				case 'int':	  $new_state = intval($new_state); break;
				case 'word':  $new_state = preg_replace('/[^A-Z_]/i', '', $new_state); break;
				case 'cmd':   $new_state = preg_replace('/[^A-Z0-9_\.-]/i', '', $new_state); break;
				case 'bool':  $new_state = (bool) $new_state; break;
				case 'float': $new_state = preg_replace('/-?[0-9]+(\.[0-9]+)?/', '', $new_state); break;
				case 'string': $new_state = (string) $new_state; break;
				case 'array': $new_state = (array) $new_state; break;
			}

			\User::setState($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Checks for a form token in the request.
	 *
	 * Use in conjunction with Html::input('token').
	 *
	 * @param   string   $method  The request method in which to look for the token key.
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public static function checkToken($method = 'post')
	{
		return \App::get('session')->checkToken($method);
	}

	/**
	 * Checks for a honeypot in the request
	 *
	 * @param   string   $name
	 * @param   integer  $delay
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public static function checkHoneypot($name = null, $delay = 3)
	{
		$name = $name ?: \Hubzero\Spam\Honeypot::getName();

		if ($honey = self::getVar($name, array(), 'post'))
		{
			if (!\Hubzero\Spam\Honeypot::isValid($honey['p'], $honey['t'], $delay))
			{
				if (\App::has('log'))
				{
					$fallback = 'option=' . self::getCmd('option') . '&controller=' . self::getCmd('controller') . '&task=' . self::getCmd('task');
					$from = self::getVar('REQUEST_URI', $fallback, 'server');
					$from = $from ?: $fallback;

					\App::get('log')->logger('spam')->info('spam honeypot ' . self::ip() . ' ' . \User::get('id') . ' ' . \User::get('username') . ' ' . $from);
				}

				return false;
			}
		}

		return true;
	}
}