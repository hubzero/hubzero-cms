<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2015 HUBzero Foundation, LLC.
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
 * @author	  Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Password;

class History
{
	/**
	 * User ID
	 *
	 * @var integer
	 */
	private $user_id;

	/**
	 * Log a message
	 *
	 * @param   string  $msg
	 * @return  void
	 */
	private function logDebug($msg)
	{
		$xlog = \App::get('log')->logger('debug');
		$xlog->debug($msg);
	}

	/**
	 * Get an instance of a user's password History
	 *
	 * @param   mixed  $instance  User ID (integer) or username (string)
	 * @return  object
	 */
	public static function getInstance($instance)
	{
		$db = \App::get('db');

		if (empty($db)) {
			return false;
		}

		$hzph = new self();

		if (is_numeric($instance) && $instance > 0)
		{
			$hzph->user_id = $instance;
		}
		else
		{
			$query = "SELECT id FROM `#__users` WHERE username=" .  $db->quote($instance) . ";";
			$db->setQuery($query);
			$result = $db->loadResult();
			if (is_numeric($result) && $result > 0)
			{
				$hzph->user_id = $result;
			}
		}

		if (empty($hzph->user_id))
		{
			return false;
		}

		return $hzph;
	}

	/**
	 * Add a passhash to a user's history
	 *
	 * @param   string  $passhash
	 * @param   string  $invalidated
	 * @return  boolean
	 */
	public function add($passhash = null, $invalidated = null)
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		if (empty($passhash))
		{
			$passhash = null;
		}

		if (empty($invalidated))
		{
			$invalidated = "UTC_TIMESTAMP()";
		}
		else
		{
			$invalidated = $db->quote($invalidated);
		}

		$user_id = $this->user_id;

		$query = "INSERT INTO `#__users_password_history` (user_id," .
			"passhash,invalidated)" .
			" VALUES ( " .
			$db->quote($user_id) . "," .
			$db->quote($passhash) . "," .
			$invalidated .
			");";

		$db->setQuery($query);

		$result = $db->query();

		if ($result !== false || $db->getErrorNum() == 1062)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if a password exists for a user
	 *
	 * @param   string  $password
	 * @param   string  $since
	 * @return  boolean
	 */
	public function exists($password = null, $since = null)
	{
		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$query = "SELECT `passhash` FROM `#__users_password_history` WHERE user_id = " . $db->quote($this->user_id);

		if (!empty($since))
		{
			$query .= " AND invalidated >= " . $db->quote($since);
		}

		$db->setQuery($query);

		$results = $db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $result)
			{
				$compare = \Hubzero\User\Password::comparePasswords($result->passhash, $password);
				if ($compare)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Remove a passhash from a user's history
	 *
	 * @param   string  $passhash
	 * @param   string  $timestamp
	 * @return  boolean
	 */
	public function remove($passhash, $timestamp)
	{
		if ($this->user_id <= 0)
		{
			return false;
		}

		$db = \App::get('db');

		if (empty($db))
		{
			return false;
		}

		$db->setQuery(
			"DELETE FROM `#__users_password_history` WHERE user_id= " .
			$db->quote($this->user_id) . " AND passhash = " .
			$db->quote($passhash) . " AND invalidated = " .
			$db->quote($timestamp) . ";"
		);

		if (!$db->query())
		{
			return false;
		}

		return true;
	}

	/**
	 * Shortcut helper method for adding
	 * a password to a user's history
	 *
	 * @param   string  $passhash
	 * @param   string  $user
	 * @return  boolean
	 */
	public static function addPassword($passhash, $user = null)
	{
		$hzuph = self::getInstance($user);
		$hzuph->add($passhash);

		return true;
	}
}
