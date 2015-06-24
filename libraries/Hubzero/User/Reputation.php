<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User;

/**
 * User reputation model
 */
class Reputation
{
	/**
	 * Increments user spam count, both globally and in current session
	 *
	 * @return bool
	 **/
	public static function incrementSpamCount()
	{
		// Save global spam count
		$db   = \JFactory::getDbo();
		$user = \JFactory::getUser();

		if (!$user->get('guest'))
		{
			$query = "SELECT * FROM `#__user_reputation` WHERE `user_id` = " . $db->quote($user->get('id'));
			$db->setQuery($query);

			$current = $db->loadObject();

			if ($current)
			{
				$query = "UPDATE `#__user_reputation` SET `spam_count` = " . (int)($current->spam_count+1) . " WHERE `user_id` = " . (int)$current->user_id;
				$db->setQuery($query);
				$db->query();
			}
			else
			{
				$query = "INSERT INTO `#__user_reputation` (`user_id`, `spam_count`) VALUES (" . $db->quote($user->get('id')) . ", 1)";
				$db->setQuery($query);
				$db->query();
			}

			// Also increment session spam count
			$current = \JFactory::getSession()->get('spam_count', 0);
			\JFactory::getSession()->set('spam_count', ($current+1));
		}
	}

	/**
	 * Checks to see if user is jailed
	 *
	 * @return bool
	 **/
	public static function isJailed()
	{
		$db   = \JFactory::getDbo();
		$user = \JFactory::getUser();

		$query = "SELECT * FROM `#__user_reputation` WHERE `user_id` = " . $db->quote($user->get('id'));
		$db->setQuery($query);

		$current = $db->loadObject();

		if (!$user->get('guest') && $current)
		{
			$plugin        = \JPluginHelper::getPlugin('system', 'spamjail');
			$params        = new \JRegistry($plugin->params);
			$sessionCount  = $params->get('session_count', 5);
			$lifetimeCount = $params->get('user_count', 10);
			if (\JFactory::getSession()->get('spam_count', 0) > $sessionCount || $current->spam_count > $lifetimeCount)
			{
				return true;
			}
		}

		return false;
	}
}