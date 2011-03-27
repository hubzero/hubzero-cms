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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class JobStats extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $itemid			= NULL;  // @var int(11)
	var $category		= NULL;  // job / seeker  / employer
	var $total_viewed	= NULL;
	var $total_shared	= NULL;
	var $viewed_today	= NULL;
	var $lastviewed		= NULL;
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__jobs_stats', 'id', $db );
	}
		
	//-----------
	
	public function check() 
	{
		if (intval( $this->itemid ) == 0) {
			$this->setError( JText::_('Missing item id.') );
			return false;
		}
		
		if (intval( $this->category ) == '') {
			$this->setError( JText::_('Missing category.') );
			return false;
		}

		return true;
	}
	
	//--------
	
	public function loadStat($itemid = NULL, $category = NULL, $type = "viewed")
	{
		if ($itemid === NULL or $category === NULL) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE itemid='$itemid' AND category='$category' ORDER BY ";
		$query .= $type=='shared' ? "lastshared": "lastviewed";
		$query .= " DESC LIMIT 1";

		$this->_db->setQuery( $query );
		
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}
	
	//--------
	
	public function getStats($itemid = NULL, $category = 'employer', $admin = 0)
	{
		if ($itemid === NULL) {
			return false;
		}
		
		$stats = array();
		$stats = array('total_resumes'=> 0,
						'shortlisted' => 0,
						'applied' => 0,
						'bookmarked' => 0,
						'total_viewed' => 0,
						'total_shared' => 0,
						'viewed_today' => 0,
						'viewed_thisweek' => 0,
						'viewed_thismonth' => 0,
						'lastviewed' => '');
		
		// get total resumes in the pool
		$row = new JobSeeker( $this->_db );
		$filters = array('filterby'=>'all', 'sortby'=>'', 'search'=>'', 'category'=>'', 'type'=>'');
		$stats['total_resumes'] = $row->countSeekers( $filters);
		
		// get stats for employer
		if ($category == 'employer') {
			$filters['filterby'] = 'shortlisted';
			$stats['shortlisted'] = $row->countSeekers( $filters, $itemid);
			
			$filters['filterby'] = 'applied';
			$itemid = $admin ? 1 : $itemid;
			$stats['applied'] = $row->countSeekers( $filters, $itemid);
		}
		
		// get stats for seeker
		if ($category == 'seeker') {
			$stats['totalviewed'] = $this->getView($itemid, $category);
			$stats['viewed_today'] = $this->getView($itemid, $category, 'viewed', 'today');
			$stats['viewed_thisweek'] = $this->getView($itemid, $category, 'viewed', 'thisweek');
			$stats['viewed_thismonth'] = $this->getView($itemid, $category, 'viewed', 'thismonth');
			$stats['shortlisted'] = $row->countShortlistedBy($itemid);
		}
		
		return $stats;	
	}
	
	//--------------
	
	public function getView( $itemid=NULL, $category=NULL, $type='viewed', $when ='') 
	{
		$lastweek = date('Y-m-d H:i:s', time() - (7 * 24 * 60 * 60));
		$lastmonth = date('Y-m-d H:i:s', time() - (30 * 24 * 60 * 60));
		$today = date('Y-m-d H:i:s', time() - (24 * 60 * 60));
		
		$query  = "SELECT ";
		if ($type == 'viewed') {
			$query .= $when ? " SUM(viewed_today) AS times " : " MAX(total_viewed) AS times ";
		} else {
			$query .= " MAX(p.total_shared) AS times ";
		}
		$query .= " FROM $this->_tbl WHERE itemid='$itemid' AND category='$category' AND ";
	
		if ($when == 'thisweek') {
			$query .= " lastviewed > '".$lastweek."' ";
		} else if($when == 'thismonth') {
			$query .= " lastviewed > '".$lastmonth."' ";
		} else if ($when == 'today') {
			$query .= " lastviewed > '".$today."' ";
		} else {
			$query .= " 1=1 ";
		}			
		$query .= "GROUP BY itemid, category ";		
		$query .= "ORDER BY times DESC ";
		$query .= "LIMIT 1";
		
		$this->_db->setQuery( $query );
		$result =  $this->_db->loadResult();
		
		$result = $result ? $result : 0;
		return $result;		
	}
	
	//--------------
	
	public function saveView( $itemid=NULL, $category=NULL, $type='viewed') 
	{
		if ($itemid=== NULL) {
			$itemid = $this->itemid;
		}
		if ($category === NULL) {
			$category = $this->category;
		}
		
		if ($itemid === NULL or $category === NULL) {
			return false;
		}
		
		$today = date( 'Y-m-d');
		$now = date( 'Y-m-d H:i:s' );

		// load existing entry
		$this->loadStat( $itemid, $category);
		
		// create new entry for another day
		if (substr($this->lastviewed, 0, 10) != $today ) {
			$this->id = 0;
			$this->itemid = $itemid;
			$this->category = $category;
			$this->viewed_today = 1;
		} else {
			$this->viewed_today = $this->viewed_today + 1;
		}
		
		$this->total_viewed = $this->total_viewed + 1;
		
		// avoid duplicates
		if ($this->lastviewed != $now) {
			$this->lastviewed = $now;
			
			if (!$this->store()) {
				$this->setError( JText::_('Failed to store item view.') );
				return false;
			} else {
				// clean-up views older than 30 days
				$this->cleanup();
			}			
		}			
	}
	
	//--------------
	
	public function cleanup() 
	{
		$lastmonth = date('Y-m-d H:i:s', time() - (30 * 24 * 60 * 60));
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE lastviewed < '".$lastmonth."'");
		$this->_db->query();
	}
	
	//--------------
	
	public function deleteStats($itemid, $category) 
	{
		if ($itemid === NULL or $category === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE itemid ='$itemid' AND category ='$category'");
		$this->_db->query();
	}	
}

