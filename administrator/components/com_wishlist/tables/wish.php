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

	public function get_votes_sum($wishid, $what) 
	{
		if ($wishid === NULL) {
			return false;
		}
		
	 	$sql = "SELECT SUM($what) FROM #__wishlist_vote WHERE wishid=".$wishid;
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	
	//----------
	
	public function get_count($listid, $filters, $admin, $juser=NULL) 
	{
		if ($listid === NULL) {
			return false;
		}
		if (is_object($juser)) {
			$uid = $juser->get('id');
		} else {
			$uid = 0;
		}
		
		$sql = "SELECT ws.id FROM #__wishlist_item AS ws ";
		
		if ($filters['tag']) {
			$sql.= "\n LEFT JOIN #__tags_object AS RTA ON RTA.objectid=ws.id AND RTA.tbl='wishlist' ";
			$sql.= "\n LEFT JOIN #__tags AS TA ON RTA.tagid=TA.id ";
		}
		
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
				case 'mine':    	if ($uid) {
										$sql.= ' AND ws.assigned="'.$uid.'" AND ws.status!=2';
									}       
									break;
				case 'submitter':   if ($uid) {
										$sql.= ' AND ws.proposed_by='.$uid.' AND ws.status!=2';
									}
									break;
				case 'assigned':    $sql.= ' AND ws.assigned NOT NULL AND ws.status!=2';       
									break;
				default: 			$sql .= ' AND ws.status!=2';
									break; 
		}
		
		// do not show private wishes
		if (!$admin) {
			$sql.="\n AND ws.private='0'";
		}
		
		if ($filters['tag']) {
			$tagging = new WishTags( $this->_db );
			$tags = $tagging->_parse_tags($filters['tag']);

			$sql .= " AND (RTA.objectid=ws.id AND (RTA.tbl='wishlist') AND (TA.tag IN (";
			$tquery = '';
			foreach ($tags as $tagg)
			{
				$tquery .= "'".$tagg."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$sql .= $tquery.") OR TA.raw_tag IN (".$tquery;
			$sql .= ")))";			
			$sql .= " GROUP BY ws.id ";
		}
		
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		
		return count($result);
	}
		 
	//----------
	
	public function get_wishes($listid, $filters, $admin, $juser=NULL, $fullinfo = 1) 
	{
		if ($listid === NULL) {
			return false;
		}
		if (is_object($juser)) {
			$uid = $juser->get('id');
		} else {
			$uid = 0;
		}
		
		$filters['tag'] = isset($filters['tag']) ? $filters['tag'] : '';
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_wishlist'.DS.'helpers'.DS.'tags.php' );
				
		$sort = 'ws.status ASC, ws.proposed DESC';	
		// list  sorting
		switch ($filters['sortby']) 
		{
				case 'date':    		$sort = 'ws.status ASC, ws.proposed DESC';       
										break;
				case 'ranking':    		$sort = 'ws.status ASC, ranked, ws.ranking DESC, positive DESC, ws.proposed DESC';       
										break;
				case 'feedback':    	$sort = 'positive DESC, ws.status ASC';       
										break;
				case 'bonus':    		$sort = 'ws.status ASC, bonus DESC, positive DESC, ws.ranking DESC, ws.proposed DESC';       
										break;
				case 'latestcomment':   $sort = 'latestcomment DESC, ws.status ASC';       
										break;
				default: 				$sort = 'ws.accepted DESC, ws.status ASC, ws.proposed DESC';
										break; 
		}
				
		$sql = $fullinfo 
				? "SELECT ws.*, v.helpful AS vote, m.importance AS myvote_imp, m.effort AS myvote_effort, xp.name AS authorname, " 
				: "SELECT ws.id, ws.wishlist, ws.proposed, ws.granted, ws.granted_vid, ws.status ";
			
		if ($fullinfo) {
			if ($uid) {
				$sql .= "\n (SELECT count(*) FROM #__wishlist_vote AS wv WHERE wv.wishid=ws.id AND wv.userid=".$uid.") AS ranked,";
			} else {
				$sql .= "\n NULL AS ranked,";
			} 
			// Get votes
			$sql .= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=ws.id) AS positive, ";
			$sql .= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=ws.id) AS negative, ";
			$sql .= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS num_votes, ";
			
			if ($filters['sortby'] == 'latestcomment') {
				$sql .= "\n (SELECT MAX(CC.added) FROM #__comments AS CC WHERE CC.referenceid=ws.id AND (CC.category='wish' OR CC.category='wishcomment')  GROUP BY CC.referenceid) AS latestcomment, ";
			}
			
			// Get xprofile info
			$sql .= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.granted_by ) as grantedby, ";
			$sql .= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.assigned ) as assignedto, ";
			
			// Get comments count
			$sql .= "\n (SELECT count(*) FROM #__comments AS CC WHERE CC.referenceid=ws.id AND CC.state=0 AND CC.category='wish') AS comments, ";
			$sql .= "\n (SELECT count(*) FROM #__comments AS CC JOIN #__comments AS C2 ON C2.id=CC.referenceid AND C2.category='wish' WHERE CC.state=0 AND CC.category='wishcomment' AND C2.referenceid=ws.id) AS commentreplies, ";
			$sql .= "\n (SELECT count(*) FROM #__comments AS CC JOIN #__comments AS C2 ON C2.id=CC.referenceid AND C2.category='wishcomment' JOIN #__comments AS C3 ON C3.id=C2.referenceid AND C3.category='wish'  WHERE CC.state=0 AND CC.category='wishcomment' AND C3.referenceid=ws.id) AS replyreplies, ";
			$sql .= "\n (SELECT comments + commentreplies + replyreplies) AS numreplies, ";
			
			// Get abouse reports count
			$sql .= "\n (SELECT count(*) FROM #__abuse_reports AS RR WHERE RR.referenceid=ws.id AND RR.state=0 AND RR.category='wish') AS reports, ";
			
			// Get averages
			$sql .= "\n (SELECT AVG(m.importance) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_imp, ";
			$sql .= "\n (SELECT AVG(m.effort) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id AND m.effort!=6) AS average_effort, ";	
			
			// Get bonus
			$sql .= "\n (SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonus, ";
			$sql .= "\n (SELECT COUNT(DISTINCT uid) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonusgivenby ";
		}		
		$sql .= "\n FROM #__wishlist_item AS ws";
		
		if ($fullinfo) {			
			$sql .= "\n JOIN #__xprofiles AS xp ON xp.uidNumber=ws.proposed_by ";
			$sql .= "\n LEFT JOIN #__vote_log AS v ON v.referenceid=ws.id AND v.category='wish' AND v.voter='".$uid."' ";
			$sql .= "\n LEFT JOIN #__wishlist_vote AS m ON m.wishid=ws.id AND m.userid='".$uid."' ";
			if ($filters['tag']) {
				$sql .= "\n JOIN #__tags_object AS RTA ON RTA.objectid=ws.id AND RTA.tbl='wishlist' ";
				$sql .= "\n INNER JOIN #__tags AS TA ON RTA.tagid=TA.id ";
			}
		}
		
		$sql .= "\n WHERE ws.wishlist='".$listid."'";
		$sql .= "\n AND 1=1 ";
		
		if (!$fullinfo && isset($filters['timelimit'])) {
			$sql.="\n OR (ws.status= 1 AND ws.granted > '".$filters['timelimit']."') ";
		}
		
		if (!$fullinfo && isset($filters['versionid'])) {
			$sql.="\n OR (ws.granted_vid = '".$filters['versionid']."') ";
		}
		
		if ($fullinfo) {
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
					case 'mine':    	if ($uid) {
											$sql.= ' AND ws.assigned="'.$uid.'" AND ws.status!=2';
										}       
										break;
					case 'submitter':   if ($uid) {
											$sql.= ' AND ws.proposed_by='.$uid.' AND ws.status!=2';
										}
										break;
					case 'assigned':    $sql.= ' AND ws.assigned NOT NULL AND ws.status!=2';       
										break;
					default: 			$sql .= ' AND ws.status!=2';
										break; 
			}
		}
		
		// do not show private wishes
		if (!$admin) {
			$sql.="\n AND ws.private='0'";
		}
		
		if ($fullinfo && $filters['tag']) {
			$tagging = new WishTags( $this->_db );
			$tags = $tagging->_parse_tags($filters['tag']);

			$sql .= " AND (RTA.objectid=ws.id AND (RTA.tbl='wishlist') AND (TA.tag IN (";
			$tquery = '';
			foreach ($tags as $tagg)
			{
				$tquery .= "'".$tagg."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$sql .= $tquery.") OR TA.raw_tag IN (".$tquery;
			$sql .= ")))";			
			$sql .= " GROUP BY ws.id ";
	
		}
		
		$sql.= "\n ORDER BY ". $sort;
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : ""; 
	
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//----------
	
	public function delete_wish ($wishid, $withdraw=0) 
	{
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
	
	public function get_wish ($wishid = 0, $uid = 0, $refid = 0, $cat = '', $deleted = 0) 
	{
	 	if ($wishid === NULL) {
			return false;
		}
	
		$sql = "SELECT ws.*, v.helpful AS vote, m.importance AS myvote_imp, m.effort AS myvote_effort, xp.name AS authorname, ";
		if ($uid) {
			$sql.= "\n (SELECT count(*) FROM #__wishlist_vote AS wv WHERE wv.wishid=ws.id AND wv.userid=".$uid.") AS ranked,";
		} 
		$sql.= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=ws.id) AS positive, ";
		$sql.= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=ws.id) AS negative, ";
		
		// Get xprofile info
		$sql.= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.granted_by ) as grantedby, ";
		$sql.= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.assigned ) as assignedto, ";
			
		// Get comments count
		$sql.= "\n (SELECT count(*) FROM #__comments AS CC WHERE CC.referenceid=ws.id AND CC.state=0 AND CC.category='wish') AS comments, ";
		$sql.= "\n (SELECT count(*) FROM #__comments AS CC JOIN #__comments AS C2 ON C2.id=CC.referenceid AND C2.category='wish' WHERE CC.state=0 AND CC.category='wishcomment' AND C2.referenceid=ws.id) AS commentreplies, ";
		$sql.= "\n (SELECT count(*) FROM #__comments AS CC JOIN #__comments AS C2 ON C2.id=CC.referenceid AND C2.category='wishcomment' JOIN #__comments AS C3 ON C3.id=C2.referenceid AND C3.category='wish'  WHERE CC.state=0 AND CC.category='wishcomment' AND C3.referenceid=ws.id) AS replyreplies, ";
		$sql.= "\n (SELECT comments + commentreplies + replyreplies) AS numreplies, ";
		
		// Get abouse reports count
		$sql.= "\n (SELECT count(*) FROM #__abuse_reports AS RR WHERE RR.referenceid=ws.id AND RR.state=0 AND RR.category='wish') AS reports, ";
		
		$sql.= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS num_votes, ";
		$sql.= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id AND m.effort=6) AS num_skipped_votes, "; // did anyone skip effort selection?
		$sql.= "\n (SELECT AVG(m.importance) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_imp, ";
		$sql.= "\n (SELECT AVG(m.effort) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id AND m.effort!=6) AS average_effort, ";	
		$sql.= "\n (SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonus, ";
		$sql.= "\n (SELECT COUNT(DISTINCT uid) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonusgivenby ";	
			
		$sql.= "\n FROM #__wishlist_item AS ws";
		if ($refid && $cat) {
			$sql.= "\n JOIN #__wishlist AS W ON W.id=ws.wishlist AND W.referenceid='$refid' AND W.category='$cat' ";
		}
		$sql.= "\n JOIN #__xprofiles AS xp ON xp.uidNumber=ws.proposed_by ";
		$sql.= "\n LEFT JOIN #__vote_log AS v ON v.referenceid=ws.id AND v.category='wish' AND v.voter='".$uid."' ";
		$sql.= "\n LEFT JOIN #__wishlist_vote AS m ON m.wishid=ws.id AND m.userid='".$uid."' ";
		$sql.= "\n WHERE ws.id='".$wishid."' ";
		if (!$deleted) {
			$sql.=" AND ws.status!=2";
		}
	
		$this->_db->setQuery( $sql );
		$res = $this->_db->loadObjectList();
		$wish = ($res) ? $res[0] : array();		
		
		return $wish;
	}
	 
	//----------
	// Does the wish exist on this list?
	public function check_wish($wishid, $listid) 
	{
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
	 
	public function getWishID($which, $id, $listid, $admin, $uid, $filters=array()) 
	{
		if ($which === NULL or $id === NULL or $listid === NULL) {
			return false;
		}
		
		$query  = "SELECT ws.id ";
		$query .= "FROM #__wishlist_item AS ws ";
		if (isset($filters['tag']) && $filters['tag']!='') {
			$query.= "\n JOIN #__tags_object AS RTA ON RTA.objectid=ws.id AND RTA.tbl='wishlist' ";
			$query.= "\n INNER JOIN #__tags AS TA ON RTA.tagid=TA.id ";
		}
		$query .= "WHERE ws.wishlist='".$listid."' AND ";
		$query .= ($which == 'prev')  ? "ws.id < '".$id."' " : "ws.id > '".$id."'";

		if (isset($filters['filterby'])) {
			switch ($filters['filterby']) 
			{
				case 'all':    		$query .= ' AND ws.status!=2';       
									break;
				case 'granted':    	$query.= ' AND ws.status=1';       
									break;
				case 'open':    	$query.= ' AND ws.status=0';       
									break;								
				case 'accepted':    $query.= ' AND ws.accepted=1 AND ws.status=0';       
									break;
				case 'pending':     $query.= ' AND ws.accepted=0 AND ws.status=0';       
									break;
				case 'rejected':    $query.= ' AND ws.status=3';       
									break;
				case 'withdrawn':   $query.= ' AND ws.status=4';       
									break;
				case 'deleted':     $query.= ' AND ws.status=2';       
									break;
				case 'useraccepted':$query.= ' AND ws.accepted=3 AND ws.status!=2';       
									break;
				case 'private':    	$query.= ' AND ws.status!=2 AND ws.private=1';       
									break;
				case 'public':    	$query.= ' AND ws.status!=2 AND ws.private=0';       
									break;
				case 'mine':    	if ($uid) {
										$query.= ' AND ws.assigned="'.$uid.'" AND ws.status!=2';
									}       
									break;
				case 'assigned':    $query .= ' AND ws.assigned NOT NULL AND ws.status!=2';       
									break;
				default: 			$query .= ' AND ws.status!=2';
									break; 
			}
		} else {
			$query .= ' AND ws.status!=2';
		}
		
		if (!$admin) {
			$query.="\n AND ws.private='0' ";
		}
		if (isset($filters['tag']) && $filters['tag']!='') {
			$tagging = new WishTags( $this->_db );
			$tags = $tagging->_parse_tags($filters['tag']);

			$query .= " AND (RTA.objectid=ws.id AND (RTA.tbl='wishlist') AND (TA.tag IN (";
			$tquery = '';
			foreach ($tags as $tagg)
			{
				$tquery .= "'".$tagg."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.") OR TA.raw_tag IN (".$tquery;
			$query .= ")))";			
			$query .= " GROUP BY ws.id ";	
		}
		$query .= ($which == 'prev') ? " ORDER BY ws.id DESC " : " ORDER BY ws.id ASC ";
		$query .= " LIMIT 1";
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();	 	
	}
	
	//----------
	 
	public function get_vote($refid, $category= 'wish', $uid) 
	{
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
