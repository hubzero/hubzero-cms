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
// Resource Types class - USE CONTRIBTOOL TOOL CLASSES INSTEAD
//----------------------------------------------------------
/*
class ToolTool extends JTable 
{
	var $id            = NULL;  // @var int(11) Primary key
	var $toolname      = NULL;  // @var varchar(250)
	var $title         = NULL;  // @var int(11)
	var $version       = NULL;
	var $description   = NULL;
	var $fulltext      = NULL;
	var $license       = NULL;
	var $toolaccess    = NULL;
	var $codeaccess    = NULL;
	var $wikiaccess    = NULL;
	var $published     = NULL;
	var $state         = NULL;
	var $priority      = NULL;
	var $team          = NULL;
	var $registered    = NULL;
	var $registered_by = NULL;
	var $mw            = NULL;
	var $vnc_geometry  = NULL;
	var $ticketid      = NULL;
	var $state_changed = NULL;
	var $revision      = NULL;
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__tool', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->toolname ) == '') {
			$this->setError( JText::_('Your tool must have a toolname.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadFromName( $toolname )
	{
		if ($toolname === NULL) {
			return false;
		}
		
		$query = "SELECT * FROM $this->_tbl as t WHERE t.toolname= '".$toolname."' LIMIT 1";
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}


class ToolVersion extends JTable 
{
	var $id           = NULL;  // @var int(11) Primary key
	var $toolname     = NULL;  // @var varchar(250)
	var $instance     = NULL;  // @var varchar(31)
	var $title        = NULL;  // @var int(11)
	var $description  = NULL;
	var $fulltext     = NULL;
	var $version      = NULL;
	var $revision     = NULL;
	var $toolaccess   = NULL;
	var $codeaccess   = NULL;
	var $wikiaccess   = NULL;
	var $state        = NULL;
	var $released_by  = NULL;
	var $released     = NULL;
	var $unpublished  = NULL;
	var $license      = NULL;
	var $vnc_geometry = NULL;
	var $mw           = NULL;
	var $toolid       = NULL;
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__tool_version', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->toolname ) == '') {
			$this->setError( JText::_('Your tool must have a toolname.') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function loadFromInstance( $tool=NULL ) 
	{
		if ($tool === NULL) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl AS v WHERE v.instance='".$tool."' LIMIT 1";
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function loadFromName( $alias ) 
	{
		if ($alias === NULL) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl as v WHERE v.toolname='".$alias."' AND state='1' ORDER BY v.revision DESC LIMIT 1";
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getVersions( $alias )
	{
		if ($alias === NULL) {
			$alias = $this->toolname;
		}
		if (!$alias) {
			return false;
		}
		
		$rd = new ResourcesDoi( $this->_db );
		
		$query  = "SELECT v.*, d.doi_label as doi ";
		$query .= "FROM $this->_tbl as v ";
		$query .= "LEFT JOIN $rd->_tbl as d ON d.alias=v.toolname  AND d.local_revision=v.revision ";
		$query .= "WHERE v.toolname = '".$alias."' ORDER BY v.revision DESC";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}


class ToolAuthor extends JTable 
{
	var $toolname = NULL;  // @var int(11) Primary key
	var $revision = NULL;  // @var varchar(250)
	var $uid      = NULL;  // @var int(11)
	var $ordring  = NULL;
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__tool_authors', 'toolname', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->toolname ) == '') {
			$this->setError( JText::_('Your entry must have a toolname.') );
			return false;
		}
		if (trim( $this->revision ) == '') {
			$this->setError( JText::_('Your entry must have a revision.') );
			return false;
		}
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Your entry must have a user ID.') );
			return false;
		}
		return true;
	}
}
*/

class ToolLicense extends JTable 
{
	var $id  = NULL;  // @var int(11) Primary key
	var $name     = NULL;  // @var varchar(250)
	var $text = NULL;  // @var int(11)
	var $title = NULL;
	var $ordering = NULL;
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__tool_licenses', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->name ) == '') {
			$this->setError( JText::_('Your entry must have a name.') );
			return false;
		}
		return true;
	}
}
