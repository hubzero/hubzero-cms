<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

use Hubzero\Base\Obj;
use User;
use App;

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
		if ($assetId)
		{
			$assetName .= '.' . $assetType;
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
		$result = new Obj;

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
		$db = App::get('db');
		$sql = "SELECT r.id, r.name, r.permissions FROM `#__xgroups_roles` as r, `#__xgroups_member_roles` as m WHERE r.id=m.roleid AND m.uidNumber=" . $db->quote($uid) . " AND r.gidNumber=" . $db->quote($gid);
		$db->setQuery($sql);

		return $db->loadAssocList();
	}

	/**
	 * Check to see if user has permission to perform task
	 *
	 * @param   object   $group   Hubzero\User\Group
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
