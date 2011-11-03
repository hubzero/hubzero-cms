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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Bank_Transaction'
 * 
 * Long description (if any) ...
 */
class Hubzero_Bank_Transaction extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id          = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid         = NULL;  // @var int(11)

	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type        = NULL;  // @var varchar(20)

	/**
	 * Description for 'category'
	 * 
	 * @var unknown
	 */
	var $category    = NULL;  // @var varchar(50)

	/**
	 * Description for 'referenceid'
	 * 
	 * @var unknown
	 */
	var $referenceid = NULL;  // @var int(11)

	/**
	 * Description for 'amount'
	 * 
	 * @var unknown
	 */
	var $amount      = NULL;  // @var int(11)

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description = NULL;  // @var varchar(250)

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created     = NULL;  // @var datetime

	/**
	 * Description for 'balance'
	 * 
	 * @var unknown
	 */
	var $balance     = NULL;  // @var int(11)

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
		parent::__construct( '#__users_transactions', 'id', $db );
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
			$this->_error = 'Entry must have a user ID.';
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'history'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $limit Parameter description (if any) ...
	 * @param      string $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'deleteRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $referenceid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getTransactions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $referenceid Parameter description (if any) ...
	 * @param      string $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getAmount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $referenceid Parameter description (if any) ...
	 * @param      string $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getTotals'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $category Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @param      string $referenceid Parameter description (if any) ...
	 * @param      integer $royalty Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      integer $allusers Parameter description (if any) ...
	 * @param      string $when Parameter description (if any) ...
	 * @param      integer $calc Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
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

