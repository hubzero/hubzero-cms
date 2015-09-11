<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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