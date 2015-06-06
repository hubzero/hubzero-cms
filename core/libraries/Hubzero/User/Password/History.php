<?php
/**
 * HUBzero CMS
 *
 * Copyright 2010-2012 Purdue University. All rights reserved.
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
 * @author	  Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2010-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @param   string $msg
	 * @return  void
	 */
	private function logDebug($msg)
	{
		$xlog = \JFactory::getLogger();
		$xlog->debug($msg);
	}

	/**
	 * Get an instance of a user's password History
	 *
	 * @param   mixed $instance User ID (integer) or username (string)
	 * @return  object
	 */
	public static function getInstance($instance)
	{
		$db = \JFactory::getDBO();

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
			$query = "SELECT id FROM #__users WHERE username=" .  $db->Quote($instance) . ";";
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
	 * @param   string $passhash
	 * @param   string $invalidated
	 * @return  boolean
	 */
	public function add($passhash = null, $invalidated = null)
	{
		$db = \JFactory::getDBO();

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
			$invalidated = $db->Quote($invalidated);
		}

		$user_id = $this->user_id;

		$query = "INSERT INTO #__users_password_history(user_id," .
			"passhash,invalidated)" .
			" VALUES ( " .
			$db->Quote($user_id) . "," .
			$db->Quote($passhash) . "," .
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
	 * @param   string $password
	 * @param   string $since
	 * @return  boolean
	 */
	public function exists($password = null, $since = null)
	{
		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		$query = "SELECT `passhash` FROM `#__users_password_history` WHERE user_id = " . $db->Quote($this->user_id);

		if (!empty($since))
		{
			$query .= " AND invalidated >= " . $db->Quote($since);
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
	 * @param   string $passhash
	 * @param   string $timestamp
	 * @return  boolean
	 */
	public function remove($passhash, $timestamp)
	{
		if ($this->user_id <= 0)
		{
			return false;
		}

		$db = \JFactory::getDBO();

		if (empty($db))
		{
			return false;
		}

		$db->setQuery("DELETE FROM #__users_password_history WHERE user_id= " .
			$db->Quote($this->user_id) . " AND passhash = " .
			$db->Quote($passhash) . " AND invalidated = " .
			$db->Quote($timestamp) . ";");

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
	 * @param   string $passhash
	 * @param   string $user
	 * @return  boolean
	 */
	public static function addPassword($passhash, $user = null)
	{
		$hzuph = self::getInstance($user);
		$hzuph->add($passhash);

		return true;
	}
}
