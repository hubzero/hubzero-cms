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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubzero class for user information
 */
class Hubzero_User
{
	/**
	 * Container for Hubzero_User_Profile object
	 * 
	 * @var object
	 */
	private $_profile = null;

	/**
	 * Container for JUser object
	 * 
	 * @var object
	 */
	private $_user = null;

	/**
	 * Constructor
	 * Loads a user, defaults to current user
	 * 
	 * @param      mixed $user Username or ID to load
	 * @return     void
	 */
	public function __construct($user = null)
	{
		if (!is_null($user))
		{
			$this->_user = JUser::getInstance($user);
		}
	}

	/**
	 * Load a user
	 * 
	 * @param      mixed $user Username or ID to load
	 * @return     object Hubzero_User
	 */
	public function getInstance($user)
	{
		$instance = new Hubzero_User($user);

		if ($instance->_user == null)
		{
			return null;
		}

		return $instance;
	}

	/**
	 * Load a user's profile
	 * 
	 * @return     void
	 */
	private function _load_profile()
	{
		$this->_profile = Hubzero_User_Profile::getInstance($this->_user->get('username'));
	}

	/**
	 * Compare a password against the profile password
	 * 
	 * @param      string $password Password to compare
	 * @return     boolean True if passwords are the same, false if not
	 */
	public function comparePassword($password) // @TODO: PASSWORD, deprecated?
	{
		if (is_null($this->_profile))
		{
			$this->_load_profile();
		}

		if (is_null($this->_profile))
		{
			return false;
		}

		$password = Hubzero_User_Helper::encrypt_password($password);

		if (empty($password))
		{
			return false;
		}

		if ($password === $this->_profile->get('userPassword'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the user's ID
	 * 
	 * @return     integer
	 */
	public function getUserId()
	{
		return $this->_user->get('id');
	}
}
