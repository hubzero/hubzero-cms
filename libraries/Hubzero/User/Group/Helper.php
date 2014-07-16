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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\User\Group;

use Hubzero\User\Group;

/**
 * Misc. group helper methods
 */
class Helper
{
	/**
	 * Short description for 'niceidformat'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
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
	 * @return    array
	 */
	public static function getPopularGroups($limit=0)
	{
		//database object
		$database = \JFactory::getDBO();

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
	 * @return    array
	 */
	public static function getFeaturedGroups( $groupList )
	{
		//database object
		$database = \JFactory::getDBO();

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
	 * @return    string
	 */
	public static function getGroupsMatchingTagString($usertags, $usergroups)
	{
		//database object
		$database = \JFactory::getDBO();

		$gt = new \GroupsTags($database);

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
				FROM #__xgroups AS g
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
			$group->tags = $gt->get_tag_string($group->gidNumber);
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
	 * List groups in common format
	 *
	 * @return    string
	 */
	public static function listGroups($name='', $config, $groups=array(), $num_columns=2, $display_logos=true, $display_private_description=false, $description_char_limit=150)
	{
		//user object
		$user = \JFactory::getUser();

		//database object
		$database = \JFactory::getDBO();

		//check to see if we have any groups to show
		if (!$groups)
		{
			return '<p class="info">' . \JText::sprintf('COM_GROUPS_INTRO_NO_' . str_replace(' ', '_', strtoupper($name)), $user->get('id')) . '</p>';
		}

		//var to hold html
		$html = '';

		//var to hold count
		$count = 0;
		$totalCount = 0;

		//loop through each group
		foreach ($groups as $group)
		{
			//get the Hubzero Group Object
			$hg = Group::getInstance($group->gidNumber);

			$gt = new \GroupsTags($database);

			//var to hold group description
			$description = "";

			//get the column were on
			switch ($count)
			{
				case 0: $cls = "first";  break;
				case 1: $cls = "second"; break;
				case 2: $cls = "third";  break;
				case 3: $cls = "fourth"; break;
			}

			//how many columns are we showing
			switch ($num_columns)
			{
				case 2: $columns = "two";   break;
				case 3: $columns = "three"; break;
				case 4: $columns = "four";  break;
			}

			//if we want to display private description and if we have a private description
			if ($display_private_description && $hg->private_desc)
			{
				$description = $hg->getDescription('parsed', 0, 'private');
			}
			elseif ($hg->public_desc)
			{
				$description = $hg->getDescription('parsed', 0, 'public');
			}

			//are we a group manager
			$isManager = (in_array($user->get("id"), $hg->get('managers'))) ? true : false;

			//are we publised
			$isPublished = ($hg->get('published')) ? true : false;

			//if we have a description then strip tags, remove links, and shorten
			if ($description != '')
			{
				$description = strip_tags($description);
				$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
				$description = preg_replace("/$UrlPtrn/", '', $description);
			}
			else
			{
				$description = '<em>No group description available.</em>';
			}

			//shorten description
			$gdescription = substr($description, 0, $description_char_limit);
			$gdescription .= (strlen($description) > $description_char_limit && $description_char_limit != 0) ? "&hellip;" : "";

			//get the group logo
			/*$logo = "/components/com_groups/assets/img/group_default_logo.png";
			$group_logo = $config->get('uploadpath') . DS . $hg->gidNumber . DS . $hg->logo;
			if(isset($hg->logo) && $hg->logo != '' && file_exists(JPATH_ROOT . DS . $group_logo))
			{
				$logo = $group_logo;
			}*/
			$logo = $hg->getLogo();

			//build the html
			$html .= "<div class=\"{$columns} columns {$cls} no-top-border\">";
				$html .= "<div class=\"group-list\">";
					if ($display_logos)
					{
						$html .= "<div class=\"logo\"><img src=\"{$logo}\" alt=\"{$hg->description}\" /></div>";
						$d_cls = "-w-logo";
					}
					else
					{
						$d_cls = "";
					}
					$html .= "<div class=\"details{$d_cls}\">";
						if (!$isPublished)
						{
							$html .= "<h3>{$hg->description}</h3>";
						}
						else
						{
							$html .= "<h3><a href=\"" . \JRoute::_('index.php?option=com_groups&task=view&cn=' . $group->cn) . "\">{$hg->description}</a></h3>";
						}

						if ($gdescription)
						{
							$html .= "<p>{$gdescription}</p>";
						}
						if ($isManager)
						{
							$html .= "<span class=\"status manager\">Manager</span>";
						}
						if (!$isPublished)
						{
							$html .= "<span class=\"status not-published\">".\JText::_('Group has been unpublished by administrator')."</span>";
						}

						if (isset($group->matches))
						{
							$html .= "<ol class=\"tags\">";
								foreach ($group->matches as $t)
								{
									$html .= "<li><a href=\"/tags/".$gt->normalize_tag($t)."\">{$t}</a></li>";
								}
							$html .= "</ol>";
						}
					$html .= "</div>";
				$html .= "</div>";
			$html .= "</div>";

			//increment counter
			$count++;
			$totalCount++;

			//move to next line depending on num columns
			if (($cls == 'second' && $columns == 'two')
			 || ($cls == 'third' && $columns == 'three')
			 || ($cls == 'fourth' && $columns == 'four'))
			{
				$count = 0;
				if (sizeof($groups) > $totalCount)
				{
					$html .= '<br class="clear" /><hr />';
				}
			}
		}

		return $html;
	}

	/**
	 * Converts invite emails to true group
	 *
	 * @return    void
	 */
	public function convertInviteEmails($email, $user_id)
	{
		// @FIXME: Should wrap this up in a nice transaction to handle partial failures and
		// race conditions.

		if (empty($email) || empty($user_id))
		{
			return false;
		}

		$db = \JFactory::getDBO();

		$sql = 'SELECT gidNumber FROM `#__xgroups_inviteemails` WHERE email=' . $db->Quote($email) . ';';

		$db->setQuery($sql);

		$result = $db->loadResultArray();

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

		$sql = 'DELETE FROM `#__xgroups_inviteemails` WHERE email=' . $db->Quote($email) . ' AND gidNumber IN (' . $gids . ');';

		$db->setQuery($sql);

		$result = $db->query();

		if (!$result)
		{
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'search_roles'
	 * Long description (if any) ...
	 *
	 * @param   object  $group Parameter description (if any) ...
	 * @param   string  $role  Parameter description (if any) ...
	 * @return  boolean Return description (if any) ...
	 */
	public static function search_roles($group, $role = '')
	{
		if ($role == '')
			return false;

		$db =  \JFactory::getDBO();

		$query = "SELECT uidNumber FROM #__xgroups_roles as r, #__xgroups_member_roles as m WHERE r.id='" . $role . "' AND r.id=m.roleid AND r.gidNumber='" . $group->gidNumber . "'";

		$db->setQuery($query);

		$result = $db->loadResultArray();

		$result = array_intersect($result, $group->members);

		if (count($result) > 0)
		{
			return $result;
		}
	}

	/**
	 * Short description for 'getPluginAccess'
	 * Long description (if any) ...
	 *
	 * @param   object $group      Parameter description (if any) ...
	 * @param   string $get_plugin Parameter description (if any) ...
	 * @return  mixed  Return description (if any) ...
	 */
	public static function getPluginAccess($group, $get_plugin = '')
	{
		// make sure we have a Hubzero group
		if (!($group instanceof Group))
		{
			return;
		}

		// Get plugins
		\JPluginHelper::importPlugin('groups');
		$dispatcher = \JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_group_plugins = $dispatcher->trigger('onGroupAreas', array());
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
	 * @param      array     Array of database options
	 * @return     object    JDatabase Object
	 */
	public static function getDbo($config = array(), $cname = '')
	{
		// empty instance of db
		$db = \JDatabase::getInstance();

		// make sure we have a group object
		if (!$group = Group::getInstance(\JRequest::getVar('cn', $cname)))
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
			$uploadPath = \JComponentHelper::getparams( 'com_groups' )->get('uploadpath');
			$configPath = JPATH_ROOT . DS . trim($uploadPath, DS) . DS . $group->get('gidNumber') . DS . 'config' . DS . 'db.php';

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