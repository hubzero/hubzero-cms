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


class Hubzero_Bank_Teller extends JObject
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
	
	public function summary()
	{
		return $this->balance;
	}
	
	//-----------
	// Get the current credit balance
	
	public function credit_summary()
	{
		return $this->credit;
	}
	
	//-----------
	// Add points
	
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
	
	public function hold($amount, $desc='Hold', $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);
		
		if ($this->_error) {
			echo $this->getError();
			return;
		}
		
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
	
	public function credit_adjustment($amount)
	{	
		$amount = (intval($amount) > 0) ? intval($amount) : 0;
		$this->credit = $amount;
		$this->_saveBalance('update');
	}
	
	//-----------
	// Get a history of transactions
	
	public function history( $limit=20 )
	{
		$lmt = "";
		if ($limit > 0) {
			$lmt .= " LIMIT ".$limit;
		}
		$this->_db->setQuery( "SELECT * FROM #__users_transactions WHERE uid=".$this->uid." ORDER BY created DESC, id DESC".$lmt );
		return $this->_db->loadObjectList();
	}
	
	//-----------

	public function getError() 
	{
		return $this->_error;
	}

	//-----------
	// Check that they have enough in their account 
	// to perform the transaction.
	
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
	
	//-----------
	
	public function _amountCheck($amount)
	{
		$amount = intval($amount);
		if ($amount == 0) {
			$this->_error = 'Cannot process transaction with 0 points.';
		}
		return $amount;
	}
	
	//-----------
	
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

