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
	 * This method is an alias for onAfterStoreUser
	 *
	 * @param   array    $user     holds the new user data
	 * @param   boolean  $isnew    true if a new user is stored
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  void
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		return $this->onAfterStoreUser($user, $isnew, $success, $msg);
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param   array    $user     holds the new user data
	 * @param   boolean  $isnew    true if a new user is stored
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  void
	 */
	public function onAfterStoreUser($user, $isnew, $succes, $msg)
	{
		\Hubzero\Utility\Ldap::syncUser($user['id']);
	}

	/**
	 * This method is an alias for onAfterDeleteUser
	 *
	 * @param   array    $user     holds the user data
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  boolean  True on success
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		return $this->onAfterDeleteUser($user, $success, $msg);
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     holds the user data
	 * @param   boolean  $success  true if user was succesfully stored in the database
	 * @param   string   $msg      message
	 * @return  boolean  True on success
	 */
	public function onAfterDeleteUser($user, $succes, $msg)
	{
		\Hubzero\Utility\Ldap::syncUser($user['id']);

		return true;
	}

	/**
	 * Method is called after user data is stored in the database
	 *
	 * @param   object  $user  holds the new profile data (\Hubzero\User\User)
	 * @return  void
	 */
	public function onAfterStoreProfile($user)
	{
		\Hubzero\Utility\Ldap::syncUser($user->get('uidNumber'));
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param   object  $user  holds the new profile data (\Hubzero\User\User)
	 * @return  void
	 */
	public function onAfterDeleteProfile($user)
	{
		\Hubzero\Utility\Ldap::syncUser($user->get('uidNumber'));
	}

	/**
	 * Method is called after password data is stored in the database
	 *
	 * @param   object  $user  holds the new password data (\Hubzero\User\Password)
	 * @return  void
	 */
	public function onAfterStorePassword($user)
	{
		\Hubzero\Utility\Ldap::syncUser($user->user_id);
	}

	/**
	 * Method is called after password data is deleted from the database
	 *
	 * @param   object  $user  holds the new password data (\Hubzero\User\Password)
	 * @return  void
	 */
	public function onAfterDeletePassword($user)
	{
		\Hubzero\Utility\Ldap::syncUser($user->user_id);
	}

	/**
	 * Method is called after group data is stored in the database
	 *
	 * @param   object  $user  holds the new group data (\Hubzero\User\Group)
	 * @return  void
	 */
	public function onAfterStoreGroup($group)
	{
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}

	/**
	 * Method is called after group data is deleted from the database
	 *
	 * @param   object  $user  holds the new group data (\Hubzero\User\Group)
	 * @return  void
	 */
	public function onAfterDeleteGroup($group)
	{
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}
}
