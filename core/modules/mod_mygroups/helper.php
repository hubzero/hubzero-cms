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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyGroups;

use Hubzero\Module\Module;
use Components\Groups\Models\Recent;
use Hubzero\User\Group;
use User;

/**
 * Module class for displaying a list of groups for a user
 */
class Helper extends Module
{
	/**
	 * Get groups for a user
	 *
	 * @param   integer  $uid   User ID
	 * @param   string   $type  Membership type to return groups for
	 * @return  array
	 */
	private function _getGroups($uid, $type='all', $groups=array())
	{
		$db = \App::get('db');

		// Get all groups the user is a member of
		$query1 = "SELECT g.gidNumber, g.published, g.approved, g.description, g.cn, '1' AS registered, '0' AS regconfirmed, '0' AS manager
				   FROM `#__xgroups` AS g, `#__xgroups_applicants` AS m
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		$query2 = "SELECT g.gidNumber, g.published, g.approved, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '0' AS manager
				   FROM `#__xgroups` AS g, `#__xgroups_members` AS m
				   WHERE (g.type='1' || g.type='3') AND m.uidNumber NOT IN
						(SELECT uidNumber
						 FROM `#__xgroups_managers` AS manager
						 WHERE manager.gidNumber = m.gidNumber)
				   AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		$query3 = "SELECT g.gidNumber, g.published, g.approved, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '1' AS manager
				   FROM `#__xgroups` AS g, `#__xgroups_managers` AS m
				   WHERE (g.type='1' || g.type='3') AND m.gidNumber=g.gidNumber AND m.uidNumber=" . $uid;

		$query4 = "SELECT g.gidNumber, g.published, g.approved, g.description, g.cn, '0' AS registered, '1' AS regconfirmed, '0' AS manager
				   FROM `#__xgroups` AS g, `#__xgroups_invitees` AS m
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

		if (!empty($groups))
		{
			$query .= " WHERE g.cn IN (" . implode(',', $groups) . ")";
		}

		$db->setQuery($query);
		$db->query();

		return $db->loadObjectList();
	}

	/**
	 * Get the user's status in the gorup
	 *
	 * @param   object  $group  Group to check status in
	 * @return  string
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
	 * @return  void
	 */
	public function display()
	{
		// Get the module parameters
		$this->moduleclass = $this->params->get('moduleclass');
		$this->limit = intval($this->params->get('limit', 100));
		$this->recentgroups = array();

		// Get the user's groups
		$this->allgroups = $this->_getGroups(User::get('id'), 'all');

		include_once(\Component::path('com_groups') . DS . 'models' . DS . 'recent.php');

		$recents = Recent::all()
			->whereEquals('user_id', User::get('id'))
			->order('created', 'desc')
			->limit(5)
			->rows();

		foreach ($this->allgroups as $group)
		{
			foreach ($recents as $recent)
			{
				if ($recent->get('group_id') == $group->gidNumber)
				{
					$this->recentgroups[] = $group;
				}
			}
		}

		if (!User::authorise('core.create', 'com_groups'))
		{
			$this->params->set('button_show_add', 0);
		}

		require $this->getLayoutPath();
	}
}

