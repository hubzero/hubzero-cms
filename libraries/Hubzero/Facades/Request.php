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
 * Request facade
 */
class Request extends \JRequest
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
	 * Get the current path info for the request.
	 *
	 * @return  string
	 */
	public static function base($pathonly = false)
	{
		return \JURI::base($pathonly);
	}

	/**
	 * Returns the root URI for the request.
	 *
	 * @param   boolean  $pathonly  If false, prepend the scheme, host and port information. Default is false.
	 * @param   string   $path      The path
	 * @return  string  The root URI string.
	 */
	public static function root($pathonly = false, $path = null)
	{
		return \JURI::root($pathonly, $path);
	}

	/**
	 * Returns the URL for the request, minus the query.
	 *
	 * @return  string
	 */
	public static function current()
	{
		return \JURI::current();
	}

	/**
	 * Returns the path.
	 *
	 * @return  string
	 */
	public static function path()
	{
		return \JURI::getInstance()->getPath();
	}

	/**
	 * Temporary placeholder
	 *
	 * @return  void
	 */
	public static function createFromGlobals()
	{
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
}