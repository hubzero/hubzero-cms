<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.event.plugin');

/**
 * User plugin for hub users
 */
class plgUserLdap extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param array holds the new user data
	 * @param boolean true if a new user is stored
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterStoreUser(" . $user['id'] . ")");
		\Hubzero\Utility\Ldap::syncUser($user['id']);
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param array holds the user data
	 * @param boolean true if user was succesfully stored in the database
	 * @param string message
	 */
	public function onAfterDeleteUser($user, $succes, $msg)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterDeleteUser(" . $user['id'] . ")");
		\Hubzero\Utility\Ldap::syncUser($user['id']);
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param object holds the new profile data (\Hubzero\User\Profile)
	 */
	public function onAfterStoreProfile($user)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterStoreProfile(" . $user->get('uidNumber') . ")");
		\Hubzero\Utility\Ldap::syncUser($user->get('uidNumber'));
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param object holds the new profile data (\Hubzero\User\Profile)
	 */
	public function onAfterDeleteProfile($user)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterDeleteProfile(" . $user->get('uidNumber') . ")");
		\Hubzero\Utility\Ldap::syncUser($user->get('uidNumber'));
	}

	/**
	 * Method is called after password data is stored in the database
	 *
	 * @param object holds the new password data (\Hubzero\User\Password)
	 */
	public function onAfterStorePassword($user)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterStoreUser(" . $user->user_id . ")");
		\Hubzero\Utility\Ldap::syncUser($user->user_id);
	}

	/**
	 * Method is called after password data is deleted from the database
	 *
	 * @param object holds the new password data (\Hubzero\User\Password)
	 */
	public function onAfterDeletePassword($user)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterDeleteUser(" . $user->user_id . ")");
		\Hubzero\Utility\Ldap::syncUser($user->user_id);
	}

	/**
	 * Method is called after group data is stored in the database
	 *
	 * @param object holds the new group data (\Hubzero\User\Group)
	 */
	public function onAfterStoreGroup($group)
	{
		//JFactory::getLogger()->debug("plgUserLdap::onAfterStoreGroup(" . $group->cn . ")");
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}

	/**
	 * Method is called after group data is deleted from the database
	 *
	 * @param object holds the new group data (\Hubzero\User\Group)
	 */
	public function onAfterDeleteGroup($group)
	{
		//JFactory::getLogger()->debug("onAfterDeleteGroup($group)");
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}
}