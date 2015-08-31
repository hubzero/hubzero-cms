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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Session;

use Hubzero\Session\Storage;

/**
 * Session helper
 */
class Helper
{
	/**
	 * Get Session storage class
	 * 
	 * @return  object
	 */
	public static function storage()
	{
		// get storage handler (from config)
		$storageHandler = \Config::get('session_handler');

		// create storage class
		$storageClass = __NAMESPACE__ . '\\Storage\\' . ucfirst($storageHandler);

		// return new instance of storage class
		return new $storageClass();
	}

	/**
	 * Get Session by id
	 * 
	 * @param   integer  $id  Session ID
	 * @return  object
	 */
	public static function getSession($id)
	{
		return self::storage()->session($id);
	}

	/**
	 * Get Session by User Id
	 * 
	 * @param   integer  $id  User ID
	 * @return  mixed
	 */
	public static function getSessionWithUserId($userid)
	{
		// get list of all sessions
		$sessions = self::storage()->all(array(
			'guest'    => 0,
			'distinct' => 1
		));

		// see if any session matches our userid
		foreach ($sessions as $session)
		{
			if ($session->userid == $userid)
			{
				return $session;
			}
		}

		// nothing found
		return null;
	}

	/**
	 * Get list of all sessions
	 * 
	 * @param   array  $filters  Filters to apply
	 * @return  array
	 */
	public static function getAllSessions($filters = array())
	{
		return self::storage()->all($filters);
	}
}