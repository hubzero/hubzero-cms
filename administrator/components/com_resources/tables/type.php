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
