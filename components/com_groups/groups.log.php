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

class XGroupLog extends JTable 
{
	var $id        = NULL;  // @var int(11) Primary key
	var $gid       = NULL;  // @var int(11)
	var $timestamp = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $uid       = NULL;  // @var int(11)
	var $action    = NULL;  // @var varchar(50)
	var $comments  = NULL;  // @var text
	var $actorid   = NULL;  // @var int(11)
	
	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__xgroups_log', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->gid ) == '') {
			$this->setError( JText::_('GROUPS_LOGS_MUST_HAVE_GROUP_ID') );
			return false;
		}
		
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('GROUPS_LOGS_MUST_HAVE_USER_ID') );
			return false;
		}
		
		return true;
	}
	
	//-----------
	
	function getLogs( $gid=null, $limit=5 ) 
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}
		
		$query = "SELECT * FROM $this->_tbl WHERE gid=$gid ORDER BY `timestamp` DESC";
		if ($limit) {
			$query .= " LIMIT ".$limit;
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getLog( $gid=null, $which='first' ) 
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}
		
		$query = "SELECT * FROM $this->_tbl WHERE gid=$gid ";
		if ($which == 'first') {
			$query .= "ORDER BY `timestamp` ASC LIMIT 1";
		} else {
			$query .= "ORDER BY `timestamp` DESC LIMIT 1";
		}
		
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function deleteLogs( $gid=null ) 
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}
		
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE gid=".$gid );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function logCount( $gid=null, $action='' ) 
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}
		
		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE gid=$gid";
		if ($action) {
			$query .= " AND action='$action'";
		}
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
?>