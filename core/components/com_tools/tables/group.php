<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Tools\Tables;

use Lang;
use User;

/**
 * Table class for tool/group mapping
 */
class Group extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_groups', 'cn', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (!$this->cn)
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_GROUP_NO_CN'));
			return false;
		}

		if (!$this->toolid)
		{
			$this->setError(Lang::txt('CONTRIBTOOL_ERROR_GROUP_NO_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'save'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $cn Parameter description (if any) ...
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      string $role Parameter description (if any) ...
	 * @return     void
	 */
	public function save($cn, $toolid = '', $role = '')
	{
		$query = "INSERT INTO $this->_tbl (cn, toolid, role) VALUES (" . $this->_db->Quote($cn) . "," . $this->_db->Quote($toolid) . "," . $this->_db->Quote($role) . ")";
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Save a group
	 *
	 * @param      string  $toolid   Tool ID
	 * @param      string  $devgroup Group name
	 * @param      array   $members  List of members
	 * @param      boolean $exist    Group exists?
	 * @return     boolean True if no errors
	 */
	public function saveGroup($toolid=NULL, $devgroup, $members, $exist)
	{
		if (!$toolid or !$devgroup)
		{
			return false;
		}

		$members = \Components\Tools\Helpers\Utils::transform($members, 'uidNumber');
		$group = new \Hubzero\User\Group();

		if (\Hubzero\User\Group::exists($devgroup))
		{
			$group->read($devgroup);
			$existing_members = \Components\Tools\Helpers\Utils::transform(Tool::getToolDevelopers($toolid), 'uidNumber');
			$group->set('members', $existing_members);
			$group->set('managers', $existing_managers);
		}
		else
		{
			$group->create();
			$group->set('type', 2);
			$group->set('published', 1);
			$group->set('discoverability', 0);
			$group->set('description', 'Dev group for tool ' . $toolid);
			$group->set('cn', $devgroup);
			$group->set('members', $existing_members);
			$group->set('managers', $existing_managers);
		}

		$group->update();

		if (!$exist)
		{
			$this->save($devgroup, $toolid, '1');
		}

		return true;
	}

	/**
	 * Short description for 'saveMemberGroups'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $toolid Parameter description (if any) ...
	 * @param      array $newgroups Parameter description (if any) ...
	 * @param      string $editversion Parameter description (if any) ...
	 * @param      array $membergroups Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function saveMemberGroups($toolid=NULL, $newgroups, $editversion='dev', $membergroups=array())
	{
		if (!$toolid)
		{
			return false;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'models' . DS . 'tool.php');

		$membergroups = \Components\Tools\Models\Tool::getToolGroups($toolid);
		$membergroups = \Components\Tools\Helpers\Utils::transform($membergroups, 'cn');
		$newgroups = \Components\Tools\Helpers\Utils::transform($newgroups, 'cn');
		$to_delete = array_diff($membergroups, $newgroups);

		if (count($to_delete) > 0 && $editversion != 'current')
		{
			foreach ($to_delete as $del)
			{
				$query = "DELETE FROM $this->_tbl WHERE cn=" . $this->_db->Quote($del) . " AND toolid=" . $this->_db->Quote($toolid) . " AND role=0";
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}

		if (count($newgroups) > 0)
		{
			foreach ($newgroups as $newgroup)
			{
				if (\Hubzero\User\Group::exists($newgroup) && !in_array($newgroup, $membergroups))
				{
					// create an entry in tool_groups table
					$this->save($newgroup, $toolid, '0');
				}
			}
		}

		return true;
	}

	/**
	 * Write the list of group members
	 *
	 * @param      array   $new      New members
	 * @param      unknown $id       Parameter description (if any) ...
	 * @param      object  $database JDatabase
	 * @param      string  &$err     Error message
	 * @return     array
	 */
	public function writeMemberGroups($new, $id, $database, &$err='')
	{
		$toolhelper = new \Components\Tools\Helpers\Utils();

		$groups    = is_array($new) ? $new : $toolhelper->makeArray($new);
		$grouplist = array();
		$invalid   = '';
		$i = 0;

		if (count($groups) > 0)
		{
			foreach ($groups as $group)
			{
				if (\Hubzero\User\Group::exists($group))
				{
					if ($id)
					{
						$grouplist[$i]->cn = $group;
					}
					else
					{
						$grouplist[$i] = $group;
					}
					$i++;
				}
				else
				{
					$err = Lang::txt('CONTRIBTOOL_ERROR_GROUP_DOES_NOT_EXIST');
					$invalid .= ' ' . $group . ';';
				}
			}
		}
		if ($err)
		{
			$err.= $invalid;
		}

		return $grouplist;
	}

	/**
	 * Get a list of team members
	 *
	 * @param      array   $new      Parameter description (if any) ...
	 * @param      unknown $id       Parameter description (if any) ...
	 * @param      object  $database JDatabase
	 * @param      string  &$err     Error message
	 * @return     array
	 */
	public function writeTeam($new, $id, $database, &$err='')
	{
		$toolhelper = new \Components\Tools\Helpers\Utils();

		$members  = is_array($new) ? $new : $toolhelper->makeArray($new);
		$teamlist = array();
		$invalid  = '';
		$i = 0;

		if (count($members) > 0)
		{
			foreach ($members as $member)
			{
				$user = User::getInstance($member);
				if (is_object($user))
				{
					if ($id)
					{
						$teamlist[$i]->uidNumber = $user->get('id');
					}
					else
					{
						$teamlist[$i] = $user->get('id');
					}
					$i++;
				}
				else
				{
					$err = Lang::txt('CONTRIBTOOL_ERROR_LOGIN_DOES_NOT_EXIST');
					$invalid .= ' ' . $member . ';';
				}
			 }
		}
		if ($err)
		{
			$err .= $invalid;
		}

		return $teamlist;
	}
}
