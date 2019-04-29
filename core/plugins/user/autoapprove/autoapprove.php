<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if (!isset($user['id']) || !$user['id'])
		{
			return;
		}

		$this->approveUser($user);
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
		$userArr = User::getInstance()->toArray();

		return $this->approveUser($userArr);
	}

	/**
	 * Set user access groups based on profile choice
	 *
	 * @param   array    $user
	 * @return  boolean  True on success
	 */
	public function approveUser($user)
	{
		$pattern = $this->params->get('email_pattern');

		if (!$pattern)
		{
			return true;
		}

		if (!$user || !$user['email'])
		{
			return true;
		}

		if (isset($user['approved']) && $user['approved'])
		{
			return true;
		}

		if (preg_match("/$pattern/", $user['email']))
		{
			$usr = User::getInstance($user['id']);

			$query = $usr->getQuery()
				->update($usr->getTableName())
				->set(['approved' => 1])
				->whereEquals('id', $user['id'])
				->toString();

			$db = App::get('db');
			$db->setQuery($query);

			if ($db->query())
			{
				User::set('approved', 1);
			}
		}

		return true;
	}
}
