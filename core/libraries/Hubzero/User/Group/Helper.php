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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User\Group;

use Hubzero\User\Group;

/**
 * Misc. group helper methods
 */
class Helper
{
	/**
	 * Pad an ID with prepended zeros
	 *
	 * @param   integer  $group_id
	 * @return  integer
	 */
	public static function niceidformat($group_id)
	{
		while (strlen($group_id) < 5)
		{
			$group_id = 0 . $group_id;
		}
		return $group_id;
	}

	/**
	 * Get popular groups
	 *
	 * @param   integer  $limit
	 * @return  array
	 */
	public static function getPopularGroups($limit=0)
	{
		//database object
		$database = \App::get('db');

		//query
		$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc,
				(SELECT COUNT(*) FROM #__xgroups_members AS gm WHERE gm.gidNumber=g.gidNumber) AS members
				FROM #__xgroups AS g
				WHERE g.type=1
				AND g.published=1
				AND g.approved=1
				AND g.discoverability=0
				ORDER BY members DESC";

		//do we want to limit return
		if ($limit > 0)
		{
			$sql .= "  LIMIT {$limit}";
		}

		//execute query and return result
		$database->setQuery( $sql );
		if (!$database->getError())
		{
			return $database->loadObjectList();
		}
	}

	/**
	 * Gets featured groups
	 *
	 * @param   string  $groupList
	 * @return  array
	 */
	public static function getFeaturedGroups($groupList)
	{
		//database object
		$database = \App::get('db');

		//parse the group list
		$groupList = array_map('trim', array_filter(explode(',', $groupList), 'trim'));

		//make sure we have a list of groups
		if (count($groupList) < 1)
		{
			return array();
		}

		//query to get groups
		$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc
				FROM jos_xgroups AS g
				WHERE g.type=1
				AND g.published=1
				AND g.approved=1
				AND g.discoverability=0
				AND g.cn IN ('".implode("','", $groupList)."')";

		$database->setQuery( $sql );
		if (!$database->getError())
		{
			return $database->loadObjectList();
		}
	}

	/**
	 * Gets groups matching tag string
	 *
	 * @param   string  $usertags
	 * @param   string  $usergroups
	 * @return  string
	 */
	public static function getGroupsMatchingTagString($usertags, $usergroups)
	{
		//database object
		$database = \App::get('db');

		//turn users tag string into array
		$mytags = ($usertags != '') ? array_map('trim', explode(',', $usertags)) : array();

		//users groups
		$mygroups = array();
		if (is_array($usergroups))
		{
			foreach ($usergroups as $ug)
			{
				$mygroups[] = $ug->gidNumber;
			}
		}

		//query the databse for all published, type "HUB" groups
		$sql = "SELECT g.gidNumber, g.cn, g.description, g.public_desc
				FROM `#__xgroups` AS g
				WHERE g.type=1
				AND g.published=1
				AND g.discoverability=0";
		$database->setQuery($sql);

		//get all groups
		$groups = $database->loadObjectList();

		//loop through each group and see if there is a tag match
		foreach ($groups as $k => $group)
		{
			//get the groups tags
			$gt = new \Components\Groups\Models\Tags($group->gidNumber);

			$group->tags = $gt->render('string');
			$group->tags = array_map('trim', explode(',', $group->tags));

			//get common tags
			$group->matches = array_intersect($mytags, $group->tags);

			//remove tags from the group object since its no longer needed
			unset($group->tags);

			//if we dont have a match remove group from return results
			//or if we are already a member of the group remove from return results
			if (count($group->matches) < 1 || in_array($group->gidNumber, $mygroups))
			{
				unset($groups[$k]);
			}
		}

		return $groups;
	}

	/**
	 * Converts invite emails to true group
	 *
	 * @param   string   $email
	 * @param   integer  $user_id
	 * @return  void
	 */
	public function convertInviteEmails($email, $user_id)
	{
		// @FIXME: Should wrap this up in a nice transaction to handle partial failures and
		// race conditions.

		if (empty($email) || empty($user_id))
		{
			return false;
		}

		$db = \App::get('db');

		$sql = 'SELECT gidNumber FROM `#__xgroups_inviteemails` WHERE email=' . $db->quote($email) . ';';

		$db->setQuery($sql);

		$result = $db->loadColumn();

		if ($result === false)
		{
			return false;
		}

		if (empty($result))
		{
			return true;
		}

		foreach ($result as $r)
		{
			$values .= "($r,$user_id),";
			$gids   .= "$r,";
		}

		$values = rtrim($values,',');
		$gids = rtrim($gids,',');

		$sql = 'INSERT INTO `#__xgroups_invitees` (gidNumber,uidNumber) VALUES ' . $values . ';';

		$db->setQuery($sql);

		$result = $db->query();

		if (!$result)
		{
			return false;
		}

		$sql = 'DELETE FROM `#__xgroups_inviteemails` WHERE email=' . $db->quote($email) . ' AND gidNumber IN (' . $gids . ');';

		$db->setQuery($sql);

		$result = $db->query();

		if (!$result)
		{
			return false;
		}

		return true;
	}

	/**
	 * Search group roles
	 *
	 * @param   object  $group
	 * @param   string  $role
	 * @return  array
	 */
	public static function search_roles($group, $role = '')
	{
		if ($role == '')
			return false;

		$db =  \App::get('db');

		$query = "SELECT uidNumber FROM #__xgroups_roles as r, #__xgroups_member_roles as m WHERE r.id='" . $role . "' AND r.id=m.roleid AND r.gidNumber='" . $group->gidNumber . "'";

		$db->setQuery($query);

		$result = $db->loadColumn();

		$result = array_intersect($result, $group->members);

		if (count($result) > 0)
		{
			return $result;
		}
	}

	/**
	 * Get the access level for a group plugin
	 *
	 * @param   object $group
	 * @param   string $get_plugin
	 * @return  mixed
	 */
	public static function getPluginAccess($group, $get_plugin = '')
	{
		// make sure we have a Hubzero group
		if (!($group instanceof Group))
		{
			return;
		}

		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_group_plugins = \Event::trigger('groups.onGroupAreas', array());
		array_unshift($hub_group_plugins, array('name'=>'overview', 'title'=>'Overview', 'default_access'=>'anyone'));

		//array to store plugin preferences when after retrieved from db
		$active_group_plugins = array();

		//get the group plugin preferences
		//returns array of tabs and their access level (ex. [overview] => 'anyone', [messages] => 'registered')
		$group_plugins = $group->get('plugins');

		if ($group_plugins)
		{
			$group_plugins = explode(',', $group_plugins);

			foreach ($group_plugins as $plugin)
			{
				$temp = explode('=', trim($plugin));

				if ($temp[0])
				{
					$active_group_plugins[$temp[0]] = trim($temp[1]);
				}
			}
		}

		//array to store final group plugin preferences
		//array of acceptable access levels
		$group_plugin_access = array();
		$acceptable_levels = array('nobody', 'anyone', 'registered', 'members');

		//if we have already set some
		if ($active_group_plugins)
		{
			//for each plugin that is active on the hub
			foreach ($hub_group_plugins as $hgp)
			{
				//if group defined access level is not an acceptable value or not set use default value that is set per plugin
				//else use group defined access level
				if (!isset($active_group_plugins[$hgp['name']]) || !in_array($active_group_plugins[$hgp['name']], $acceptable_levels))
				{
					$value = $hgp['default_access'];
				}
				else
				{
					$value = $active_group_plugins[$hgp['name']];
				}

				//store final  access level in array of access levels
				$group_plugin_access[$hgp['name']] = $value;
			}
		}
		else
		{
			//for each plugin that is active on the hub
			foreach ($hub_group_plugins as $hgp)
			{
				$value = $hgp['default_access'];

				//store final  access level in array of access levels
				$group_plugin_access[$hgp['name']] = $value;
			}
		}

		//if we wanted to return only a specific level return that otherwise return all access levels
		if ($get_plugin != '')
		{
			return $group_plugin_access[$get_plugin];
		}
		else
		{
			return $group_plugin_access;
		}
	}

	/**
	 * Get Instance of Super Group Database
	 *
	 * Always returns the same instance whenever this method is called
	 *
	 * @param   array   $config  Array of database options
	 * @param   string  $cname
	 * @return  object  Database Object
	 */
	public static function getDbo($config = array(), $cname = '')
	{
		// empty instance of db
		$db = \JDatabase::getInstance();

		// make sure we have a group object
		if (!$group = Group::getInstance(\Request::getVar('cn', $cname)))
		{
			return $db;
		}

		// make sure we are a super group
		if (!$group->isSuperGroup())
		{
			return $db;
		}

		// load super group db config if not passed in
		if (empty($config))
		{
			// build path to config file
			$uploadPath = \Component::params( 'com_groups' )->get('uploadpath');
			$configPath = PATH_APP . DS . trim($uploadPath, DS) . DS . $group->get('gidNumber') . DS . 'config' . DS . 'db.php';

			// make sure file exists
			if (!file_exists($configPath))
			{
				return $db;
			}

			// include config
			$config = include $configPath;
		}

		// return instance of db
		return \JDatabase::getInstance($config);
	}
}