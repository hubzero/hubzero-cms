<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for automatically adding users to a specified group based on geolocation
 */
class plgUserGeo extends \Hubzero\Plugin\Plugin
{
	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param array $user    holds the user data
	 * @param array $options holding options (remember, autoregister, group)
	 * @return bool
	 */
	public function onUserLogin($user, $options = array())
	{
		// Get params
		$groupAlias = $this->params->get('group', false);
		$location   = $this->params->get('location', false);

		// Make sure params were set
		if (!$groupAlias || !$location)
		{
			return;
		}

		// Check user location
		if (\Hubzero\Geocode\Geocode::is_iplocation($_SERVER['REMOTE_ADDR'], $location))
		{
			// Get user groups and instances of access groups
			$group = \Hubzero\User\Group::getInstance($groupAlias);

			// Update group if that group exists
			if (is_object($group))
			{
				$group->add('members', array(\User::getInstance($user['username'])->get('id')));
				$group->update();
			}
		}
	}

	/**
	 * Method is called after user data is deleted from the database
	 *
	 * @param array  $user   holds the user data
	 * @param bool   $succes true if user was succesfully stored in the database
	 * @param string $msg    message
	 */
	public function onUserAfterDelete($user, $succes, $msg)
	{
		// Check params for group name
		$groupAlias = $this->params->get('group', false);

		if ($groupAlias)
		{
			// Get the group
			$group = \Hubzero\User\Group::getInstance($groupAlias);

			if (is_object($group))
			{
				// Remove the user from the group
				$group->remove('members', array($user['id']));

				// Update the groups
				$group->update();
			}
		}
	}
}
