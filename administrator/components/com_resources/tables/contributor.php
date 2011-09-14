<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

class ResourcesContributor extends JTable
{
	var $subtable = NULL;  // @var varchar(50) Primary Key
	var $subid    = NULL;  // @var int(11) Primary Key
	var $authorid = NULL;  // @var int(11) Primary Key
	var $ordering = NULL;  // @var int(11)
	var $role     = NULL;  // @var varchar(50)
	var $name     = NULL;  // @var varchar(255)
	var $organization = NULL;  // @var varchar(255)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__author_assoc', 'authorid', $db );
	}

	public function check()
	{
		if (!$this->authorid) {
			$this->setError( JText::_('Must have an author ID.') );
			return false;
		}

		if (!$this->subid) {
			$this->setError( JText::_('Must have an item ID.') );
			return false;
		}

		return true;
	}

	public function loadAssociation( $authorid=NULL, $subid=NULL, $subtable='' )
	{
		if (!$authorid) {
			$authorid = $this->authorid;
		}
		if (!$authorid) {
			return false;
		}
		if (!$subid) {
			$subid = $this->subid;
		}
		if (!$subtable) {
			$subtable = $this->subtable;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE subid=".$subid." AND subtable='$subtable' AND authorid=".$authorid );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	public function deleteAssociations( $id=NULL )
	{
		if (!$id) {
			$id = $this->authorid;
		}

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE authorid=".$id );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	public function deleteAssociation( $authorid=NULL, $subid=NULL, $subtable='' )
	{
		if (!$authorid) {
			$authorid = $this->authorid;
		}
		if (!$authorid) {
			return false;
		}
		if (!$subid) {
			$subid = $this->subid;
		}
		if (!$subtable) {
			$subtable = $this->subtable;
		}

		$query = "DELETE FROM $this->_tbl WHERE subtable='$subtable' AND subid=".$subid." AND authorid=".$authorid;

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	public function createAssociation()
	{
		//$query = "INSERT INTO $this->_tbl (subtable, subid, authorid, ordering) VALUES('$this->subtable', $this->subid, $this->authorid, $this->ordering)";
		$query = "INSERT INTO $this->_tbl (subtable, subid, authorid, ordering, role, name, organization) VALUES('$this->subtable', $this->subid, $this->authorid, $this->ordering, '$this->role', '$this->name', '$this->organization')";
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	public function updateAssociation()
	{
		//$query = "UPDATE $this->_tbl SET ordering=$this->ordering WHERE subtable='$this->subtable' AND subid=$this->subid AND authorid=$this->authorid";
		$query = "UPDATE $this->_tbl SET ordering=$this->ordering, role='$this->role', name='$this->name', organization='$this->organization' WHERE subtable='$this->subtable' AND subid=$this->subid AND authorid=$this->authorid";
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	public function getCount( $subid=NULL, $subtable=null )
	{
		if (!$subid) {
			$subid = $this->subid;
		}
		if (!$subid) {
			return null;
		}
		if (!$subtable) {
			$subtable = $this->subtable;
		}
		if (!$subtable) {
			return null;
		}
		$this->_db->setQuery( "SELECT count(*) FROM $this->_tbl WHERE subid=$subid AND subtable='$subtable'" );
		return $this->_db->loadResult();
	}

	public function getLastOrder( $subid=NULL, $subtable=null )
	{
		if (!$subid) {
			$subid = $this->subid;
		}
		if (!$subid) {
			return null;
		}
		if (!$subtable) {
			$subtable = $this->subtable;
		}
		if (!$subtable) {
			return null;
		}
		$this->_db->setQuery( "SELECT ordering FROM $this->_tbl WHERE subid=$subid AND subtable='$subtable' ORDER BY ordering DESC LIMIT 1" );
		return $this->_db->loadResult();
	}

	public function getNeighbor( $move )
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE subid=$this->subid AND subtable='$this->subtable' AND ordering < $this->ordering ORDER BY ordering DESC LIMIT 1";
				break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE subid=$this->subid AND subtable='$this->subtable' AND ordering > $this->ordering ORDER BY ordering LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}

