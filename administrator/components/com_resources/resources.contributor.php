<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

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

	function __construct( &$db ) 
	{
		parent::__construct( '#__author_assoc', 'authorid', $db );
	}
	
	//-----------
	
	function check() 
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
	
	//-----------
	
	function loadAssociation( $authorid=NULL, $subid=NULL, $subtable='' ) 
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
	
	//-----------
	
	function deleteAssociations( $id=NULL ) 
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
	
	//-----------
	
	function deleteAssociation( $authorid=NULL, $subid=NULL, $subtable='' ) 
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
	
	//-----------
	
	function createAssociation() 
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
	
	//-----------
	
	function updateAssociation() 
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
	
	//-----------
	
	function getCount( $subid=NULL, $subtable=null ) 
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
	
	//-----------
	
	function getLastOrder( $subid=NULL, $subtable=null ) 
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
	
	//-----------
	
	function getNeighbor( $move ) 
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
?>