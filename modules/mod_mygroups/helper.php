<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a list of groups for a user
 */
class modMyGroups extends \Hubzero\Module\Module
{
	/**
	 * Get groups for a user
	 *
	 * @param      integer $uid  User ID
	 * @param      string  $type Membership type to return groups for
	 * @return     array
	 */
	private function _getGroups($uid, $type='all')
	{
		$db = JFactory::getDBO();

		// Get all groups the user is a member of
		$query1 = "SELECT g.published, g.approved, g.description, g.cn, '1' AS registered, '0' AS regconfirmed, '0' AS manager
				   FROM #__xgroups AS g, #__xgroups_applicants AS m
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		$query2 = "SELECT g.published, g.approved, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '0' AS manager
				   FROM #__xgroups AS g, #__xgroups_members AS m
				   WHERE (g.type='1' || g.type='3') AND m.uidNumber NOT IN
						(SELECT uidNumber
						 FROM #__xgroups_managers AS manager
						 WHERE manager.gidNumber = m.gidNumber)
				   AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		$query3 = "SELECT g.published, g.approved, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '1' AS manager
				   FROM #__xgroups AS g, #__xgroups_managers AS m
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		$query4 = "SELECT g.published, g.approved, g.description, g.cn, '0' AS registered, '1' AS regconfirmed, '0' AS manager
				   FROM #__xgroups AS g, #__xgroups_invitees AS m
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		switch ($type)
		{
			case 'all':
				$query = "( $query1 ) UNION ( $query2 ) UNION ( $query3 ) UNION ( $query4 ) ORDER BY description ASC";
			break;
			case 'applicants':
				$query = $query1;
			break;
			case 'members':
				$query = $query2;
			break;
			case 'managers':
				$query = $query3;
			break;
			case 'invitees':
				$query = $query4;
			break;
		}

		$db->setQuery($query);
		$db->query();

		$result = $db->loadObjectList();

		if (empty($result))
		{
			return array();
		}

		return $result;
	}

	/**
	 * Get the user's status in the gorup
	 *
	 * @param      object $group Group to check status in
	 * @return     string
	 */
	public function getStatus($group)
	{
		if ($group->manager)
		{
			$status = 'manager';
		}
		else
		{
			if ($group->registered)
			{
				if ($group->regconfirmed)
				{
					$status = 'member';
				}
				else
				{
					$status = 'pending';
				}
			}
			else
			{
				if ($group->regconfirmed)
				{
					$status = 'invitee';
				}
				else
				{
					$status = '';
				}
			}
		}
		return $status;
	}

	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		$juser = JFactory::getUser();

		// Get the module parameters
		$this->moduleclass = $this->params->get('moduleclass');
		$this->limit = intval($this->params->get('limit', 10));

		// Get the user's groups
		$members = $this->_getGroups($juser->get('id'), 'all');

		$groups = array();
		foreach ($members as $mem)
		{
			$groups[] = $mem;
		}
		$this->groups = $groups;

		// Push the module CSS to the template
		$this->css();

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}

