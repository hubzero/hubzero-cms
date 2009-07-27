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

class License extends JTable 
{
	var $id = null;
	var $alias = null;
	var $description = null;
	
	//-----------
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__licenses', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->alias ) == '') {
			$this->setError( JText::_('License must have an alias') );
			return false;
		}
		
		if (trim( $this->description ) == '') {
			$this->setError( JText::_('License must contain text') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getIdfromAlias( $alias )
	{
		if($alias === NULL) {
			return false;
		}
		$query  = "SELECT id FROM $this->_tbl WHERE alias='".$alias."' LIMIT 1";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	
	//-----------
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT l.*, 
					(SELECT COUNT(*) FROM #__licenses_users AS lu WHERE lu.license_id=l.id) AS ucount,
					(SELECT COUNT(*) FROM #__licenses_tools AS lt WHERE lt.license_id=l.id) AS tcount 
					FROM $this->_tbl AS l";
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}


class LicenseUser extends JTable 
{
	var $license_id = NULL;  // @var varchar(50) Primary Key
	var $user_id    = NULL;  // @var int(11) Primary Key

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__licenses_users', 'license_id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (!$this->license_id) {
			$this->setError( JText::_('Must have a license ID.') );
			return false;
		}
		
		if (!$this->user_id) {
			$this->setError( JText::_('Must have a user ID.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function delete( $lid=NULL, $uid=NULL ) 
	{
		if (!$lid) {
			$lid = $this->license_id;
		}
		if (!$uid) {
			$uid = $this->user_id;
		}
		if (!$lid || !$uid) {
			$this->setError( JText::_('Missing required parameter') );
			return false;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE license_id='$lid' AND user_id='$uid'" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	function deleteAssociation( $lid=NULL, $uid=NULL ) 
	{
		if (!$lid) {
			$lid = $this->license_id;
		}
		if (!$uid) {
			$uid = $this->user_id;
		}
		if (!$lid && !$uid) {
			$this->setError( JText::_('Missing required parameter') );
			return false;
		}
		
		if ($lid) {
			$query = "license_id='$lid'";
		}
		if ($uid) {
			$query = "user_id='$uid'";
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE ".$query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS l";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " LEFT JOIN #__users AS u ON u.id=l.user_id";
		}
		$query .= " WHERE l.license_id='".$filters['lid']."'";
		if (isset($filters['search']) && $filters['search'] != '') {
			if (is_numeric($filters['search'])) {
				$query .= " AND l.user_id='".$filters['search']."'";
			} else {
				$query .= " AND (LOWER(u.name) LIKE '%".strtolower($filters['search'])."%' OR LOWER(u.username) LIKE '%".strtolower($filters['search'])."%')";
			}
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT l.*, u.name, u.username FROM $this->_tbl AS l
					LEFT JOIN #__users AS u ON u.id=l.user_id
					WHERE l.license_id='".$filters['lid']."'";
		if (isset($filters['search']) && $filters['search'] != '') {
			if (is_numeric($filters['search'])) {
				$query .= " AND l.user_id='".$filters['search']."'";
			} else {
				$query .= " AND (LOWER(u.name) LIKE '%".strtolower($filters['search'])."%' OR LOWER(u.username) LIKE '%".strtolower($filters['search'])."%')";
			}
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '' && $filters['sortby'] != 'all') {
			$query .= " ORDER BY ".$filters['sortby'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------

	function store( $new=false ) 
	{
		/*if (!$new) {
			$this->_db->setQuery( "UPDATE $this->_tbl SET user_id=".$this->tool_id." WHERE license_id=".$this->child_id." AND parent_id=".$this->parent_id);
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		} else {*/
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		//}
		if (!$ret) {
			$this->setError( strtolower(get_class( $this )).'::store failed <br />' . $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}
	
	//-----------
	
	function getLicenses( $uid=null ) 
	{
		if (!$uid) {
			return false;
		}
		
		$query = "SELECT * FROM $this->_tbl WHERE user_id='$uid'";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}


class LicenseTool extends JTable 
{
	var $license_id = NULL;  // @var varchar(50) Primary Key
	var $tool_id    = NULL;  // @var int(11) Primary Key

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__licenses_tools', 'license_id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (!$this->license_id) {
			$this->setError( JText::_('Must have a license ID.') );
			return false;
		}
		
		if (!$this->tool_id) {
			$this->setError( JText::_('Must have a tool ID.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function delete( $lid=NULL, $tid=NULL ) 
	{
		if (!$lid) {
			$lid = $this->license_id;
		}
		if (!$uid) {
			$tid = $this->tool_id;
		}
		if (!$lid || !$tid) {
			$this->setError( JText::_('Missing required parameter') );
			return false;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE license_id='$lid' AND tool_id='$tid'" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	function deleteAssociation( $lid=NULL, $tid=NULL ) 
	{
		if (!$lid) {
			$lid = $this->license_id;
		}
		if (!$tid) {
			$tid = $this->tool_id;
		}
		if (!$lid && !$tid) {
			$this->setError( JText::_('Missing required parameter') );
			return false;
		}
		
		if ($lid) {
			$query = "license_id='$lid'";
		}
		if ($tid) {
			$query = "tool_id='$tid'";
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE ".$query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " LEFT JOIN #__resources AS r ON r.id=t.tool_id";
		}
		$query .= " WHERE t.license_id='".$filters['lid']."'";
		if (isset($filters['search']) && $filters['search'] != '') {
			if (is_numeric($filters['search'])) {
				$query .= " AND t.tool_id='".$filters['search']."'";
			} else {
				$query .= " AND (LOWER(r.alias) LIKE '%".strtolower($filters['search'])."%' OR LOWER(r.title) LIKE '%".strtolower($filters['search'])."%')";
			}
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT t.*, r.title, r.toolname AS alias FROM $this->_tbl AS t
					LEFT JOIN #__tool_version AS r ON r.id=t.tool_id
					WHERE t.license_id='".$filters['lid']."'";
		if (isset($filters['search']) && $filters['search'] != '') {
			if (is_numeric($filters['search'])) {
				$query .= " AND t.tool_id='".$filters['search']."'";
			} else {
				$query .= " AND (LOWER(r.toolname) LIKE '%".strtolower($filters['search'])."%' OR LOWER(r.title) LIKE '%".strtolower($filters['search'])."%')";
			}
		}
		if (isset($filters['sortby']) && $filters['sortby'] != '' && $filters['sortby'] != 'all') {
			$query .= " ORDER BY ".$filters['sortby'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------

	function store( $new=false ) 
	{
		/*if (!$new) {
			$this->_db->setQuery( "UPDATE $this->_tbl SET user_id=".$this->tool_id." WHERE license_id=".$this->child_id." AND parent_id=".$this->parent_id);
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		} else {*/
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		//}
		if (!$ret) {
			$this->setError( strtolower(get_class( $this )).'::store failed <br />' . $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}
	
	//-----------
	
	function getLicenses( $tool=null ) 
	{
		if (!$tool) {
			return false;
		}
		
		$query = "SELECT t.* FROM $this->_tbl AS t, #__tool_version AS r WHERE r.id=t.tool_id AND r.instance='$tool'";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

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


?>