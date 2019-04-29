<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public function onAfterStoreUser($user, $isnew, $success, $msg)
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
	public function onAfterDeleteUser($user, $success, $msg)
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
	 * @param   object  $group  holds the new group data (\Hubzero\User\Group)
	 * @return  void
	 */
	public function onAfterStoreGroup($group)
	{
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}

	/**
	 * Method is called after group data is deleted from the database
	 *
	 * @param   object  $group  holds the new group data (\Hubzero\User\Group)
	 * @return  void
	 */
	public function onAfterDeleteGroup($group)
	{
		\Hubzero\Utility\Ldap::syncGroup($group->cn);
	}
}
