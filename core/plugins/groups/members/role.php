<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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