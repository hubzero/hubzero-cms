<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'Subscription'
 *
 * Long description (if any) ...
 */
class Subscription extends JTable
{

	/**
	 * Description for 'id'
	 *
	 * @var unknown
	 */
	var $id       		= NULL;  // @var int(11) Primary key

	/**
	 * Description for 'uid'
	 *
	 * @var unknown
	 */
	var $uid      		= NULL;  // @var int(11)

	/**
	 * Description for 'serviceid'
	 *
	 * @var unknown
	 */
	var $serviceid  	= NULL;  // @var int(11)

	/**
	 * Description for 'units'
	 *
	 * @var unknown
	 */
	var $units 			= NULL;  //	@var int(11)

	/**
	 * Description for 'status'
	 *
	 * @var unknown
	 */
	var $status 		= NULL;  //	@var int(11)

	/**
	 * Description for 'code'
	 *
	 * @var unknown
	 */
	var $code 			= NULL;  //	@var varchar

	/**
	 * Description for 'contact'
	 *
	 * @var unknown
	 */
	var $contact 		= NULL;  //	@var varchar

	/**
	 * Description for 'added'
	 *
	 * @var unknown
	 */
	var $added 			= NULL;  //	@var datetime

	/**
	 * Description for 'updated'
	 *
	 * @var unknown
	 */
	var $updated 		= NULL;  //	@var datetime

	/**
	 * Description for 'expires'
	 *
	 * @var unknown
	 */
	var $expires 		= NULL;  //	@var datetime

	/**
	 * Description for 'pendingunits'
	 *
	 * @var unknown
	 */
	var $pendingunits 	= NULL;  //	@var int(11)

	/**
	 * Description for 'installment'
	 *
	 * @var unknown
	 */
	var $installment 	= NULL;  //	@var int(11)

	/**
	 * Description for 'pendingpayment'
	 *
	 * @var unknown
	 */
	var $pendingpayment = NULL;  //	@var int(11)

	/**
	 * Description for 'totalpaid'
	 *
	 * @var unknown
	 */
	var $totalpaid 		= NULL;  //	@var int(11)

	/**
	 * Description for 'notes'
	 *
	 * @var unknown
	 */
	var $notes 			= NULL;  //	@var text

	/**
	 * Description for 'usepoints'
	 *
	 * @var unknown
	 */
	var $usepoints 		= NULL;  //	@var tinyint

	//-----------

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__users_points_subscriptions', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'loadSubscription'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $id Parameter description (if any) ...
	 * @param      unknown $oid Parameter description (if any) ...
	 * @param      unknown $serviceid Parameter description (if any) ...
	 * @param      array $status Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'cancelSubscription'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $subid Parameter description (if any) ...
	 * @param      integer $refund Parameter description (if any) ...
	 * @param      integer $unitsleft Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getSubscriptionsCount'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getSubscriptionsCount( $filters=array(), $admin=false )
	{
		$filters['exlcudeadmin'] = 1;
		$filter = $this->buildQuery( $filters, $admin );

		$sql = "SELECT count(*) FROM $this->_tbl AS u JOIN #__users_points_services as s ON s.id=u.serviceid $filter";

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getSubscriptions'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getSubscription'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'buildQuery'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters=array(), $admin=false )
	{
		$juser = JFactory::getUser();
		$now = JFactory::getDate()->toSql();

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

	/**
	 * Short description for 'generateCode'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $minlength Parameter description (if any) ...
	 * @param      integer $maxlength Parameter description (if any) ...
	 * @param      integer $usespecial Parameter description (if any) ...
	 * @param      integer $usenumbers Parameter description (if any) ...
	 * @param      integer $useletters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getRemaining'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $type Parameter description (if any) ...
	 * @param      object $subscription Parameter description (if any) ...
	 * @param      integer $maxunits Parameter description (if any) ...
	 * @param      mixed $unitsize Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getRemaining( $type='unit', $subscription = NULL, $maxunits = 24, $unitsize=1 )
	{
		if ($subscription === NULL ) {
			return false;
		}

		$current_time = time();

		$limits = array();
		$starttime = $subscription->added;
		$lastunit = 0;
		$today = JFactory::getDate(time() - (24 * 60 * 60))->toSql();

		for ($i = 0; $i < $maxunits; $i++)
		{
			$starttime = JFactory::getDate(strtotime("+".$unitsize."month", strtotime($starttime)))->format('Y-m-d');
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

