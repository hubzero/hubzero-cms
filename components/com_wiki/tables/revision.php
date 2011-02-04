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


class WikiPageRevision extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $pageid     = NULL;  // @var int(11)
	var $version    = NULL;  // @var int(11)
	var $created    = NULL;  // @var datetime
	var $created_by = NULL;  // @var int(11)
	var $minor_edit = NULL;  // @var int(1)
	var $pagetext   = NULL;  // @var text
	var $pagehtml   = NULL;  // @var text
	var $approved   = NULL;  // @var int(1)
	var $summary    = NULL;  // @var varchar(255)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wiki_version', 'id', $db );
	}
	
	//-----------
	
	public function loadByVersion( $pageid, $version=0 ) 
	{
		if ($version) {
			$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='$pageid' AND version='$version'" );
		} else {
			$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='$pageid' AND approved='1' ORDER BY version DESC LIMIT 1" );
		}
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getContributors() 
	{
		$this->_db->setQuery( "SELECT DISTINCT created_by AS id FROM $this->_tbl WHERE pageid='$this->pageid' AND approved='1'" );
		$contributors = $this->_db->loadObjectList();
		
		$cons = array();
		if (count($contributors) > 0) {
			foreach ($contributors as $con) 
			{
				$cons[] = $con->id;
			}
		}
		return $cons;
	}
	
	//-----------
	
	public function getRevisionCount() 
	{
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE pageid='$this->pageid' AND approved='1'" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRevisionNumbers( $pageid=NULL ) 
	{
		if (!$pageid) {
			$pageid = $this->pageid;
		}
		$this->_db->setQuery( "SELECT DISTINCT version FROM $this->_tbl WHERE pageid='$pageid' AND approved='1' ORDER BY version DESC" );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getRevisions( $pageid=NULL ) 
	{
		if (!$pageid) {
			$pageid = $this->pageid;
		}
		//$this->_db->setQuery( "SELECT id, pageid, version, created, created_by, minor_edit, approved, summary FROM $this->_tbl WHERE pageid='$pageid' ORDER BY version DESC, created DESC" );
		//return $this->_db->loadObjectList();
		return $this->getRecords( array('pageid'=>$pageid) );
	}
	
	
	public function getRecordsCount( $filters=array() ) 
	{
		$sql  = "SELECT COUNT(*) ";
		$sql .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	public function getRecords( $filters=array() ) 
	{
		$sql  = "SELECT r.id, r.pageid, r.version, r.created, r.created_by, r.minor_edit, r.approved, r.summary, u.name AS created_by_name, u.username AS created_by_alias ";
		$sql .= $this->buildQuery( $filters );
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	public function buildQuery($filters) 
	{
		$query = " FROM $this->_tbl AS r,
		 			#__users AS u 
					WHERE r.created_by=u.id AND r.pageid='".$filters['pageid']."'";
		if (isset($filters['search']) && $filters['search']) {
			$query .= " AND LOWER( r.pagehtml ) LIKE '%".strtolower($filters['search'])."%'";
		}
		
		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY ".$filters['sortby'];
		} else {
			$query .= " ORDER BY version DESC, created DESC";
		}
		
		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $query;
	}
}
