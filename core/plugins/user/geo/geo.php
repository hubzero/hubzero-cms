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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2021 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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