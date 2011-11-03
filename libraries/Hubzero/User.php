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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_User'
 * 
 * Long description (if any) ...
 */
class Hubzero_User
{

	/**
	 * Description for '_profile'
	 * 
	 * @var object
	 */
	private $_profile = null;

	/**
	 * Description for '_user'
	 * 
	 * @var object
	 */
	private $_user = null;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @return     void
	 */
	function __construct($user = null)
	{
		if (!is_null($user))
		{
			$this->_user = JUser::getInstance($user);
		}
	}

	/**
	 * Short description for 'getInstance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $user Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	function getInstance($user)
	{
		$instance = new Hubzero_User($user);

		if ($instance->_user == null)
			return null;

		return $instance;

	}

	/**
	 * Short description for '_load_profile'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function _load_profile()
	{
		$this->_profile = Hubzero_User_Profile::getInstance($this->_user->get('username'));
	}

	/**
	 * Short description for 'comparePassword'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $password Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function comparePassword($password)
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
	 * Short description for 'getUserId'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	function getUserId()
	{
		return $this->_user->get('id');
	}

}
