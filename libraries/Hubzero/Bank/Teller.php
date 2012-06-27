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
 * Short description for 'Hubzero_Bank_Teller'
 * 
 * Long description (if any) ...
 */
class Hubzero_Bank_Teller extends JObject
{

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	var $_db      = NULL;  // Database

	/**
	 * Description for 'uid'
	 * 
	 * @var string
	 */
	var $uid      = NULL;  // User ID

	/**
	 * Description for 'balance'
	 * 
	 * @var mixed
	 */
	var $balance  = NULL;  // Current point balance

	/**
	 * Description for 'earnings'
	 * 
	 * @var mixed
	 */
	var $earnings = NULL;  // Lifetime point earnings

	/**
	 * Description for 'credit'
	 * 
	 * @var mixed
	 */
	var $credit   = NULL;  // Credit point balance 

	/**
	 * Description for '_error'
	 * 
	 * @var string
	 */
	var $_error   = NULL;  // Errors
	//var $_id      = NULL;  // ID for #__users_points record

	//-----------
	// Constructor
	// Find the balance from the most recent transaction.
	// If no balance is found, create an initial transaction.

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db, $uid )
	{
		$this->_db = $db;
		$this->uid = $uid;

		$BA = new Hubzero_Bank_Account( $this->_db );

		if ($BA->load_uid( $this->uid )) {
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

	/**
	 * Short description for 'summary'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function summary()
	{
		return $this->balance;
	}

	//-----------
	// Get the current credit balance

	/**
	 * Short description for 'credit_summary'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function credit_summary()
	{
		return $this->credit;
	}

	//-----------
	// Add points

	/**
	 * Short description for 'deposit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $amount Parameter description (if any) ...
	 * @param      string $desc Parameter description (if any) ...
	 * @param      unknown $cat Parameter description (if any) ...
	 * @param      unknown $ref Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function deposit($amount, $desc='Deposit', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);

		if ($this->_error) {
			echo $this->getError();
			return;
		}

		$this->balance  += $amount;
		$this->earnings += $amount;

		if (!$this->_save( 'deposit', $amount, $desc, $cat, $ref )) {
			echo $this->getError();
		}
	}

	//-----------
	// Withdraw (spend) points

	/**
	 * Short description for 'withdraw'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      number $amount Parameter description (if any) ...
	 * @param      string $desc Parameter description (if any) ...
	 * @param      unknown $cat Parameter description (if any) ...
	 * @param      unknown $ref Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function withdraw($amount, $desc='Withdraw', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);

		if ($this->_error) {
			echo $this->getError();
			return;
		}

		if ($this->_creditCheck($amount)) {
			$this->balance -= $amount;

			if (!$this->_save( 'withdraw', $amount, $desc, $cat, $ref )) {
				echo $this->getError();
			}
		} else {
			echo $this->getError();
		}
	}

	//-----------
	// Set points aside (credit)

	/**
	 * Short description for 'hold'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $amount Parameter description (if any) ...
	 * @param      string $desc Parameter description (if any) ...
	 * @param      unknown $cat Parameter description (if any) ...
	 * @param      unknown $ref Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function hold($amount, $desc='Hold', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);

		if ($this->_error) {
			echo $this->getError();
			return;
		}

		// Current order processing workflow (which requires manual order fulfillment on the backend) prevents race
		// condition with the check and update below from corrupting user point balance, but if 
		// workflow is ever changed, a table/row level lock would need to be added to this function
		// and error code added to deal with multiple orders with insufficient balances.
		//
		// See ticket #234 for details

		if ($this->_creditCheck($amount)) {
			$this->credit += $amount;

			if (!$this->_save( 'hold', $amount, $desc, $cat, $ref )) {
				echo $this->getError();
			}
		} else {
			echo $this->getError();
		}
	}

	//-------------
	// Make credit adjustment

	/**
	 * Short description for 'credit_adjustment'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $amount Parameter description (if any) ...
	 * @return     void
	 */
	public function credit_adjustment($amount)
	{
		$amount = (intval($amount) > 0) ? intval($amount) : 0;
		$this->credit = $amount;
		$this->_saveBalance('update');
	}

	//-----------
	// Get a history of transactions

	/**
	 * Short description for 'history'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $limit Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function history( $limit=20 )
	{
		$lmt = "";
		if ($limit > 0) {
			$lmt .= " LIMIT ".$limit;
		}
		$this->_db->setQuery( "SELECT * FROM #__users_transactions WHERE uid=".$this->uid." ORDER BY created DESC, id DESC".$lmt );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getError'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
	 */
	public function getError()
	{
		return $this->_error;
	}

	//-----------
	// Check that they have enough in their account 
	// to perform the transaction.

	/**
	 * Short description for '_creditCheck'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      number $amount Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function _creditCheck($amount)
	{
		$b = $this->balance;
		$b -= $amount;
		$c = $this->credit;
		$ccheck = $b - $c;

		if ($b >= 0 && $ccheck >= 0) {
			return true;
		} else {
			$this->_error = 'Not enough points in user account to process transaction.';
			return false;
		}
	}

	/**
	 * Short description for '_amountCheck'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $amount Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function _amountCheck($amount)
	{
		$amount = intval($amount);
		if ($amount == 0) {
			$this->_error = 'Cannot process transaction with 0 points.';
		}
		return $amount;
	}

	/**
	 * Short description for '_save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      unknown $amount Parameter description (if any) ...
	 * @param      unknown $desc Parameter description (if any) ...
	 * @param      unknown $cat Parameter description (if any) ...
	 * @param      unknown $ref Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function _save( $type, $amount, $desc, $cat, $ref )
	{
		if (!$this->_saveBalance( $type )) {
			return false;
		}
		if (!$this->_saveTransaction( $type, $amount, $desc, $cat, $ref )) {
			return false;
		}

		return true;
	}

	//-----------
	// Save the current balance

	/**
	 * Short description for '_saveBalance'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $type Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function _saveBalance( $type )
	{

		if ($type == 'creation') {
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

	/**
	 * Short description for '_saveTransaction'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $type Parameter description (if any) ...
	 * @param      unknown $amount Parameter description (if any) ...
	 * @param      unknown $desc Parameter description (if any) ...
	 * @param      unknown $cat Parameter description (if any) ...
	 * @param      unknown $ref Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function _saveTransaction( $type, $amount, $desc, $cat, $ref )
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

		$BT = new Hubzero_Bank_Transaction( $this->_db );
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

