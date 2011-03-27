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

class EventsEvent extends JTable 
{
	var $id               = NULL;
	var $sid              = NULL;
	var $catid            = NULL;  
	var $title            = NULL;
	var $content          = NULL;  
	var $contact_info     = NULL;
	var $adresse_info     = NULL;
	var $extra_info       = NULL;
	var $color_bar        = NULL;
	var $useCatColor      = NULL;
	var $state            = NULL;
	var $mask             = NULL;
	var $created          = NULL;
	var $created_by       = NULL;
	var $created_by_alias = NULL;
	var $modified         = NULL;
	var $modified_by      = NULL;
	var $checked_out      = NULL;
	var $checked_out_time = NULL;
	var $publish_up       = NULL;
	var $publish_down     = NULL;
	var $images           = NULL;
	var $reccurtype       = NULL;
	var $reccurday        = NULL;
	var $reccurweekdays   = NULL;
	var $reccurweeks      = NULL;  
	var $approved         = NULL;
	var $announcement     = NULL;
	var $ordering         = NULL;
	var $archived         = NULL;  
	var $access           = NULL;
	var $hits             = NULL;
	var $registerby       = NULL;
	var $params           = NULL;
	var $restricted       = NULL;
	var $email            = NULL;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__events', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('EVENTS_MUST_HAVE_TITLE') );
			return false;
		}
		if (trim( $this->catid ) == '' || trim( $this->catid ) == 0) {
			$this->setError( JText::_('EVENTS_MUST_HAVE_CATEGORY') );
			return false;
		}
		return true;
	}

	//-----------
	
	public function hit( $oid=NULL ) 
	{
		$k = $this->_tbl_key;
		if ($oid !== NULL) {
			$this->$k = intval( $oid );
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET hits=(hits+1) WHERE id=$this->id" );
		$this->_db->query();
	}
	
	//-----------
	
	public function publish( $oid=NULL ) 
	{
		if (!$oid) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET state=1 WHERE id=$oid" );
		$this->_db->query();
	}

	//-----------

	public function unpublish( $oid=NULL ) 
	{
		if (!$oid) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET state=0 WHERE id=$oid" );
		$this->_db->query();
	}
	
	//-----------
	
	public function getFirst() 
	{
		$this->_db->setQuery( "SELECT publish_up FROM $this->_tbl ORDER BY publish_up ASC LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getLast() 
	{
		$this->_db->setQuery( "SELECT publish_down FROM $this->_tbl ORDER BY publish_down DESC LIMIT 1" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getEvents( $period='month', $filters=array() ) 
	{
		$gid = (isset($filters['gid'])) ? $filters['gid'] : 0;
		
		// Build the query
		switch ($period) 
		{
			case 'month':
				$select_date = $filters['select_date'];
				$select_date_fin = $filters['select_date_fin'];
				
				$sql = "SELECT $this->_tbl.* 
						FROM #__categories AS b, $this->_tbl
						WHERE $this->_tbl.catid = b.id 
						AND b.access <= $gid 
						AND $this->_tbl.access <= $gid 
						AND ( ((publish_up >= '$select_date%' AND publish_up <= '$select_date_fin%') 
							OR (publish_down >= '$select_date%' AND publish_down <= '$select_date_fin%') 
							OR (publish_up >= '$select_date%' AND publish_down <= '$select_date_fin%') 
							OR (publish_up <= '$select_date%' AND publish_down >= '$select_date_fin%')) 
							AND $this->_tbl.state = '1'";
				$sql .= ($filters['category'] != 0) ? " AND b.id=".$filters['category'] : "";
				$sql .= ") ORDER BY publish_up ASC";
			break;
			
			case 'year':
				$year = $filters['year'];
				
				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl
						WHERE $this->_tbl.catid = b.id AND b.access <= $gid AND $this->_tbl.access <= $gid
						AND publish_up LIKE '$year%' AND (publish_down >= '$year%' OR publish_down = '0000-00-00 00:00:00')
						AND $this->_tbl.state = '1'";
				$sql .= ($filters['category'] != 0) ? " AND b.id=".$filters['category'] : "";
				$sql .= " ORDER BY publish_up ASC";
				//$sql .= " LIMIT ".$filters['start'].", ".$filters['limit'];
			break;
			
			case 'week':
				$startdate = $filters['startdate'];
				$enddate = $filters['enddate'];
				
				$sql = "SELECT * FROM $this->_tbl 
					WHERE ((publish_up >= '$startdate%' AND publish_up <= '$enddate%') 
					OR (publish_down >= '$startdate%' AND publish_down <= '$enddate%') 
					OR (publish_up >= '$startdate%' AND publish_down <= '$enddate%') 
					OR (publish_down >= '$enddate%' AND publish_up <= '$startdate%')) 
					AND state = '1' ORDER BY publish_up ASC";
			break;
			
			case 'day':
				$select_date = $filters['select_date'];
				
				$sql = "SELECT $this->_tbl.* FROM #__categories AS b, $this->_tbl 
						WHERE $this->_tbl.catid = b.id AND b.access <= $gid AND $this->_tbl.access <= $gid AND 
							((publish_up >= '$select_date 00:00:00' AND publish_up <= '$select_date 23:59:59') 
							OR (publish_down >= '$select_date 00:00:00' AND publish_down <= '$select_date 23:59:59') 
							OR (publish_up <= '$select_date 00:00:00' AND publish_down >= '$select_date 23:59:59') 
							OR (publish_up >= '$select_date 00:00:00' AND publish_down <= '$select_date 23:59:59')";
				$sql .= ($filters['category'] != 0) ? " AND b.id=".$filters['category'] : "";
				$sql .= ") AND $this->_tbl.state = '1' ORDER BY publish_up ASC";
			break;
		}
		
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$query = "SELECT count(*) FROM $this->_tbl AS a";
		$where = array();
		if ($filters['catid'] > 0) {
			$where[] = "a.catid='".$filters['catid']."'";
		}
		if ($filters['search']) {
			$where[] = "LOWER(a.title) LIKE '%".$filters['search']."%'";
		}
		$query .= (count( $where )) ? " WHERE ".implode( ' AND ', $where ) : "";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query = "SELECT a.*, cc.name AS category, u.name AS editor, g.name AS groupname 
				FROM $this->_tbl AS a 
				LEFT JOIN #__users AS u ON u.id = a.checked_out 
				LEFT JOIN #__groups AS g ON g.id = a.access, 
				#__categories AS cc";
				
		$where = array();
		if ($filters['catid'] > 0) {
			$where[] = "a.catid='".$filters['catid']."'";
		}
		if ($filters['search']) {
			$where[] = "LOWER(a.title) LIKE '%".$filters['search']."%'";
		}
		$where[] = "a.catid=cc.id";
		
		$query .= (count( $where )) ? " WHERE ".implode( ' AND ', $where ) : "";
		$query .= " ORDER BY a.publish_up DESC LIMIT ".$filters['start'].",".$filters['limit'];
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

