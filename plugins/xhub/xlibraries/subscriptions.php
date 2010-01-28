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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


//----------------------------------------------------------
// Subscription class
//----------------------------------------------------------

class Subscription extends JTable
{
	var $id       		= NULL;  // @var int(11) Primary key
	var $uid      		= NULL;  // @var int(11)
	var $serviceid  	= NULL;  // @var int(11)
	var $units 			= NULL;  //	@var int(11)
	var $status 		= NULL;  //	@var int(11)
	var $code 			= NULL;  //	@var varchar
	var $contact 		= NULL;  //	@var varchar
	var $added 			= NULL;  //	@var datetime
	var $updated 		= NULL;  //	@var datetime
	var $expires 		= NULL;  //	@var datetime
	var $pendingunits 	= NULL;  //	@var int(11)
	var $installment 	= NULL;  //	@var int(11)
	var $pendingpayment = NULL;  //	@var int(11)
	var $totalpaid 		= NULL;  //	@var int(11)
	var $notes 			= NULL;  //	@var text
	var $usepoints 		= NULL;  //	@var tinyint
	
	//-----------
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__users_points_subscriptions', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Entry must have a user ID.') );
			return false;
		}
		
		if (trim( $this->serviceid ) == '') {
			$this->setError( JText::_('Entry must have a service ID.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function loadSubscription( $id = NULL, $oid=NULL, $serviceid = NULL, $status = array(0, 1, 2) ) 
	{
		if ($id == 0 or  ($oid === NULL && $serviceid === NULL)) {
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE ";
		if($id) {
		$query .= "id='$id' ";
		}
		else if ($oid && $serviceid) {
		$query .= "uid='$oid' AND serviceid='$serviceid' ";
		}
		$query .= " AND status IN ( ";
		$tquery = '';
		foreach ($status as $tagg)
		{
			$tquery .= "'".$tagg."',";
		}
		$tquery = substr($tquery,0,strlen($tquery) - 1);
		
		$query .= $tquery.")";
		
		$this->_db->setQuery( $query );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	function cancelSubscription( $subid = NULL, $refund=0, $unitsleft=0) 
	{
		if($subid === NULL ) {
			return false;
		}
		
		// status quo if now money back is expected
		$unitsleft = $refund ? $unitsleft : 0;
	
		$query  = "UPDATE $this->_tbl SET status='2', pendingpayment='$refund', pendingunits='$unitsleft' WHERE id='$subid'" ;
		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	function getSubscriptionsCount( $filters=array(), $admin=false ) 
	{
		$filters['exlcudeadmin'] = 1;
		$filter = $this->buildQuery( $filters, $admin );		
		
		$sql = "SELECT count(*) FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid $filter";

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getSubscriptions ($filters, $admin=false)
	{
		
		$filter = $this->buildQuery( $filters, $admin );
		$filters['exlcudeadmin'] = 1;
		
		$sql  = "SELECT u.*, s.title, s.category, s.unitprice, s.currency, s.unitsize, s.unitmeasure, s.minunits, s.maxunits ";
		$sql .= " FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid ";
		$sql .= $this->buildQuery( $filters, $admin );
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getSubscription ($id)
	{
		
		if($id === NULL ) {
			return false;
		}	
	
		$sql  = "SELECT u.*, s.id as serviceid, s.title, s.category, s.unitprice, s.pointsprice, s.currency, s.unitsize, s.unitmeasure, s.minunits, s.maxunits, e.companyLocation, e.companyName, e.companyWebsite ";
		$sql .= " FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid ";
		$sql .= " JOIN #__jobs_employers as e ON e.uid=u.uid ";
		$sql .= " WHERE u.id='$id' ";
		

		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		
		$result = $result ? $result[0] : NULL;
		return $result;
	}
	
	//-----------
	
	function buildQuery( $filters=array(), $admin=false ) 
	{
		$juser =& JFactory::getUser();
		$now = date( 'Y-m-d H:i:s', time() );
		
		$query  = "";
		$query .= "WHERE 1=1 ";
		if (isset($filters['filterby'])) {
			switch ($filters['filterby']) 
			{
				case 'pending':    $query .= "AND (u.status=0 OR u.pendingpayment > 0 OR u.pendingunits > 0) "; break;
				case 'cancelled':  $query .= "AND u.status=2 ";             break;
				default:  		   $query .= '';   							break;
			}
		}
		
		if(isset($filters['exlcudeadmin'])) {
			$query .= "AND u.uid!=1 ";
		}
		
		$query .= " ORDER BY ";
		if (isset($filters['sortby'])) {
			switch ($filters['sortby']) 
			{
				case 'date':
				case 'date_added':      $query .= 'u.added DESC';      		break;
				case 'date_expires':    $query .= 'u.expires DESC';    		break;
				case 'date_updated':    $query .= 'u.updated DESC';        	break;
				case 'category':    	$query .= 's.category DESC';        break;
				case 'status':    		$query .= 'u.status ASC';        	break;
				case 'pending':    		
				default:  $query .= 'u.pendingunits DESC, u.pendingpayment DESC, u.status ASC, u.updated DESC ';   break;
			}
		}
		
		return $query;
	}
	
	 //-----------
	function generateCode ($minlength = 6, $maxlength = 6, $usespecial = 0, $usenumbers = 1, $useletters = 1 )
	{
	
		$key = '';
		$charset = '';
		if ($useletters) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	}
	 
	
	//-----------
	
	function getRemaining( $type='unit', $subscription = NULL, $maxunits = 24, $unitsize=1 ) 
	{
		if($subscription === NULL ) {
			return false;
		}
		
		$current_time = time();
			
		$limits = array();
		$starttime = $subscription->added;
		$lastunit = 0;
		$today = date('Y-m-d H:i:s', time() - (24 * 60 * 60));
		
				
		for($i = 0; $i < $maxunits; $i++) {
			$starttime = date('Y-m-d', strtotime("+".$unitsize."month", strtotime($starttime)));
			$limits[$i] = $starttime;
		}
			
		for($j = 0; $j < count($limits); $j++) {
		  if(strtotime($current_time) < strtotime($limits[$j])) {
		  	$lastunit = $j + 1;
			if($type == 'unit') {
				$remaining= $subscription->units - $lastunit;
				$refund = $remaining > 0 ? $remaining : 0;
				return ($remaining);			
			}
			
		  } 
		}

	
	}
	
	//-----------
	
	function postPayment ($subid, $units, $amount, $usepoints=0)
	{
	
	
	}
	
}

//----------------------------------------------------------
// Service  class
//----------------------------------------------------------

class Service extends JTable 
{
	var $id          	= NULL;  // @var int(11) Primary key
	var $title       	= NULL;  // @var varchar(250)
	var $category    	= NULL;  // @var varchar(50)
	var $alias		 	= NULL;  // @var varchar(50)
	var $description 	= NULL;  // @var varchar(250)
	var $unitprice   	= NULL;  // @var float
	var $pointsprice   	= NULL;  // @var int(11)
	var $currency    	= NULL;  // @var varchar(11)
	var $maxunits 		= NULL;  // @var int(11)
	var $minunits   	= NULL;  // @var int(11)
	var $unitsize   	= NULL;  // @var int(11)
	var $status   		= NULL;  // @var int(11)
	var $restricted   	= NULL;  // @var int(11)
	var $ordering   	= NULL;  // @var int(11)
	var $unitmeasure    = NULL;  // @var varchar
	var $changed     	= NULL;  // @var datetime
	var $params   		= NULL;  // @var text

	
	function __construct( &$db ) 
	{
		parent::__construct( '#__users_points_services', 'id', $db );
	}

	//-----------
	function check() 
	{
		if (trim( $this->alias ) == '') {
			$this->_error = 'Entry must have an alias.';
			return false;
		}
		if (trim( $this->category ) == '') {
			$this->_error = 'Entry must have a category.';
			return false;
		}
		return true;
	}
	
	//-----------
	
	function loadService( $alias=NULL, $id = NULL ) 
	{
		if ($alias === NULL && $id === NULL) {
			return false;
		}
		
		$query  = "SELECT * FROM $this->_tbl WHERE ";
		if($alias) {
		$query .= "alias='$alias' ";
		}
		else {
		$query .= "id='$id' ";
		}
		
		$this->_db->setQuery(  $query );
		if($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------
	
	public function getServices ($category = NULL, $completeinfo = 0, $active = 1, $sortby = 'category', $sortdir = 'ASC', $specialgroup='', $admin = 0)
	{
		$services = array();
		
		$query  = "SELECT s.* ";
		$query .= $specialgroup ? " , m.gidNumber as ingroup ": "";
		$query .= "FROM $this->_tbl AS s ";
		
		// do we have special admin group
		if($specialgroup) {
			$juser 	  =& JFactory::getUser();
			
			$query .= "JOIN #__xgroups AS xg ON xg.cn='".$specialgroup."' ";
			$query .= " LEFT JOIN #__xgroups_members AS m ON xg.gidNumber=m.gidNumber AND m.uidNumber='".$juser->get('id')."' ";
		}
		
		$query .= "WHERE 1=1 ";
		if($category) {
		$query .= "AND s.category ='$category' ";
		}
		if($active) {
		$query .= "AND s.status = 1 ";
		}
		if(!$admin) {
		$query .= $specialgroup ? "AND s.restricted = 0 or (s.restricted = 1 AND m.gidNumber IS NOT NULL ) " : " AND s.restricted = 0 ";
		}
		$query .= " ORDER BY $sortby $sortdir ";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();
		if($result) {
			foreach($result as $r) {
				if($completeinfo) {
					$services[] = $r;
				}
				else {
					$services[$r->id] = $r->title;
				}
				
			}
		}
		
		return $services;
		
	}
	
	//-----------
	
	public function getServiceCost ($id, $points = 0)
	{
		if ($id === NULL) {
			return false;
		}
		
		if($points) {
		$this->_db->setQuery( "SELECT pointsprice FROM $this->_tbl WHERE id='$id'" );
		}
		else {
		$this->_db->setQuery( "SELECT unitprice FROM $this->_tbl WHERE id='$id'" );
		}
		return $this->_db->loadResult();
		
	}
	
	//--------
	
	function getUserService( $uid = NULL, $field = 'alias', $category = 'jobs')
	{
		if ($uid === NULL) {
			return false;
		}
		
		$field = $field ? 's.'.$field : 's.*';
		
		$query  = "SELECT $field  ";
		$query .= "FROM $this->_tbl as s ";
		$query .= "JOIN #__users_points_subscriptions AS y ON s.id=y.serviceid  ";
	
		$query .= "WHERE s.category = '$category' AND y.uid = '$uid' ";
		$query .= " ORDER BY y.id DESC LIMIT 1 ";

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	
	}
	

}




?>