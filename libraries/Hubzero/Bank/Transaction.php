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


class Hubzero_Bank_Transaction extends JTable 
{
	var $id          = NULL;  // @var int(11) Primary key
	var $uid         = NULL;  // @var int(11)
	var $type        = NULL;  // @var varchar(20)
	var $category    = NULL;  // @var varchar(50)
	var $referenceid = NULL;  // @var int(11)
	var $amount      = NULL;  // @var int(11)
	var $description = NULL;  // @var varchar(250)
	var $created     = NULL;  // @var datetime
	var $balance     = NULL;  // @var int(11)

	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__users_transactions', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->_error = 'Entry must have a user ID.';
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function history( $limit=50, $uid=null )
	{
		if ($uid == null) {
			$uid = $this->uid;
		}
		if ($uid == null) {
			return false;
		}
		$lmt = "";
		if ($limit > 0) {
			$lmt .= " LIMIT ".$limit;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid=".$uid." ORDER BY created DESC, id DESC".$lmt );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function deleteRecords( $category=null, $type=null, $referenceid=null ) 
	{
		if ($referenceid == null) {
			$referenceid = $this->referenceid;
		}
		if ($referenceid == null) {
			return false;
		}
		if ($type == null) {
			$type = $this->type;
		}
		if ($category == null) {
			$category = $this->category;
		}
		
		$query = "DELETE FROM $this->_tbl WHERE category='".$category."' AND type='".$type."' AND referenceid=".$referenceid;
		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
	}
	
	//-----------
	
	public function getTransactions( $category=null, $type=null, $referenceid=null, $uid=null ) 
	{ 
		if ($referenceid == null) {
			$referenceid = $this->referenceid;
		}
		if ($referenceid == null) {
			return false;
		}
		if ($type == null) {
			$type = $this->type;
		}
		if ($category == null) {
			$category = $this->category;
		}
		$query = "SELECT amount, SUM(amount) as sum, count(*) as total FROM $this->_tbl WHERE category='".$category."' AND type='".$type."' AND referenceid=".$referenceid;
		if ($uid) {
			$query .= " AND uid=".$uid;
		}
		$query .= " GROUP BY referenceid";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	
	}
	
	//-----------
	
	public function getAmount( $category=null, $type=null, $referenceid=null, $uid=null ) 
	{
		if ($referenceid == null) {
			$referenceid = $this->referenceid;
		}
		if ($referenceid == null) {
			return false;
		}
		if ($type == null) {
			$type = $this->type;
		}
		if ($category == null) {
			$category = $this->category;
		}
		
		$query = "SELECT amount FROM $this->_tbl WHERE category='".$category."' AND type='".$type."' AND referenceid=".$referenceid;
		if ($uid) {
			$query .= " AND uid=".$uid;
		}
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getTotals( $category=null, $type=null, $referenceid=null, $royalty=0, $action=null, $uid=null, $allusers = 0, $when=null, $calc=0 ) 
	{
		if ($referenceid == null) {
			$referenceid = $this->referenceid;
		}
		if ($type == null) {
			$type = $this->type;
		}
		if ($category == null) {
			$category = $this->category;
		}
		
		if ($uid == null) {
			$juser =& JFactory::getUser();
			$uid = $juser->get('id');
		}
		
		$query = "SELECT ";
		if ($calc==0) {
			$query .= " SUM(amount)";
		} else if ($calc==1) {
			// average
			$query .= " AVG(amount)";
		} else if ($calc==2) {
			// num of transactions
			$query .= " COUNT(*)";
		}
		$query .= " FROM $this->_tbl WHERE type='".$type."' ";
		if ($category) {
			$query .= " AND category='".$category."' ";
		}
		if ($referenceid) {
			$query .= "AND referenceid=".$referenceid;
		}
		if ($royalty) {
			$query .= " AND description like 'Royalty payment%' ";
		}
		if ($action=='asked') {
			$query .= " AND description like '%posting question%' ";
		} else if ($action=='answered') {
			$query .= " AND (description like '%answering question%' OR description like 'Answer for question%' OR description like 'Answered question%') ";
		} else if ($action=='misc') {
			$query .= " AND (description NOT LIKE '%posting question%' AND description NOT LIKE '%answering question%' 
			AND description NOT LIKE 'Answer for question%' AND description NOT LIKE 'Answered question%') ";
		}
		if (!$allusers) {
			$query .= " AND uid=$uid ";
		}
		if ($when) {
			$query .= " AND created LIKE '".$when."%' ";
		}
		
		$this->_db->setQuery( $query );
		/*$results = $this->_db->loadObjectList();
		
		$total = 0;
		if($results) {
			foreach($results as $result) {
			 $total = $total + $result->amount;
			}
		}
		*/
		//return $total;
		$total =  $this->_db->loadResult();
		return $total ? $total : 0;
	}
}

