<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Table class for logging group actions
 */
class GroupsMembersRole extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__xgroups_roles', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		// make sure we have group id
		if (trim($this->gidNumber) == '')
		{
			$this->setError(Lang::txt('PLG_GROUPS_MEMBERS_ROLE_MUST_HAVE_GROUP_ID'));
		}

		// make sure we ahve role name
		if (trim($this->name) == '')
		{
			$this->setError(Lang::txt('PLG_GROUPS_MEMBERS_ROLE_MUST_HAVE_ROLE_NAME'));
		}

		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Get Role Permisisons
	 *
	 * @return  string
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * Check to see if role has permission
	 * 
	 * @param   string   $permission  Permission name
	 * @return  boolean
	 */
	public function hasPermission($permission = '')
	{
		// get role permissions & json decode
		$permissions = json_decode($this->getPermissions());

		if (isset($permissions->$permission))
		{
			return $permissions->$permission;
		}
		return null;
	}

	/**
	 * Delete Roles for user
	 * 
	 * @param   integer  $userId
	 * @return  boolean
	 */
	public static function deleteRolesForUserWithId($userId)
	{
		$database = App::get('db');
		$sql = "DELETE FROM `#__xgroups_member_roles` WHERE uidNumber=" . $database->Quote($userId);
		$database->setQuery($sql);
		if (!$database->query())
		{
			return false;
		}
		return true;
	}
}