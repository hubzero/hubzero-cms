<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for automatically adding users to the d1_nation group based on IP country group lookup
 */
class plgUserD1 extends \Hubzero\Plugin\Plugin
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
		$approved = \Hubzero\User\Group::getInstance('d1_override');

		if (is_object($approved) && $approved->isMember(User::get('id')))
		{
			Log::debug('plgUserD1: [' . User::get('username') . '] is in d1_override, removing from d1_nation and skipping geo check.');

			$nation = \Hubzero\User\Group::getInstance('d1_nation');

			if (is_object($nation))
			{
				$nation->remove('members', array(User::get('id')));
				$nation->update();
			}

			return;
		}

		$ip = $_SERVER['REMOTE_ADDR'];

		$gdb = \Hubzero\Geocode\Geocode::getGeoDBO();

		if (!$gdb)
		{
			Log::debug('plgUserD1: geo database unavailable, skipping group update for [' . User::get('username') . '].');
			return;
		}

		$gdb->setQuery(
			"SELECT cg.countrygroup FROM ipcountry ic" .
			" JOIN countrygroup cg ON ic.countrySHORT = cg.countrycode" .
			" WHERE ic.ipfrom <= INET_ATON(" . $gdb->quote($ip) . ")" .
			" AND ic.ipto >= INET_ATON(" . $gdb->quote($ip) . ")"
		);
		$countrygroup = $gdb->loadResult();

		if (!$countrygroup)
		{
			return;
		}

		if ($countrygroup == 'D1')
		{
			Log::debug($ip . ' is in a D1 nation, adding [' . User::get('username') . '] to group [d1_nation].');

			$group = \Hubzero\User\Group::getInstance('d1_nation');

			if (is_object($group))
			{
				$group->add('members', array(User::get('id')));
				$group->update();
			}
			else
			{
				Log::debug('group [d1_nation] does not exist, member addition failed.');
			}
		}
		else
		{
			Log::debug($ip . ' has countrygroup [' . $countrygroup . '], leaving [' . User::get('username') . '] membership to group [d1_nation] unchanged.');
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
		$group = \Hubzero\User\Group::getInstance('d1_nation');

		if (is_object($group))
		{
			$group->remove('members', array($user['id']));
			$group->update();
		}
	}
}
