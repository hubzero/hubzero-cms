<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
// Wish class
//----------------------------------------------------------

class Wish extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $wishlist       = NULL;  // @var int
	var $subject		= NULL;  // @var varchar(200)
	var $about			= NULL;  // @var text
	var $status			= NULL;  // @var int(11)
		// 0 new/pending
		// 1 granted
		// 2 deleted
		// 3 rejected
		// 4 withdrawn
		
	var $proposed    	= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $granted    	= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $proposed_by 	= NULL;  // @var int(50)
	var $granted_by 	= NULL;  // @var int(50)
	var $granted_vid 	= NULL;  // @var int(50)
	var $assigned 		= NULL;  // @var int(50)
	var $effort		    = NULL;  // @var int(3)
	var $due    	    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $anonymous		= NULL;  // @var int(3)
	var $ranking		= NULL;  // @var int(11)
	var $private		= NULL;  // @var int(11)
	var $accepted		= NULL;  // @var int(11) 
	var $points			= NULL;  // @var int(11) 
		// 1 admins accepted this wish
		// 2 wish author accepted solution
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist_item', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->subject ) == '') {
			$this->setError( JText::_('WISHLIST_ERROR_NO_SUBJECT') );
			return false;
		}
		
		if (trim( $this->wishlist ) == '') {
			$this->setError( JText::_('WISHLIST_ERROR_NO_LIST') );
			return false;
		}

		return true;
	}
	//----------
	 
	 public function get_votes_sum ($wishid, $what) {
	 	
		if ($wishid === NULL) {
			return false;
		}
		
	 	$sql = "SELECT SUM($what) FROM #__wishlist_vote WHERE wishid=".$wishid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
		
	 }
	 	 
	 //----------
	 
	 public function get_wishes ($listid, $filters, $admin, $juser=NULL) {
	
		if ($listid === NULL) {
			return false;
		}
		if (is_object($juser)) {
			$uid = $juser->get('id');
		}
		else {
			$uid = 0;
		}
		
		
		$sort = 'ws.status ASC, ws.proposed DESC';	
		// list  sorting
		switch ($filters['sortby']) 
			{
				case 'date':    	$sort = 'ws.status ASC, ws.proposed DESC';       
									break;
				case 'ranking':    	$sort = 'ws.status ASC, ranked, ws.ranking DESC, ws.proposed DESC';       
									break;
				case 'feedback':    $sort = 'positive DESC, ws.status ASC';       
									break;
				case 'bonus':    	$sort = 'ws.status ASC, bonus DESC, ws.proposed DESC';       
									break;
				default: 			$sort = 'ws.accepted DESC, ws.status ASC, ws.proposed DESC';
									break; 
		}
				
		$sql = "SELECT ws.*, v.helpful AS vote, m.importance AS myvote_imp, m.effort AS myvote_effort, m.due AS myvote_due,";
		if($uid) {
		$sql.= "\n (SELECT count(*) FROM #__wishlist_vote AS wv WHERE wv.wishid=ws.id AND wv.userid=".$uid.") AS ranked,";
		}
		else {
		$sql.= "\n NULL AS ranked,";
		} 
		$sql.= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=ws.id) AS positive, ";
		$sql.= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=ws.id) AS negative, ";
		
		$sql.= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS num_votes, ";
		$sql.= "\n (SELECT AVG(m.importance) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_imp, ";
		$sql.= "\n (SELECT AVG(m.effort) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_effort, ";	
		$sql.= "\n (SELECT m.due FROM #__wishlist_vote AS m WHERE m.wishid=ws.id ORDER BY m.due DESC LIMIT 1) AS average_due, ";	
		$sql.= "\n (SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonus, ";
		$sql.= "\n (SELECT COUNT(DISTINCT uid) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonusgivenby ";		
		
		$sql.= "\n FROM #__wishlist_item AS ws";
		$sql.= "\n LEFT JOIN #__vote_log AS v ON v.referenceid=ws.id AND v.category='wish' AND v.voter='".$uid."' ";
		$sql.= "\n LEFT JOIN #__wishlist_vote AS m ON m.wishid=ws.id AND m.userid='".$uid."' ";
		$sql.="\n WHERE ws.wishlist='".$listid."'";
		// list  filtering
		switch ($filters['filterby']) 
			{
				case 'all':    		$sql.= ' AND ws.status!=2';       
									break;
				case 'granted':    	$sql.= ' AND ws.status=1';       
									break;
				case 'open':    	$sql.= ' AND ws.status=0';       
									break;								
				case 'accepted':    $sql.= ' AND ws.accepted=1 AND ws.status=0';       
									break;
				case 'pending':     $sql.= ' AND ws.accepted=0 AND ws.status=0';       
									break;
				case 'rejected':    $sql.= ' AND ws.status=3';       
									break;
				case 'withdrawn':   $sql.= ' AND ws.status=4';       
									break;
				case 'deleted':     $sql.= ' AND ws.status=2';       
									break;
				case 'useraccepted':$sql.= ' AND ws.accepted=3 AND ws.status!=2';       
									break;
				case 'private':    	$sql.= ' AND ws.status!=2 AND ws.private=1';       
									break;
				case 'public':    	$sql.= ' AND ws.status!=2 AND ws.private=0';       
									break;
				case 'mine':    	if($uid) {
									$sql.= ' AND ws.assigned="'.$uid.'" AND ws.status!=2';
									}       
									break;
				case 'assigned':    $sql.= ' AND ws.assigned NOT NULL AND ws.status!=2';       
									break;
				default: 			$sql .= ' AND ws.status!=2';
									break; 
		}
		// do not show private wishes
		if(!$admin) {
		$sql.="\n AND ws.private='0'";
		}
		
		$sql.= " ORDER BY ". $sort;
		
		if(isset ($filters['limit']) && $filters['limit']!=0) {
		$sql.= " LIMIT ". $filters['limit'];
		} 
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	 }
	 
	 //----------
	 
	 public function delete_wish ($wishid, $withdraw=0) {
	 	
		if ($wishid === NULL) {
			$wishid == $this->id;
		}
		if ($wishid === NULL) {
			return false;
		}
		$status = $withdraw ? 4 : 2;
		
		$query  = "UPDATE $this->_tbl SET status='".$status."', ranking='0' WHERE id=".$wishid;		
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	 
	 }
	 
	 //----------
	 
	 public function get_wish ($wishid, $juser='', $deleted=0) {
	 
	 	if ($wishid === NULL) {
			return false;
		}
		if (is_object($juser)) {
			$uid = $juser->get('id');
		}
		else {
			$uid = 0;
		}
	
		$sql = "SELECT ws.*, v.helpful AS vote, m.importance AS myvote_imp, m.effort AS myvote_effort, m.due AS myvote_due,";
		if($uid) {
		$sql.= "\n (SELECT count(*) FROM #__wishlist_vote AS wv WHERE wv.wishid=ws.id AND wv.userid=".$uid.") AS ranked,";
		} 
		$sql.= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=ws.id) AS positive, ";
		$sql.= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=ws.id) AS negative, ";
		
		$sql.= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS num_votes, ";
		$sql.= "\n (SELECT AVG(m.importance) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_imp, ";
		$sql.= "\n (SELECT AVG(m.effort) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_effort, ";	
		$sql.= "\n (SELECT m.due FROM #__wishlist_vote AS m WHERE m.wishid=ws.id ORDER BY m.due DESC LIMIT 1) AS average_due, ";
		$sql.= "\n (SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonus, ";
		$sql.= "\n (SELECT COUNT(DISTINCT uid) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonusgivenby ";	
			
		$sql.= "\n FROM #__wishlist_item AS ws";
		$sql.= "\n LEFT JOIN #__vote_log AS v ON v.referenceid=ws.id AND v.category='wish' AND v.voter='".$uid."' ";
		$sql.= "\n LEFT JOIN #__wishlist_vote AS m ON m.wishid=ws.id AND m.userid='".$uid."' ";
		$sql.= "\n WHERE ws.id='".$wishid."' ";
		if(!$deleted) {
		$sql.=" AND ws.status!=2";
		}
	
		$this->_db->setQuery( $sql );
		$res = $this->_db->loadObjectList();
		$wish = ($res) ? $res[0] : array();
		
		
		return $wish;
	 }
	 //----------
	 // Does the wish exist on this list?
	 public function check_wish ($wishid, $listid) {
	 	
		if ($wishid === NULL or $listid === NULL) {
			return false;
		}
		
		$query  = "SELECT id ";
		$query .= "FROM #__wishlist_item  ";
		$query .= "WHERE id = '".$wishid."' AND wishlist='".$listid."' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	 
	 }
	 
	 //----------
	 
	 public function getWishID ($which, $id, $listid, $admin) {
	 	
		if ($which === NULL or $id === NULL or $listid === NULL) {
			return false;
		}
		
		$query  = "SELECT ws.id ";
		$query .= "FROM #__wishlist_item AS ws ";
		if($which == 'prev') {
		$query .= "WHERE ws.id < '".$id."' AND ws.wishlist='".$listid."' AND ws.status!=2";
		if(!$admin) {
		$query.="\n AND ws.private='0'";
		}
		$query .= " ORDER BY ws.id DESC ";
		}
		else if ($which == 'next') {
		$query .= "WHERE ws.id > '".$id."' AND ws.wishlist='".$listid."' AND ws.status!=2";
		if(!$admin) {
		$query.="\n AND ws.private='0'";
		}
		$query .= " ORDER BY ws.id ASC ";
		}
		$query .= "LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	 	
	 }
	 
	 //----------
	 
	 public function get_vote ($refid, $category= 'wish', $uid) {
	 	
		if ($refid === NULL or $uid === NULL) {
			return false;
		}
		
		$query  = "SELECT v.helpful ";
		$query .= "FROM #__vote_log as v  ";
		$query .= "WHERE v.referenceid = '".$refid."' AND v.category='".$category."' AND v.voter='".$uid."' LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	 }
	
	
}
//----------------------------------------------------------
// Wishlist Implementation Plan class
//----------------------------------------------------------
class WishlistPlan extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $wishid		= NULL;  // @var int(11)
	var $version	= NULL;  // @var int(11)
	var $created	= NULL;
	var $created_by	= NULL;
	var $minor_edit	= NULL;
	var $pagetext	= NULL;
	var $pagehtml	= NULL;
	var $approved   = NULL;
	var $summary	= NULL;
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist_implementation', 'id', $db );
	}
	
	//-----------
	
	public function getPlan($wishid)
	{
		if($wishid == NULL) {
		 return false;
		}
		
		$query  = "SELECT * ";
		$query .= "FROM #__wishlist_implementation  ";
		$query .= "WHERE wishid = '".$wishid."' ORDER BY created DESC LIMIT 1";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function load( $oid=NULL ) 
	{
		if ($oid == NULL or !is_numeric($oid)) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE id='$oid'" );
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function deletePlan($wishid)
	{
		if($wishid == NULL) {
		 return false;
		}
		
		$query = "DELETE FROM $this->_tbl WHERE wishid='". $wishid."'";
		$this->_db->setQuery( $query );
		$this->_db->query();
		
	}
	
	
	
}

//----------------------------------------------------------
// Wishlist Group class
//----------------------------------------------------------
class WishlistOwnerGroup extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $wishlist	= NULL;  // @var int(11)
	var $groupid	= NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist_ownergroups', 'id', $db );
	}
	
	 
	//----------
	 public function get_owner_groups($listid, $controlgroup='', $wishlist='', $native=0, $groups = array()) {
		
		if ($listid === NULL) {
			return false;
		}
		
		$wishgroups = array();
		ximport('xgroup');
		
		$xgroup = new XGroup();
		$obj = new Wishlist( $this->_db );
		
		// if tool, get tool group
		if(!$wishlist) {
		$wishlist = $obj->get_wishlist($listid);
		}
		if(isset($wishlist->resource) && $wishlist->resource->type=='7') {
			$toolgroup = $obj->getToolDevGroup ($wishlist->referenceid);
			if($toolgroup) { $groups[] = $toolgroup; }
		}
		
		
		// if primary list, add all site admins
		if($controlgroup && XGroupHelper::groups_exists($controlgroup) && $wishlist->category=='general') {
			$instance = new XGroup($controlgroup);
			$groups[] = $instance->get('gidNumber');
		}
		
		// if private group list, add the group
		if($wishlist->category == 'group') {
			$groups[] = $wishlist->referenceid;
		}
	
		
		// get groups assigned to this wishlist
		if(!$native) {
			$sql = "SELECT o.groupid"
				. "\n FROM #__wishlist_ownergroups AS o "
				. "\n WHERE o.wishlist='".$listid."'";
	
			$this->_db->setQuery( $sql );
			$wishgroups = $this->_db->loadObjectList();
			
			if($wishgroups) {
				foreach ($wishgroups as $wg) {
					if(XGroupHelper::groups_exists($wg->groupid)) {
						$groups[]=$wg->groupid;
					}				
				}
			}
		}
		
		$groups = array_unique($groups);
		sort($groups);
		return $groups;
	 
	 }
	 
	 //----------
	 public function delete_owner_group($listid, $groupid, $admingroup) {

		if ($listid === NULL or $groupid === NULL) {
			return false;
		}
		
		$nativegroups = $this->get_owner_groups($listid, $admingroup, '', 1);
		ximport('xgroup');	
		
		// cannot delete "native" owners (e.g. tool dev group)
		if(XGroupHelper::groups_exists($groupid) && !in_array($groupid, $nativegroups, true)) {
				
				$query = "DELETE FROM $this->_tbl WHERE wishlist='". $listid."' AND groupid='".$groupid."'";
				$this->_db->setQuery( $query );
				$this->_db->query();
		}
		
		
	}
	//----------
	 public function save_owner_groups($listid, $admingroup, $newgroups = array()) {

		if ($listid === NULL) {
			return false;
		}
		
		$groups = $this->get_owner_groups($listid, $admingroup);
		ximport('xgroup');	
			
		if(count($newgroups) > 0)  {
			foreach($newgroups as $ng) {
				
					$instance = new XGroup($ng);
					$gid = $instance->get('gidNumber');
					if ($gid && !in_array($gid, $groups, true)) {
						
						$this->id = 0;
						$this->groupid = $gid;
						$this->wishlist = $listid;
						
						if (!$this->store()) {
						$this->setError( JText::_('Failed to add a user.') );
						return false;
						}
					}
			
			}
		}
		
	}
	
}

//----------------------------------------------------------
// Wishlist Owner  class
//----------------------------------------------------------
class WishlistOwner extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $wishlist	= NULL;  // @var int(11)
	var $userid		= NULL;  // @var int(11)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist_owners', 'id', $db );
	}
	
	//----------
	 public function delete_owner($listid, $uid, $admingroup) {

		if ($listid === NULL or $uid === NULL) {
			return false;
		}
		
		$nativeowners = $this->get_owners($listid, $admingroup, 1);
		
		$xuser =& XUser::getInstance( $uid );
		
		// cannot delete "native" owner (e.g. resource contributor)
		if(is_object($xuser) && !in_array($xuser->get('uid'), $nativeowners, true)) {
				
				$query = "DELETE FROM $this->_tbl WHERE wishlist='". $listid."' AND userid='".$uid."'";
				$this->_db->setQuery( $query );
				$this->_db->query();
		}
		
		
	}
	
	//----------
	 public function save_owners($listid, $admingroup, $newowners = array()) {

		if ($listid === NULL) {
			return false;
		}
		
		$owners = $this->get_owners($listid, $admingroup);
	
		
		if(count($newowners) > 0)  {
			foreach($newowners as $no) {
				
					$xuser =& XUser::getInstance( $no );
					if (is_object($xuser) && !in_array($xuser->get('uid'), $owners, true)) {
						
						$this->id = 0;
						$this->userid = $xuser->get('uid');
						$this->wishlist = $listid;
						
						if (!$this->store()) {
						$this->setError( JText::_('Failed to add a user.') );
						return false;
						}
					}
			
			}
		}
		
	}
	
	
	//----------
	 public function get_owners($listid, $admingroup, $wishlist='', $native=0, $wishid=0, $owners = array()) {

		if ($listid === NULL) {
			return false;
		}
		
		$obj = new Wishlist( $this->_db );
		$objG = new WishlistOwnerGroup( $this->_db );
		if(!$wishlist) {	
		$wishlist = $obj->get_wishlist($listid);
		}
		
		// if private user list, add the user
		if($wishlist->category == 'user') {
			$owners[] = $wishlist->referenceid;
		}
	
			
		// if resource, get contributors
		if($wishlist->category=='resource' &&  $wishlist->resource->type!='7') {
			$cons = $obj->getCons($wishlist->referenceid);
			if($cons) {
				foreach($cons as $con) {
					$xuser =& XUser::getInstance( $con->id );
					if (is_object($xuser)) {
						$owners[] = $xuser->get('uid');
					}
					
					
				}
			}
		}
		
		
		// get groups		
		$groups = $objG->get_owner_groups($listid, $admingroup, $wishlist, $native);
		if($groups) {
			foreach($groups as $g) {
				// Load the group
				$group = new XGroup();
				$group->select( $g);
				$members = $group->get('members');
				$managers = $group->get('managers');
				if($wishlist->category=='group') {
				$managers = $group->get('invitees');
				}
				$members = array_merge($members, $managers);
				if($members) {
					foreach($members as $member) {
						$muser =& XUser::getInstance( $member );
						if (is_object($muser)) {
							$owners[] = $member;
						}
					}
				}
			}
		}
		
		// get individuals
		if(!$native) {
			$sql = "SELECT o.userid"
				. "\n FROM #__wishlist_owners AS o "
				. "\n WHERE o.wishlist='".$listid."'";
	
			$this->_db->setQuery( $sql );
			$results =  $this->_db->loadObjectList();
			if($results) {
				foreach($results as $result) {
					$wuser =& XUser::getInstance( $result->userid );
					if (is_object($wuser)) {
						$owners[] = $wuser->get('uid');
					}
					
					
				}
			}
		}
		
		$owners = array_unique($owners);
		sort($owners);
		
		if($wishid) {
			$activeowners = array();
			$query  = "SELECT v.userid ";
			$query .= "FROM #__wishlist_vote AS v LEFT JOIN #__wishlist_item AS i ON v.wishid = i.id ";
			$query .= "WHERE i.wishlist = '".$listid."' AND v.wishid='".$wishid."' AND (v.userid IN (";
			$tquery = '';
			foreach ($owners as $o)
			{
				$tquery .= "'".$o."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.")) ";
			
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			if($result) {
				foreach($result as $r) {
					$activeowners[] = $r->userid;
				}
				
				$owners = $activeowners;
			}
		}
		
		$collect = array();
		$collect['individuals'] = $owners;
		$collect['groups'] = $groups;
		
		return $collect;
	 
	 }
		
	
}
//----------------------------------------------------------
// Wish Ranking class
//----------------------------------------------------------
class WishRank extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $wishid      	= NULL;  // @var int
	var $userid 		= NULL;  // @var int
	var $voted    	    = NULL;  // @var datetime (0000-00-00 00:00:00)
	var $importance     = NULL;  // @var int(3)
	var $effort		    = NULL;  // @var int(3)
	var $due    	    = NULL;  // @var datetime (0000-00-00 00:00:00)
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist_vote', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->wishid ) == '') {
			$this->setError( JText::_('WISHLIST_ERROR_NO_WISHID') );
			return false;
		}

		return true;
	}
	
	//--------------
	function load_vote( $oid=NULL, $wishid=NULL ) 
	{
		if ($oid === NULL) {
			$oid = $this->userid;
		}
		if ($wishid === NULL) {
			$wishid = $this->wishid;
		}
		
		if($oid === NULL or $wishid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM #__wishlist_vote WHERE userid='$oid' AND wishid='$wishid'");
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	//--------------
	function get_votes( $wishid=NULL ) 
	{
		
		if ($wishid === NULL) {
			$wishid = $this->wishid;
		}
		
		if($wishid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM #__wishlist_vote WHERE wishid='$wishid'");
		return  $this->_db->loadObjectList();
	}
	//--------------
	function remove_vote( $wishid=NULL, $oid=NULL ) 
	{
		if ($oid === NULL) {
			$oid = $this->userid;
		}
		if ($wishid === NULL) {
			$wishid = $this->wishid;
		}
		
		if($wishid === NULL) {
			return false;
		}
		
		$query = "DELETE FROM #__wishlist_vote WHERE wishid='$wishid'";
		if($oid) {
		$query .= " AND userid=".$oid;
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return true;
	
	}

}
//----------------------------------------------------------
// Wishlist  class
//----------------------------------------------------------
class Wishlist extends JTable
{
	var $id         	= NULL;  // @var int(11) Primary key
	var $category       = NULL;  // @var varchar(50)
	var $referenceid	= NULL;  // @var int(11)
	var $description	= NULL;  // @var text
	var $title			= NULL;  // @var varchar(150)
	var $created    	= NULL;  // @var datetime (0000-00-00 00:00:00)
	var $created_by 	= NULL;  // @var int(11)
	var $state     		= NULL;  // @var int(3)
	var $public			= NULL;  // @var int(3)  // can any user view and submit to it?
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__wishlist', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->title ) == '') {
			$this->setError( JText::_('Missing title for the wish list') );
			return false;
		}

		return true;
	}
	//------------
	public function get_wishlistID($rid=0, $cat='resource')
	{
		if ($rid === NULL) {
			$rid = $this->referenceid;
		}
		if ($rid === NULL) {
			return false;
		}
		
		// get individuals
		$sql = "SELECT id"
			. "\n FROM $this->_tbl "
			. "\n WHERE referenceid='".$rid."' AND category='".$cat."' ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery( $sql );
		return  $this->_db->loadResult();
		
	}
	//------------
	public function createlist ($category='resource', $refid, $public=1, $title='', $description='')
	{
		if ($refid === NULL) {
			return false;
		}
		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$juser =& JFactory::getUser();
				
		$this->created = date( 'Y-m-d H:i:s' );
		$this->category = $category;
		$this->created_by = $juser->get('id');
		$this->referenceid = $refid;
		$this->description = $description;
		$this->public = $public;
	
		switch( $category ) 
		{
			case 'general':    
			
					$this->title = $title ? $title : $hubShortName;
					
					if (!$this->store()) {
						$this->_error = $this->getError();
						return false;
					}
					else {
						// Checkin wishlist
						$this->checkin();
					}
			
					return $this->id;
			
			break;
			
			case 'resource':    
			
				// resources can only have one list
				if(!$this->get_wishlist('',$refid, 'resource')) {
						
					$this->title = $title ? $title :'Resource #'.$rid;
					
					if (!$this->store()) {
						$this->_error = $this->getError();
						return false;
					}
					else {
						// Checkin wishlist
						$this->checkin();
					}
			
					return $this->id;
				}
				else {
					return $this->get_wishlistID($refid); // return existing id
				}
			
			break;
			case 'group':    
			
				$this->title = $title ? $title :'Group #'.$rid;
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				}
				else {
					// Checkin wishlist
					$this->checkin();
				}
			
				return $this->id;
			
			break;
			case 'user':    
			
				$this->title = $title;
				if (!$this->store()) {
					$this->_error = $this->getError();
					return false;
				}
				else {
					// Checkin wishlist
					$this->checkin();
				}
			
				return $this->id;
			
			break;
			
		} 
				
		return 0;	
		
	}
	
	//------------
	public function getTitle ($id)
	{
		
		if ($id === NULL) {
			return false;
		}
		$sql = "SELECT w.title "
				. "\n FROM $this->_tbl AS w";
		$sql .=	"\n WHERE w.id=".$id;
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
		
	
	}
	
	//------------
	public function is_primary ($id)
	{
		
		if ($id === NULL) {
			return false;
		}
		$sql = "SELECT w.* "
				. "\n FROM $this->_tbl AS w";
		$sql .=	"\n WHERE w.id=".$id." AND w.referenceid=1 AND w.category='general'";
		
		$this->_db->setQuery( $sql );
		$bingo = $this->_db->loadResult();
		if($bingo) {
		 return true;
		}
		else {
			return false;
		}
	
	}
	//------------
	public function get_wishlist($id='', $refid=0, $cat='', $primary = 0, $getversions=0)
	{
		
		if($id===NULL && $refid===0 && $cat===NULL) {
			return false;
		}
		
		$sql = "SELECT w.*";
		//if($cat == 'resource') {
			//$sql .= "\n , r.title as resourcetitle, r.type as resourcetype, r.alias, r.introtext";
		//}
			$sql .= "\n FROM $this->_tbl AS w";
		//if($cat == 'resource') {
			//$sql .= "\n JOIN #__resources AS r ON r.id=w.referenceid";	
		//}
		if($id) {
			$sql .=	"\n WHERE w.id=".$id;
		}
		else if($refid && $cat) {
			$sql .=	"\n WHERE w.referenceid=".$refid." AND w.category='".$cat."'";
		}
		else if($primary) {
			$sql .=	"\n WHERE w.referenceid=1 AND w.category='general'";
		}
			
		$this->_db->setQuery( $sql );
		$res = $this->_db->loadObjectList();
		$wishlist = ($res) ? $res[0] : array();
		
		// get parent 
		//$parent = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
		
		if(count($wishlist) > 0 && $wishlist->category=='resource') {
			$wishlist->resource = $this->get_wishlist_parent($wishlist->referenceid, $wishlist->category);
			// Currenty for tools only
			if($getversions && $wishlist->resource && isset($wishlist->resource->type) && $wishlist->resource->type==7) {
			$wishlist->resource->versions = $this->get_parent_versions($wishlist->referenceid, $wishlist->resource->type );
			}
		}
		
		return $wishlist;
	}
	//-----------

	public function get_parent_versions($rid, $type)
	{
				
		$versions = array();
		// currently for tools only
		if($type == 7) {
				$query = "SELECT v.id FROM #__tool_version as v JOIN #__resources as r ON r.alias = v.toolname WHERE r.id='".$rid."'";
				$query.= " AND v.state=3 ";
				$query.= " OR v.state!=3 ORDER BY state DESC, revision DESC LIMIT 3";
				$this->_db->setQuery( $query );
				$result  = $this->_db->loadObjectList();
				$versions = $result ? $result : array();
				
		}
		
		return $versions;
	}
	//-----------

	public function get_wishlist_parent($refid, $cat='resource')
	{
				
		$resource = array();
		if($cat == 'resource') {
				$sql = "SELECT r.title, r.type, r.alias, r.introtext, t.type as typetitle"
				. "\n FROM #__resources AS r"
				. "\n LEFT JOIN #__resource_types AS t ON t.id=r.type "
				. "\n WHERE r.id='".$refid."'";
				$this->_db->setQuery( $sql );
				$res  = $this->_db->loadObjectList();
				$resource = ($res) ? $res[0]: array();
		}
		
		return $resource;
	}
	
	//---------
	function getCons($refid) 
	{
		$sql = "SELECT n.uidNumber AS id"
			 . "\n FROM #__xprofiles AS n"
			 . "\n JOIN #__author_assoc AS a ON n.uidNumber=a.authorid"
			 . "\n WHERE a.subtable = 'resources'"
			 . "\n AND a.subid=". $refid;
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getToolDevGroup($refid, $groups = array())
	{
		$query  = "SELECT g.cn FROM #__tool_groups AS g ";
		$query .= "JOIN #__xgroups AS xg ON g.cn=xg.cn ";
		$query .= " JOIN #__tool AS t ON g.toolid=t.id ";
		$query .= " JOIN #__resources as r ON r.alias = t.toolname";
		$query .= " WHERE r.id = '".$refid."' AND g.role=1 ";
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
		
	}
	 
	
	
}

?>