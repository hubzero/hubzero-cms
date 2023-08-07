<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;
use Lang;
use User;

/**
 * Table class for tool/group mapping
 */
class Group extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__tool_groups', 'cn', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
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
	 * Save a record
	 *
	 * @param   string  $cn
	 * @param   string  $toolid
	 * @param   string  $role
	 * @return  void
	 */
	public function save($cn, $toolid = '', $role = '')
	{
		$query = "INSERT INTO $this->_tbl (cn, toolid, role) VALUES (" . $this->_db->quote($cn) . "," . $this->_db->quote($toolid) . "," . $this->_db->quote($role) . ")";
		$this->_db->setQuery($query);
		$this->_db->query();
	}

	/**
	 * Save a group
	 *
	 * @param   string   $toolid    Tool ID
	 * @param   string   $devgroup  Group name
	 * @param   array    $members   List of members
	 * @param   boolean  $exist     Group exists?
	 * @return  boolean  True if no errors
	 */
	public function saveGroup($toolid, $devgroup, $members, $exist)
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
	 * Save member groups
	 *
	 * @param   string  $toolid
	 * @param   array   $newgroups
	 * @param   string  $editversion
	 * @param   array   $membergroups
	 * @return  boolean
	 */
	public function saveMemberGroups($toolid, $newgroups, $editversion='dev', $membergroups=array())
	{
		if (!$toolid)
		{
			return false;
		}

		require_once dirname(__DIR__) . DS . 'models' . DS . 'tool.php';

		$membergroups = \Components\Tools\Models\Tool::getToolGroups($toolid);
		$membergroups = \Components\Tools\Helpers\Utils::transform($membergroups, 'cn');
		$newgroups = \Components\Tools\Helpers\Utils::transform($newgroups, 'cn');
		$to_delete = array_diff($membergroups, $newgroups);

		if (count($to_delete) > 0 && $editversion != 'current')
		{
			foreach ($to_delete as $del)
			{
				$query = "DELETE FROM $this->_tbl WHERE cn=" . $this->_db->quote($del) . " AND toolid=" . $this->_db->quote($toolid) . " AND role=0";
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
	 * @param   array    $new       New members
	 * @param   integer  $id
	 * @param   object   $database  Database
	 * @param   string   &$err      Error message
	 * @return  array
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
	 * @param   array    $new
	 * @param   integer  $id
	 * @param   object   $database  Database
	 * @param   string   &$err      Error message
	 * @return  array
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
