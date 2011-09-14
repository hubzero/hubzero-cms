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

	public function __construct( &$db )
	{
		parent::__construct( '#__users_points_subscriptions', 'id', $db );
	}

	public function check()
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

	public function loadSubscription( $id = NULL, $oid=NULL, $serviceid = NULL, $status = array(0, 1, 2) )
	{
		if ($id == 0 or  ($oid === NULL && $serviceid === NULL)) {
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE ";
		if ($id) {
			$query .= "id='$id' ";
		} else if ($oid && $serviceid) {
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
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	public function cancelSubscription( $subid = NULL, $refund=0, $unitsleft=0)
	{
		if ($subid === NULL ) {
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

	public function getSubscriptionsCount( $filters=array(), $admin=false )
	{
		$filters['exlcudeadmin'] = 1;
		$filter = $this->buildQuery( $filters, $admin );

		$sql = "SELECT count(*) FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid $filter";

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	public function getSubscriptions($filters, $admin=false)
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

	public function getSubscription($id)
	{
		if ($id === NULL ) {
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

	public function buildQuery( $filters=array(), $admin=false )
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

		if (isset($filters['exlcudeadmin'])) {
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

	public function generateCode($minlength = 6, $maxlength = 6, $usespecial = 0, $usenumbers = 1, $useletters = 1 )
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

	public function getRemaining( $type='unit', $subscription = NULL, $maxunits = 24, $unitsize=1 )
	{
		if ($subscription === NULL ) {
			return false;
		}

		$current_time = time();

		$limits = array();
		$starttime = $subscription->added;
		$lastunit = 0;
		$today = date('Y-m-d H:i:s', time() - (24 * 60 * 60));

		for ($i = 0; $i < $maxunits; $i++)
		{
			$starttime = date('Y-m-d', strtotime("+".$unitsize."month", strtotime($starttime)));
			$limits[$i] = $starttime;
		}

		for ($j = 0; $j < count($limits); $j++)
		{
			if (strtotime($current_time) < strtotime($limits[$j])) {
				$lastunit = $j + 1;
				if ($type == 'unit') {
					$remaining= $subscription->units - $lastunit;
					$refund = $remaining > 0 ? $remaining : 0;
					return ($remaining);
				}
			}
		}
	}
}

