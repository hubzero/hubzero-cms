<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 */

namespace Components\Groups\Helpers;

use Hubzero\Base\Object;
use User;

/**
 * Permissions helper
 */
class Permissions
{
	/**
	 * Name of the component
	 *
	 * @var  string
	 */
	public static $extension = 'com_groups';

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $assetType
	 * @param   integer  $assetId
	 * @return  object
	 */
	public static function getActions($assetType='component', $assetId = 0)
	{
		$assetName  = self::$extension;
		$assetName .= '.' . $assetType;
		if ($assetId)
		{
			$assetName .= '.' . (int) $assetId;
		}

		$actions = array(
			'core.admin',
			'core.manage',
			'core.create',
			'core.edit',
			'core.edit.state',
			'core.delete'
		);

		$user = User::getInstance();
		$result = new Object;

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	/**
	 * Get group roles for a specific member/group pair
	 *
	 * @param   string  $uid  User ID
	 * @param   string  $gid  Group ID
	 * @return  array
	 */
	public static function getGroupMemberRoles($uid, $gid)
	{
		$db = \App::get('db');
		$sql = "SELECT r.id, r.name, r.permissions FROM `#__xgroups_roles` as r, `#__xgroups_member_roles` as m WHERE r.id=m.roleid AND m.uidNumber=" . $db->quote($uid) . " AND r.gidNumber=" . $db->quote($gid);
		$db->setQuery($sql);

		return $db->loadAssocList();
	}

	/**
	 * Check to see if user has permission to perform task
	 *
	 * @param   object   $group   \Hubzero\User\Group
	 * @param   string   $action  Group Action to perform
	 * @return  boolean
	 */
	public static function userHasPermissionForGroupAction($group, $action)
	{
		// Get user roles
		$roles = self::getGroupMemberRoles(
			User::get('id'),
			$group->get('gidNumber')
		);

		// Check to see if any of our roles for user has permission for action
		foreach ($roles as $role)
		{
			$permissions = json_decode($role['permissions']);
			$permissions = (is_object($permissions)) ? $permissions : new \stdClass;
			if (property_exists($permissions, $action) && $permissions->$action == 1)
			{
				return true;
			}
		}
		return false;
	}
}

