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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for wishlist owner group
 */
class WishlistOwnerGroup extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_ownergroups', 'id', $db);
	}

	/**
	 * Get the groups ow a wishlist owner
	 *
	 * @param   integer  $listid        List ID
	 * @param   string   $controlgroup  Control group name
	 * @param   object   $wishlist      Wishlist
	 * @param   integer  $native        Get groups assigned to this wishlist?
	 * @param   array    $groups        List of gorups
	 * @return  mixed    False if errors, array on success
	 */
	public function get_owner_groups($listid, $controlgroup='', $wishlist=null, $native=0, $groups = array())
	{
		if ($listid === NULL)
		{
			return false;
		}

		$wishgroups = array();

		$obj = new Wishlist($this->_db);

		// if tool, get tool group
		if (!$wishlist)
		{
			$wishlist = $obj->get_wishlist($listid);
		}
		if (isset($wishlist->resource) && $wishlist->resource->type == 7)
		{
			$toolgroup = $obj->getToolDevGroup ($wishlist->referenceid);
			if ($toolgroup)
			{
				$groups[] = $toolgroup;
			}
		}

		// if primary list, add all site admins
		if ($controlgroup && $wishlist->category == 'general')
		{
			$instance = \Hubzero\User\Group::getInstance($controlgroup);

			if (is_object($instance))
			{
				$gid = $instance->get('gidNumber');
				if ($gid)
				{
					$groups[] = $gid;
				}
			}
		}

		// if private group list, add the group
		if ($wishlist->category == 'group')
		{
			$groups[] = $wishlist->referenceid;
		}

		// get groups assigned to this wishlist
		if (!$native)
		{
			$sql = "SELECT o.groupid FROM `#__wishlist_ownergroups` AS o WHERE o.wishlist=" . $this->_db->Quote($listid);

			$this->_db->setQuery($sql);
			$wishgroups = $this->_db->loadObjectList();

			if ($wishgroups)
			{
				foreach ($wishgroups as $wg)
				{
					if (\Hubzero\User\Group::exists($wg->groupid))
					{
						$groups[] = $wg->groupid;
					}
				}
			}
		}

		$groups = array_unique($groups);
		sort($groups);
		return $groups;
	 }

	/**
	 * Remove a user as owner
	 *
	 * @param   integer  $listid      List ID
	 * @param   integer  $groupid     Group ID
	 * @param   object   $admingroup  Admin group
	 * @return  boolean  False if errors, true on success
	 */
	 public function delete_owner_group($listid, $groupid, $admingroup)
	 {
		if ($listid === NULL or $groupid === NULL)
		{
			return false;
		}

		$nativegroups = $this->get_owner_groups($listid, $admingroup, '', 1);

		// cannot delete "native" owners (e.g. tool dev group)
		if (\Hubzero\User\Group::exists($groupid)
		 && !in_array($groupid, $nativegroups, true))
		{
			$query = "DELETE FROM $this->_tbl WHERE wishlist=" . $this->_db->Quote($listid) . " AND groupid=" . $this->_db->Quote($groupid);
			$this->_db->setQuery($query);
			$this->_db->query();
			return true;
		}
	}

	/**
	 * Add a user as owner to groups
	 *
	 * @param   integer  $listid      Wishlist ID
	 * @param   object   $admingroup  Admin group
	 * @param   array    $newgroups   Groups to add to
	 * @return  boolean  True on success
	 */
	public function save_owner_groups($listid, $admingroup, $newgroups = array())
	{
		if ($listid === NULL)
		{
			return false;
		}

		$groups = $this->get_owner_groups($listid, $admingroup);

		if (count($newgroups) > 0)
		{
			foreach ($newgroups as $ng)
			{
				$instance = \Hubzero\User\Group::getInstance($ng);
				if (is_object($instance))
				{
					$gid = $instance->get('gidNumber');

					if ($gid && !in_array($gid, $groups, true))
					{
						$this->id       = 0;
						$this->groupid  = $gid;
						$this->wishlist = $listid;

						if (!$this->store())
						{
							$this->setError(JText::_('Failed to add a user.'));
							return false;
						}
					}
				}
			}
		}
		return true;
	}
}

