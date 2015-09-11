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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2021 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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