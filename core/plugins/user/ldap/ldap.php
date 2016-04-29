<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * User plugin for hub users
 */
class plgUserLdap extends \Hubzero\Plugin\Plugin
{
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
		//Log::debug("plgUserLdap::onAfterStoreUser(" . $user['id'] . ")");
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
		//Log::debug("plgUserLdap::onAfterDeleteUser(" . $user['id'] . ")");
		\Hubzero\Utility\Ldap::syncUser($user['id']);
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param object holds the new profile data (\Hubzero\User\User)
	 */
	public function onAfterStoreProfile($user)
	{
		//Log::debug("plgUserLdap::onAfterStoreProfile(" . $user->get('uidNumber') . ")");
		\Hubzero\Utility\Ldap::syncUser($user->get('uidNumber'));
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param object holds the new profile data (\Hubzero\User\User)
	 */
	public function onAfterDeleteProfile($user)
	{
		//Log::debug("plgUserLdap::onAfterDeleteProfile(" . $user->get('uidNumber') . ")");
		\Hubzero\Utility\Ldap::syncUser($user->get('uidNumber'));
	}

	/**
	 * Method is called after password data is stored in the database
	 *
	 * @param object holds the new password data (\Hubzero\User\Password)
	 */
	public function onAfterStorePassword($user)
	{
		//Log::debug("plgUserLdap::onAfterStoreUser(" . $user->user_id . ")");
		\Hubzero\Utility\Ldap::syncUser($user->user_id);
	}

	/**
	 * Method is called after password data is deleted from the database
	 *
	 * @param object holds the new password data (\Hubzero\User\Password)
	 */
	public function onAfterDeletePassword($user)
	{
		//Log::debug("plgUserLdap::onAfterDeleteUser(" . $user->user_id . ")");
		\Hubzero\Utility\Ldap::syncUser($user->user_id);
	}

	/**
	 * Method is called after group data is stored in the database
	 *
	 * @param object holds the new group data (\Hubzero\User\Group)
	 */
	public function onAfterStoreGroup($group)
	{
		//Log::debug("plgUserLdap::onAfterStoreGroup(" . $group->cn . ")");
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}

	/**
	 * Method is called after group data is deleted from the database
	 *
	 * @param object holds the new group data (\Hubzero\User\Group)
	 */
	public function onAfterDeleteGroup($group)
	{
		//Log::debug("onAfterDeleteGroup($group)");
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}
}