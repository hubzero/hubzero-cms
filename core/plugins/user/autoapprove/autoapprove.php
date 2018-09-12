<?php
/**
 * HUBzero CMS
 *
 * Copyright 2011-2012 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Plugin for auto-approving users with specified email domains
 */
class plgUserAutoapprove extends \Hubzero\Plugin\Plugin
{
	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method sends a registration email to new users created in the backend.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isnew    True if a new user is stored.
	 * @param   boolean  $success  True if user was succesfully stored in the database.
	 * @param   string   $msg      Message.
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if ($isnew)
		{
			if (!isset($user['id']) || !$user['id'])
			{
				return;
			}

			$this->approveUser($user['id']);
		}
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array    $user     holds the user data
	 * @param   array    $options  array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onLoginUser($user, $options = array())
	{
		return $this->onUserLogin($user, $options);
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array    $user     holds the user data
	 * @param   array    $options  array holding options (remember, autoregister, group)
	 * @return  boolean  True on success
	 */
	public function onUserLogin($user, $options = array())
	{
		$userId = User::get('id');

		return $this->approveUser($userId);
	}

	/**
	 * Set user access groups based on profile choice
	 *
	 * @param   integer  $user
	 * @return  boolean  True on success
	 */
	public function approveUser($userId)
	{
		if (!$userId)
		{
			return false;
		}

		$pattern = $this->params->get('email_pattern');

		if (!$pattern)
		{
			return false;
		}

		$user = User::getInstance($userId);

		if (!$user || !$user->get('email'))
		{
			return false;
		}

		if ($user->get('approved'))
		{
			return true;
		}

		if (preg_match("/$pattern/", $user->get('email')))
		{
			$query = $user->getQuery()
				->update($user->getTableName())
				->set(['approved' => 1])
				->whereEquals('id', $user->get('id'))
				->toString();

			$db = App::get('db');
			$db->setQuery($query);

			if (!$db->query())
			{
				return false;
			}

			User::set('approved', 1);
		}

		return true;
	}
}
