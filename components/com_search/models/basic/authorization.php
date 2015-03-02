<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Search\Models\Basic;

/**
 * Authorization checker
 */
class Authorization
{
	/**
	 * Description for 'uid'
	 *
	 * @var string
	 */
	private $uid = NULL, $super_admin = false, $groups = NULL;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$juser = \JFactory::getUser();
		if ($juser->guest)
		{
			$this->groups = array();
			return;
		}

		$this->uid = $juser->get('id');

		if ($juser->usertype == 'Super Administrator')
		{
			$this->super_admin = true;
		}
	}

	/**
	 * Is the user logged out?
	 *
	 * @return  bolean
	 */
	public function is_guest()
	{
		return is_null($this->uid);
	}

	/**
	 * Is the user a super admin?
	 *
	 * @return  boolean
	 */
	public function is_super_admin()
	{
		return $this->super_admin;
	}

	/**
	 * Get a user's groups
	 *
	 * @return  array
	 */
	public function get_groups()
	{
		if (is_null($this->groups))
		{
			$dbh = \JFactory::getDBO();
			$dbh->setQuery(
				'SELECT DISTINCT xm.gidNumber, g.cn
				FROM `#__xgroups_members` AS xm
				INNER JOIN `#__xgroups` AS g ON g.gidNumber = xm.gidNumber
				WHERE xm.uidNumber = ' . $this->uid . '
				UNION
				SELECT DISTINCT xm.gidNumber, g.cn
				FROM `#__xgroups_managers` AS xm
				INNER JOIN `#__xgroups` AS g ON g.gidNumber = xm.gidNumber
				WHERE xm.uidNumber = ' . $this->uid
			);

			$this->groups = array();
			foreach ($dbh->loadAssocList() as $row)
			{
				$this->groups[$row['gidNumber']] = $row['cn'];
			}
		}
		return $this->groups;
	}

	/**
	 * Get group IDs
	 *
	 * @return  array
	 */
	public function get_group_ids()
	{
		return array_keys($this->get_groups());
	}

	/**
	 * Get group names
	 *
	 * @return  array
	 */
	public function get_group_names()
	{
		return array_values($this->get_groups());
	}
}

