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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Group');

/**
 * Short description for 'WishlistOwnerGroup'
 * 
 * Long description (if any) ...
 */
class WishlistOwnerGroup extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var integer
	 */
	var $id         = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'wishlist'
	 * 
	 * @var unknown
	 */
	var $wishlist	= NULL;  // @var int(11)


	/**
	 * Description for 'groupid'
	 * 
	 * @var unknown
	 */
	var $groupid	= NULL;  // @var int(11)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__wishlist_ownergroups', 'id', $db );
	}

	/**
	 * Short description for 'get_owner_groups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listid Parameter description (if any) ...
	 * @param      string $controlgroup Parameter description (if any) ...
	 * @param      mixed $wishlist Parameter description (if any) ...
	 * @param      integer $native Parameter description (if any) ...
	 * @param      array $groups Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function get_owner_groups($listid, $controlgroup='', $wishlist='', $native=0, $groups = array())
	{
		ximport('Hubzero_Group');

		if ($listid === NULL) {
			return false;
		}

		$wishgroups = array();

		$obj = new Wishlist( $this->_db );

		// if tool, get tool group
		if (!$wishlist) {
			$wishlist = $obj->get_wishlist($listid);
		}
		if (isset($wishlist->resource) && $wishlist->resource->type=='7') {
			$toolgroup = $obj->getToolDevGroup ($wishlist->referenceid);
			if ($toolgroup) { $groups[] = $toolgroup; }
		}

		// if primary list, add all site admins
		if ($controlgroup && $wishlist->category=='general') {
			$instance = Hubzero_Group::getInstance($controlgroup);

			if (is_object($instance)) {
				$gid = $instance->get('gidNumber');
				if ($gid) {
					$groups[] = $gid;
				}
			}
		}

		// if private group list, add the group
		if ($wishlist->category == 'group') {
			$groups[] = $wishlist->referenceid;
		}

		// get groups assigned to this wishlist
		if (!$native) {
			$sql = "SELECT o.groupid"
				. "\n FROM #__wishlist_ownergroups AS o "
				. "\n WHERE o.wishlist='".$listid."'";

			$this->_db->setQuery( $sql );
			$wishgroups = $this->_db->loadObjectList();

			if ($wishgroups) {
				foreach ($wishgroups as $wg)
				{
					if (Hubzero_Group::exists($wg->groupid)) {
						$groups[]=$wg->groupid;
					}
				}
			}
		}

		$groups = array_unique($groups);
		sort($groups);
		return $groups;
	 }

	/**
	 * Short description for 'delete_owner_group'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listid Parameter description (if any) ...
	 * @param      string $groupid Parameter description (if any) ...
	 * @param      unknown $admingroup Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	 public function delete_owner_group($listid, $groupid, $admingroup)
	 {
	 	ximport('Hubzero_Group');

		if ($listid === NULL or $groupid === NULL) {
			return false;
		}

		$nativegroups = $this->get_owner_groups($listid, $admingroup, '', 1);

		// cannot delete "native" owners (e.g. tool dev group)
		if (Hubzero_Group::exists($groupid) && !in_array($groupid, $nativegroups, true)) {
			$query = "DELETE FROM $this->_tbl WHERE wishlist='". $listid."' AND groupid='".$groupid."'";
			$this->_db->setQuery( $query );
			$this->_db->query();
		}
	}

	/**
	 * Short description for 'save_owner_groups'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $listid Parameter description (if any) ...
	 * @param      unknown $admingroup Parameter description (if any) ...
	 * @param      array $newgroups Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function save_owner_groups($listid, $admingroup, $newgroups = array())
	{
		if ($listid === NULL) {
			return false;
		}

		$groups = $this->get_owner_groups($listid, $admingroup);

		if( count($newgroups) > 0)  {
			foreach ($newgroups as $ng)
			{
				$instance = Hubzero_Group::getInstance($ng);
				if (is_object($instance))  {
					$gid = $instance->get('gidNumber');

					if ($gid && !in_array($gid, $groups, true)) {
						$this->id = 0;
						$this->groupid = $gid;
						$this->wishlist = $listid;

						if (!$this->store()) {
							$this->setError( JText::_('Failed to add a user.') );
							return false;
						}
					}
				}
			}
		}
	}
}

