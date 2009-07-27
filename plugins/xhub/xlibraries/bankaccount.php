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
// Bank Config class
//----------------------------------------------------------

class BankConfig extends JObject
{
	var $_db = NULL;

	//-----------

	function set( $property, $value=NULL ) 
	{
		$this->$property = $value;
	}
	
	//-----------

	function get( $property, $default=NULL )
	{
		if (isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}

	//-----------

	function BankConfig( &$db ) 
	{
		$this->_db = $db;
		
		$this->_db->setQuery( "SELECT * FROM #__users_points_config" );
		$pc = $this->_db->loadObjectList();
		foreach($pc as $p)
		{
			$this->set($p->alias,$p->points);
		}
	}
}

//----------------------------------------------------------
// Bank Account class
//----------------------------------------------------------

class BankAccount extends JTable
{
	var $id       = NULL;  // @var int(11) Primary key
	var $uid      = NULL;  // @var int(11)
	var $balance  = NULL;  // @var decimal(11,2)
	var $earnings = NULL;  // @var decimal(11,2)
	var $credit   = NULL;  // @var decimal(11,2)

	
	function __construct( &$db ) 
	{
		parent::__construct( '#__users_points', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Entry must have a user ID.') );
			return false;
		}

		return true;
	}
	
	
	function load_uid( $oid=NULL ) 
	{
		if ($oid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$oid'" );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}

//----------------------------------------------------------
// Bank Transaction class
//----------------------------------------------------------

class BankTransaction extends JTable 
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

	
	function __construct( &$db ) 
	{
		parent::__construct( '#__users_transactions', 'id', $db );
	}
	
	
	function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->_error = 'Entry must have a user ID.';
			return false;
		}
		return true;
	}
	
	//-----------
	
	function history( $limit=50, $uid=null )
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
	
	function deleteRecords( $category=null, $type=null, $referenceid=null ) 
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
	function getTransactions( $category=null, $type=null, $referenceid=null, $uid=null ) { 
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
		if($uid) {
		$query .= " AND uid=".$uid;
		}
		$query .= " GROUP BY referenceid";
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	
	}
	
	//-----------
	function getAmount( $category=null, $type=null, $referenceid=null, $uid=null ) 
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
		if($uid) {
		$query .= " AND uid=".$uid;
		}
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	//-----------
	function getTotals( $category=null, $type=null, $referenceid=null, $royalty=0, $action=null, $uid=null, $allusers = 0, $when=null, $calc=0 ) 
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
		if($calc==0) {
		$query .= " SUM(amount)";
		}
		else if($calc==1) {
		// average
		$query .= " AVG(amount)";
		}
		else if($calc==2) {
		// num of transactions
		$query .= " COUNT(*)";
		}
		$query .= " FROM $this->_tbl WHERE type='".$type."' ";
		if($category) {
		$query .= " AND category='".$category."' ";
		}
		if($referenceid) {
		$query .= "AND referenceid=".$referenceid;
		}
		if($royalty) {
		$query .=" AND description like 'Royalty payment%' ";
		}
		if($action=='asked') {
		$query .=" AND description like '%posting question%' ";
		}
		else if($action=='answered') {
		$query .=" AND (description like '%answering question%' OR description like 'Answer for question%' OR description like 'Answered question%') ";
		}
		else if($action=='misc') {
		$query .=" AND (description NOT LIKE '%posting question%' AND description NOT LIKE '%answering question%' 
		AND description NOT LIKE 'Answer for question%' AND description NOT LIKE 'Answered question%') ";
		}
		if(!$allusers) {
		$query .=" AND uid=$uid ";
		}
		if($when){
		$query .=" AND created LIKE '".$when."%' ";
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

//----------------------------------------------------------
// Bank Account class
//----------------------------------------------------------

class BankTeller extends JObject
{
	var $_db      = NULL;  // Database
	var $uid      = NULL;  // User ID
	var $balance  = NULL;  // Current point balance
	var $earnings = NULL;  // Lifetime point earnings
	var $credit   = NULL;  // Credit point balance 
	var $_error   = NULL;  // Errors
	//var $_id      = NULL;  // ID for #__users_points record
	
	//-----------
	// Constructor
	// Find the balance from the most recent transaction.
	// If no balance is found, create an initial transaction.
	
	function __construct( &$db, $uid )
	{
		$this->_db = $db;
		$this->uid = $uid;
		
		$BA = new BankAccount( $this->_db );

		if($BA->load_uid( $this->uid )) {
			$this->balance  = $BA->balance;
			$this->earnings = $BA->earnings;
			$this->credit = $BA->credit;
			//$this->_id      = $BA->id;
		} else {
			// no points are given initially
			$this->balance  = 0;
			$this->earnings = 0;
			$this->credit = 0;
			$this->_saveBalance( 'creation' );
		}
		
	}
	
	//-----------
	// Get the current balance
	
	function summary()
	{
		return $this->balance;
	}
	
	//-----------
	// Get the current credit balance
	
	function credit_summary()
	{
		return $this->credit;
	}
	
	//-----------
	// Add points
	
	function deposit($amount, $desc='Deposit', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);
		
		if($this->_error) {
			echo $this->getError();
			return;
		}
		
		$this->balance  += $amount;
		$this->earnings += $amount;
		
		if(!$this->_save( 'deposit', $amount, $desc, $cat, $ref )) {
			echo $this->getError();
		}
	}
	
	//-----------
	// Withdraw (spend) points
	
	function withdraw($amount, $desc='Withdraw', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);
		
		if($this->_error) {
			echo $this->getError();
			return;
		}
		
		if($this->_creditCheck($amount)) {
			$this->balance -= $amount;
			
			if(!$this->_save( 'withdraw', $amount, $desc, $cat, $ref )) {
				echo $this->getError();
			}
		} else {
			echo $this->getError();
		}
	}
	
	//-----------
	// Set points aside (credit)
	
	function hold($amount, $desc='Hold', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);
		
		if($this->_error) {
			echo $this->getError();
			return;
		}
		
		if($this->_creditCheck($amount)) {
			$this->credit += $amount;
			
			if(!$this->_save( 'hold', $amount, $desc, $cat, $ref )) {
				echo $this->getError();
			}
		} else {
			echo $this->getError();
		}
	}
	//-------------
	// Make credit adjustment
	
	function credit_adjustment($amount)
	{	
		$amount = (intval($amount) > 0) ? intval($amount) : 0;
		$this->credit = $amount;
		$this->_saveBalance('update');
	}
	
	//-----------
	// Get a history of transactions
	
	function history( $limit=20 )
	{
		$lmt = "";
		if($limit > 0) {
			$lmt .= " LIMIT ".$limit;
		}
		$this->_db->setQuery( "SELECT * FROM #__users_transactions WHERE uid=".$this->uid." ORDER BY created DESC, id DESC".$lmt );
		return $this->_db->loadObjectList();
	}
	

	//-----------

	function getError() 
	{
		return $this->_error;
	}

	//-----------
	// Check that they have enough in their account 
	// to perform the transaction.
	
	function _creditCheck($amount)
	{
		$b = $this->balance;
		$b -= $amount;
		$c = $this->credit;
		$ccheck = $b - $c;

		if($b >= 0 && $ccheck >= 0) {
			return true;
		} else {
			$this->_error = 'Not enough points in user account to process transaction.';
			return false;
		}
	}
	
	//-----------
	
	function _amountCheck($amount)
	{
		$amount = intval($amount);
		if($amount == 0) {
			$this->_error = 'Cannot process transaction with 0 points.';
		}
		return $amount;
	}
	
	//-----------
	
	function _save( $type, $amount, $desc, $cat, $ref )
	{
		if(!$this->_saveBalance( $type )) {
			return false;
		}
		if(!$this->_saveTransaction( $type, $amount, $desc, $cat, $ref )) {
			return false;
		}
		
		return true;
	}
	
	
	//-----------
	// Save the current balance
	
	function _saveBalance( $type )
	{

		if($type == 'creation') {
			$query = "INSERT INTO #__users_points (uid, balance, earnings, credit) VALUES('".$this->uid."','".$this->balance."','".$this->earnings."','".$this->credit."')";
		} else {
			$query = "UPDATE #__users_points SET balance='".$this->balance."', earnings='".$this->earnings."', credit='".$this->credit."' WHERE uid=".$this->uid;
		}
		$this->_db->setQuery( $query );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
		return true;
	}
	
	//-----------
	// Record the transaction
	
	function _saveTransaction( $type, $amount, $desc, $cat, $ref )
	{
		$data = array();
		$data['uid'] = $this->uid;
		$data['type'] = $type;
		$data['amount'] = $amount;
		$data['description'] = $desc;
		$data['category'] = $cat;
		$data['referenceid'] = $ref;
		$data['created'] = date( 'Y-m-d H:i:s', time() );
		$data['balance'] = $this->balance;
		
		$BT = new BankTransaction( $this->_db );
		if (!$BT->bind( $data )) {
			$this->_error = $BT->getError();
			return false;
		}
		if (!$BT->check()) {
			$this->_error = $BT->getError();
			return false;
		}
		if (!$BT->store()) {
			$this->_error = $BT->getError();
			return false;
		}
		return true;
	}
}
//----------------------------------------------------------
// Wishlist Economy class:
// Stores economy funtions for wishlists
//----------------------------------------------------------

class WishlistEconomy extends JObject
{
	var $_db      = NULL;  // Database
	
	function __construct( &$db)
	{
		$this->_db = $db;
		
	}
	//-----------
	
	function getPayees($wishid) {
		if(!$wishid) {
			return null;
		}
		$sql = "SELECT DISTINCT uid FROM #__users_transactions WHERE category='wish' AND referenceid=$wishid AND type='hold'";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	
	}
	//-----------
	
	function getTotalPayment($wishid, $uid) {
		if(!$wishid or !$uid) {
			return null;
		}
		$sql = "SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid='$wishid' AND type='hold' AND uid='$uid'";
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	
	}
	
	//-----------
	
	function cleanupBonus($wishid) {
		if(!$wishid) {
			return null;
		}
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.wishlist.php' );
		$objWish = new Wish( $this->_db );
		$wish = $objWish->get_wish ($wishid, '', 1);
		
		if($wish->bonus > 0) {
			
			// Adjust credits
			$payees = $this->getPayees($wishid);
			if($payees) {
				foreach($payees as $p) {
					$BTL = new BankTeller( $this->_db , $p->uid );
					$hold = $this->getTotalPayment($wishid, $p->uid);
					if($hold) {
						$credit = $BTL->credit_summary();
						$adjusted = $credit - $hold;
						$BTL->credit_adjustment($adjusted);
					}
				}
			}
			 // Delete holds
			$BT = new BankTransaction( $this->_db  );
			$BT->deleteRecords( 'wish', 'hold', $wishid );
				
		}
		
	
	}
	
	//-----------
	
	function distribute_points($wishid, $type='grant', $points=0) {
		
		if(!$wishid) {
			return null;
		}
					
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.wishlist.php' );
		$objWish = new Wish( $this->_db );
		$wish = $objWish->get_wish ($wishid);
		
		$points = !$points ? $wish->bonus : $points;
		
		// Points for list owners
		if($points > 0 && $type!='royalty') {
			
			// Get the component parameters
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_wishlist'.DS.'wishlist.config.php' );
			$wconfig = new WishlistConfig( 'com_wishlist' );
			$admingroup = isset($wconfig->parameters['group']) ? trim($wconfig->parameters['group']) : 'hubadmin';
			
			// get list owners
			$objOwner = new WishlistOwner(  $this->_db );
			$owners   = $objOwner->get_owners($wish->wishlist, $admingroup, '', 0, $wishid );
			$owners   = $owners['individuals'];
						
			$mainshare = $wish->assigned ?  $points*0.8 : 0; //80%
			$commonshare = $mainshare ? ($points - $mainshare)/count($owners) : $points/count($owners);
						
			// give the remaining 20%
			if($owners && $commonshare) {
				foreach($owners as $owner) {
					$BTLO = new BankTeller( $this->_db , $owner );
					if($wish->assigned && $wish->assigned == $owner) {
						//$BTLO->deposit($mainshare, JText::_('Bonus for fulfilling assigned wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
						$mainshare += $commonshare;
					}
					else {
						$BTLO->deposit($commonshare, JText::_('Bonus for fulfilling wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
					}
				}
			}
			else {
				$mainshare += $commonshare;
			}
			
			// give main share
			if($wish->assigned && $mainshare) {
				$BTLM = new BankTeller( $this->_db , $wish->assigned );
				$BTLM->deposit($mainshare, JText::_('Bonus for fulfilling assigned wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
			}
					
			// Adjust credits
			$payees = $this->getPayees($wishid);
			if($payees) {
				foreach($payees as $p) {
					$BTL = new BankTeller( $this->_db , $p->uid );
					$hold = $this->getTotalPayment($wishid, $p->uid);
					if($hold) {
						$credit = $BTL->credit_summary();
						$adjusted = $credit - $hold;
						$BTL->credit_adjustment($adjusted);
						
						// withdraw bonus amount
						$BTL->withdraw($hold, JText::_('Bonus payment for granted wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist, 'wish', $wishid);
					}
				}
			}
				
			
			// Remove holds if exist
			if ($wish->bonus) {
				$BT = new BankTransaction( $this->_db  );
				$BT->deleteRecords( 'wish', 'hold', $wishid );
			}
			
			
		}
		
		// Points for wish author (needs to be granted by another person)
		$juser =& JFactory::getUser();
		if($wish->ranking > 0 && $wish->proposed_by != $juser->get('id')) {
			$BTLA = new BankTeller( $this->_db , $wish->proposed_by );
			$BTLA->deposit($wish->ranking, JText::_('Your wish').' #'.$wishid.' '.JText::_('on list').' #'.$wish->wishlist.' '.JText::_('was granted'), 'wish', $wishid);
		}
	
	}
	
}
//----------------------------------------------------------
// Resources Economy class:
// Stores economy funtions for resources
//----------------------------------------------------------

class ResourcesEconomy extends JObject
{
	var $_db      = NULL;  // Database
	
	function __construct( &$db)
	{
		$this->_db = $db;
		
	}
	
	//-----------
	
	function getCons() {
	
		// get all eligible resource contributors
		$sql = "SELECT DISTINCT aa.authorid, SUM(r.ranking) as ranking FROM jos_author_assoc AS aa "
			."\n LEFT JOIN jos_resources AS r ON r.id=aa.subid "
			."\n WHERE aa.authorid > 0 AND r.published=1 AND r.standalone=1 GROUP BY aa.authorid ";
			
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
		
	}
	
	//-----------
	
	function distribute_points($con, $type='royalty')
	{
		//$xuser 	=& XFactory::getUser();
		
		if(!is_object($con)) {
			return false;
		}
		$cat = 'resource';
		
		$points = round($con->ranking);
		
		// Get qualifying users
		$xuser =& XUser::getInstance( $con->authorid );
		
		// Reward review author
		if (is_object($xuser)) {
			$BTL = new BankTeller( $this->_db , $xuser->get('uid') );
		
			if (intval($points) > 0) {
				$msg = ($type=='royalty') ? 'Royalty payment for your resource contributions' : '';	
				$BTL->deposit($points, $msg, $cat, $review->id);
			}

		}
			
		
	}
	
}

//----------------------------------------------------------
// Reviews Economy class:
// Stores economy funtions for reviews on resources
//----------------------------------------------------------

class ReviewsEconomy extends JObject
{
	var $_db      = NULL;  // Database
	
	function __construct( &$db)
	{
		$this->_db = $db;
		
	}
	
	//-----------
	
	function getReviews() {
	
		// get all eligible reviews
		$sql = "SELECT r.id, r.user_id AS author, r.resource_id as rid, "
			."\n (SELECT COUNT(*) FROM #__abuse_reports AS a WHERE a.category='review' AND a.state!=1 AND a.referenceid=r.id) AS reports,"
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=r.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=r.id) AS nothelpful "
			."\n FROM #__resource_ratings AS r";
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		$reviews = array();
		if($result) {
			foreach ($result as $r) {
				// item is not abusive, got at least 3 votes, more positive than negative
				if(!$r->reports && (($r->helpful + $r->nothelpful) >=3) && ($r->helpful > $r->nothelpful) ) {
					$reviews[] = $r;
				}
			}
		}
		return $reviews;
		
	}
	
	//-----------
	
	function calculate_marketvalue($review, $type='royalty')
	{
		
		if(!is_object($review)) {
			return false;
		}
	
		// Get point values for actions
		$BC = new BankConfig( $this->_db );
		$p_R  = $BC->get('reviewvote') ? $BC->get('reviewvote') : 2;
		//$positive_co = 2;
		
		$calc = 0;
		if(isset($review->helpful) && isset($review->nothelpful)) {
			$calc += ($review->helpful) * $p_R;
			//$calc += ($review->helpful) * $p_R * $positive_co;
			//$calc += ($review->nothelpful)*$p_R;
		}
		
		($calc) ? $calc = $calc : $calc ='0';
		
		return $calc;
	}
	
	//-----------
	
	function distribute_points($review, $type='royalty')
	{
		//$xuser 	=& XFactory::getUser();
		
		if(!is_object($review)) {
			return false;
		}
		$cat = 'review';
		
		$points = $this->calculate_marketvalue($review, $type);
		
		// Get qualifying users
		$xuser =& XUser::getInstance( $review->author );
		
		// Reward review author
		if (is_object($xuser)) {
			$BTL = new BankTeller( $this->_db , $xuser->get('uid') );
		
			if (intval($points) > 0) {
				$msg = ($type=='royalty') ? 'Royalty payment for posting a review on resource #'.$review->rid : 'Commission for posting a review on resource #'.$review->rid;	
				$BTL->deposit($points, $msg, $cat, $review->id);
			}

		}
			
		
	}
	
}
//----------------------------------------------------------
// Answers Economy class:
// Stores economy funtions for com_answers
//----------------------------------------------------------

class AnswersEconomy extends JObject
{
	var $_db      = NULL;  // Database
	
	function __construct( &$db)
	{
		$this->_db = $db;
		
	}
	
	//-----------
	
	function getQuestions() {
	
		// get all closed questions
		$sql = "SELECT q.id, q.created_by AS q_owner, a.created_by AS a_owner
				FROM #__answers_questions AS q LEFT JOIN #__answers_responses AS a ON q.id=a.qid AND a.state=1
				WHERE q.state=1";
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
		
	}
	
	//-----------
	
	function calculate_marketvalue($id, $type='regular')
	{
		if($id === NULL) {
			$id = $this->qid;
		}
		if($id === NULL) {
			return false;
		}
		
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
		
		// Get point values for actions
		$BC = new BankConfig( $this->_db );
		$p_Q  = $BC->get('ask');
		$p_A  = $BC->get('answer');
		$p_R  = $BC->get('answervote');
		$p_RQ = $BC->get('questionvote');
		$p_A_accepted = $BC->get('accepted');
		
		$calc = 0;
		
		// Get actons and sum up
		$ar = new AnswersResponse( $this->_db );
		$result = $ar->getActions( $id );
		
		
		if ($type != 'royalty') {
			$calc += $p_Q;  // ! this is different from version before code migration !			
			$calc += (count($result))*$p_A;
		}
		
		// Calculate as if there is at leat one answer
		if($type == 'maxaward' && count($result)==0) {
			$calc += $p_A;
		}
		
		for ($i=0, $n=count($result); $i < $n; $i++) 
		{
			$calc += ($result[$i]->helpful)*$p_R;
			$calc += ($result[$i]->nothelpful)*$p_R;
			if ($result[$i]->state == 1 && $type != 'royalty') {
				$accepted = 1;
			}
		}
		
		if(isset($accepted) or $type=='maxaward') {
			$calc += $p_A_accepted;
		}
		
		// Add question votes
		$aq = new AnswersQuestion( $this->_db );
		$aq->load( $id );
		if ($aq->state != 2) {
			$calc += $aq->helpful * $p_RQ;
		}
		
		($calc) ? $calc = $calc : $calc ='0';
		
		return $calc;
	}
	

	//-----------
	
	function distribute_points($qid, $Q_owner, $BA_owner, $type)
	{
		$xuser 		=& XFactory::getUser();
		
		if($qid === NULL) {
			$qid = $this->qid;
		}
		$cat = 'answers';
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );	
		
		$points = $this->calculate_marketvalue($qid, $type);
		
		$BT = new BankTransaction( $this->_db );
		$reward = $BT->getAmount( $cat, 'hold', $qid );
		$reward = ($reward) ? $reward : '0';		
		$share = $points/3;
		
		$BA_owner_share = $share + $reward;
		$A_owner_share  = 0;
		
		// Get qualifying users
		$xuser =& XUser::getInstance( $Q_owner );
		$ba_user =& XUser::getInstance( $BA_owner );	
			
		// Calculate commissions for other answers
		$ar = new AnswersResponse( $this->_db );
		$result = $ar->getActions( $qid );
		
		$n = count($result);
		$eligible = array();
		
		if ($n > 1 ) {
			// More than one answer found
			for ($i=0; $i < $n; $i++) 
			{
				// Check if a regular answer has a good rating (at least 50% of positive votes)
				if (($result[$i]->helpful + $result[$i]->nothelpful) >= 3 
				 && ($result[$i]->helpful >= $result[$i]->nothelpful) 
				 && $result[$i]->state=='0' ) {
					$eligible[] = $result[$i]->created_by;
				}
			}
			if (count($eligible) > 0) {
				// We have eligible answers
				$A_owner_share = $share/$n;
				
			} else {
				// Best A owner gets remaining thrid
				$BA_owner_share += $share;
			}
			
		} else {
			// Best A owner gets remaining 3rd
			$BA_owner_share += $share;
		}
			
		// Reward asker
		if (is_object($xuser)) {
			$BTL_Q = new BankTeller( $this->_db , $xuser->get('uid') );
			//$BTL_Q->deposit($Q_owner_share, 'Commission for posting a question', $cat, $qid);
			// Separate comission and reward payment
			// Remove credit
			$credit = $BTL_Q->credit_summary();
			$adjusted = $credit - $reward;
			$BTL_Q->credit_adjustment($adjusted);
			
			if (intval($share) > 0) {
				$share_msg = ($type=='royalty') ? 'Royalty payment for posting question #'.$qid : 'Commission for posting question #'.$qid;	
				$BTL_Q->deposit($share, $share_msg, $cat, $qid);
			}
			// withdraw reward amount
			if ($reward) {
				$BTL_Q->withdraw($reward, 'Reward payment for your question #'.$qid, $cat, $qid);
			}
		}
				
			
		// Reward other responders
		if (count($eligible) > 0) {
				foreach ($eligible as $e) 
				{
					$auser =& XUser::getInstance( $e );
					if (is_object($auser) && is_object($ba_user) && $ba_user->get('uid') != $auser->get('uid')) {
						$BTL_A = new BankTeller( $this->_db , $auser->get('uid') );
						if (intval($A_owner_share) > 0) {
							$A_owner_share_msg = ($type=='royalty') ? 'Royalty payment for answering question #'.$qid : 'Answered question #'.$qid.' that was recently closed';
							$BTL_A->deposit($A_owner_share, $A_owner_share_msg , $cat, $qid);
						}	
					}
					// is best answer eligible for extra points?
					if(is_object($auser) && is_object($ba_user) && ($ba_user->get('uid') == $auser->get('uid'))) {
						$ba_extra = 1;
					}
				}
		}
		
		// Reward best answer
		if (is_object($ba_user)) {
			$BTL_BA = new BankTeller( $this->_db , $ba_user->get('uid') );
			
			if(isset($ba_extra)) { $BA_owner_share += $A_owner_share; }
			
			if (intval($BA_owner_share) > 0) {
				$BA_owner_share_msg = ($type=='royalty') ? 'Royalty payment for answering question #'.$qid : 'Answer for question #'.$qid.' was accepted';
				$BTL_BA->deposit($BA_owner_share, $BA_owner_share_msg, $cat, $qid);
			}
		}
	
			
		// Remove hold if exists
		if ($reward) {
			$BT = new BankTransaction( $this->_db  );
			$BT->deleteRecords( 'answers', 'hold', $qid );
		}
		
	}
		

}

//----------------------------------------------------------
// Market History class:
// Logs batch transactions, royalty distributions and other big transactions
//----------------------------------------------------------

class MarketHistory extends JTable 
{
	var $id          	= NULL;  // @var int(11) Primary key
	var $itemid      	= NULL;  // @var int(11)
	var $category    	= NULL;  // @var varchar(50)
	var $market_value	= NULL;  // @var decimal(11,2)
	var $date      		= NULL;  // @var datetime
	var $action	 		= NULL;  // @var varchar(50)
	var $log    		= NULL;  // @var text
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__market_history', 'id', $db );
	}
	
	//-----------
	
	function getRecord($itemid=0, $action='', $category='', $created='', $log = '') {
		
		if($itemid === NULL) {
			$itemid = $this->itemid;
		}
		if($action === NULL) {
			$action = $this->action;
		}
		if($category === NULL) {
			$category = $this->category;
		}
			
		$sql = "SELECT id FROM #__market_history WHERE ";
		if($itemid) {
		$sql.= " itemid='".$itemid."'";
		}
		else {
		$sql.= " 1=1";
		}
		if($action) {
		$sql.= " AND action='".$action."'";
		}
		if($category) {
		$sql.= " AND category='".$category."'";
		}
		if($created) {
		$sql.= " AND date LIKE '".$created."%'";
		}
		if($log) {
		$sql.= " AND log='".$log."'";
		}
		
		$sql.= " LIMIT 1";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
		
	}

}


?>