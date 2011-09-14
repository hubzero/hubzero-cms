<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

class WishlistPlan extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $wishid		= NULL;  // @var int(11)
	var $version	= NULL;  // @var int(11)
	var $created	= NULL;
	var $created_by	= NULL;
	var $minor_edit	= NULL;
	var $pagetext	= NULL;
	var $pagehtml	= NULL;
	var $approved   = NULL;
	var $summary	= NULL;

	public function __construct( &$db )
	{
		parent::__construct( '#__wishlist_implementation', 'id', $db );
	}

	public function getPlan($wishid)
	{
		if ($wishid == NULL) {
			return false;
		}

		$query  = "SELECT *, xp.name AS authorname ";
		$query .= "FROM #__wishlist_implementation AS p  ";
		$query .= "JOIN #__xprofiles AS xp ON xp.uidNumber=p.created_by ";
		$query .= "WHERE p.wishid = '".$wishid."' ORDER BY p.created DESC LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	public function load( $oid=NULL )
	{
		if ($oid == NULL or !is_numeric($oid)) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE id='$oid'" );
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	public function deletePlan($wishid)
	{
		if ($wishid == NULL) {
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE wishid='". $wishid."'";
		$this->_db->setQuery( $query );
		$this->_db->query();
	}
}

