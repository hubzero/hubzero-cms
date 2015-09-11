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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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