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


class ResourcesType extends JTable 
{
	var $id       = NULL;  // @var int(11) Primary key
	var $type     = NULL;  // @var varchar(250)
	var $category = NULL;  // @var int(11)
	var $description = NULL;  // @var text
	var $contributable = NULL;  // @var int(2)
	var $customFields = NULL;  // @var text
	var $params = NULL;  // @var text
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__resource_types', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->type ) == '') {
			$this->setError( JText::_('Your resource type must contain text.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getMajorTypes() 
	{
		return $this->getTypes( 27 );
	}
	
	//-----------
	
	public function getAllCount( $filters=array() ) 
	{
		$query = "SELECT count(*) FROM $this->_tbl";
		if (isset($filters['category']) && $filters['category'] != 0) {
			$query .= " WHERE category=".$filters['category'];
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getAllTypes( $filters=array() ) 
	{
		$query  = "SELECT * FROM $this->_tbl ";
		if (isset($filters['category']) && $filters['category'] != 0) {
			$query .= "WHERE category=".$filters['category']." ";
		}
		$query .= "ORDER BY ".$filters['sort']." ".$filters['sort_Dir']." ";
		$query .= "LIMIT ".$filters['start'].",".$filters['limit'];
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getTypes( $cat='0' ) 
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE category=".$cat." ORDER BY type" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function checkUsage( $id=NULL ) 
	{
		if (!$id) {
			$id = $this->id;
		}
		if (!$id) {
			return false;
		}
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );
		
		$r = new ResourcesResource( $this->_db );
		
		$this->_db->setQuery( "SELECT count(*) FROM $r->_tbl WHERE type=".$id." OR logical_type=".$id );
		return $this->_db->loadResult();
	}
}

