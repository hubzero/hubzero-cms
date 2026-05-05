<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for automatically adding users to the location_us group based on IP country lookup
 */
class plgUserUs extends \Hubzero\Plugin\Plugin
{
	public function onLoginUser($user, $options = array())
	{
		return $this->onUserLogin($user, $options);
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     holds the user data
	 * @param   array  $options  holding options (remember, autoregister, group)
	 * @return  bool
	 */
	public function onUserLogin($user, $options = array())
	{
		$ip = $_SERVER['REMOTE_ADDR'];

		$d1 = \Hubzero\User\Group::getInstance('d1_nation');

		if (is_object($d1) && in_array(User::get('id'), $d1->get('members')))
		{
			Log::debug('[' . User::get('username') . '] is a member of [d1_nation], removing from group [location_us].');

			$group = \Hubzero\User\Group::getInstance('location_us');

			if (is_object($group))
			{
				$group->remove('members', array(User::get('id')));
				$group->update();
			}

			return;
		}

		$gdb = \Hubzero\Geocode\Geocode::getGeoDBO();

		if (!$gdb)
		{
			Log::debug('plgUserUs: geo database unavailable, skipping group update for [' . User::get('username') . '].');
			return;
		}

		$gdb->setQuery(
			"SELECT countrySHORT FROM ipcountry" .
			" WHERE ipfrom <= INET_ATON(" . $gdb->quote($ip) . ")" .
			" AND ipto >= INET_ATON(" . $gdb->quote($ip) . ")"
		);
		$country = $gdb->loadResult();

		if ($country == 'US')
		{
			Log::debug($ip . ' is a US address, adding [' . User::get('username') . '] to group [location_us].');

			$group = \Hubzero\User\Group::getInstance('location_us');

			if (is_object($group))
			{
				$group->add('members', array(User::get('id')));
				$group->update();
			}
			else
			{
				Log::debug('group [location_us] does not exist, member addition failed.');
			}
		}
		else
		{
			Log::debug($ip . ' is not a US address, removing [' . User::get('username') . '] from group [location_us].');

			$group = \Hubzero\User\Group::getInstance('location_us');

			if (is_object($group))
			{
				$group->remove('members', array(User::get('id')));
				$group->update();
			}
		}
	}

	public function onAfterDeleteUser($user, $success, $msg)
	{
		return $this->onUserAfterDelete($user, $success, $msg);
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array   $user     holds the user data
	 * @param   bool    $success  true if user was successfully stored in the database
	 * @param   string  $msg      message
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		$group = \Hubzero\User\Group::getInstance('location_us');

		if (is_object($group))
		{
			$group->remove('members', array($user['id']));
			$group->update();
		}
	}
}
