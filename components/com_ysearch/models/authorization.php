<?php
/**
 * @package     hubzero-cms
 * @author      Steve Snyder <snyder13@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class YSearchAuthorization
{
	private $uid = NULL, $super_admin = false, $groups = NULL;

	public function __construct()
	{
		$juser =& JFactory::getUser();
		if ($juser->guest)
		{
			$this->groups = array();
			return;
		}
		$this->uid = $juser->get('id');
		if ($juser->usertype == 'Super Administrator')
			$this->super_admin = true;
	}

	public function is_guest() { return is_null($this->uid); }
	public function is_super_admin() { return $this->super_admin; }
	public function get_groups()
	{
		if (is_null($this->groups))
		{
			$dbh =& JFactory::getDBO();
			$dbh->setQuery(
				'select distinct xm.gidNumber, cn from #__xgroups_members xm inner join #__xgroups g on g.gidNumber = xm.gidNumber where uidNumber = '.$this->uid.' union select distinct xm.gidNumber, cn from #__xgroups_managers xm inner join #__xgroups g on g.gidNumber = xm.gidNumber where uidNumber = '.$this->uid
			);

			$this->groups = array();
			foreach ($dbh->loadAssocList() as $row)
				$this->groups[$row['gidNumber']] = $row['cn'];
		}
		return $this->groups;
	}
	public function get_group_ids()
	{
		return array_keys($this->get_groups());
	}
	public function get_group_names()
	{
		return array_values($this->get_groups());
	}
}

