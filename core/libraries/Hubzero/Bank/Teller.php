<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Bank;

use Hubzero\Base\Obj;
use Hubzero\Utility\Date;

/**
 * Teller class for controlling bank transactions
 */
class Teller extends Obj
{
	/**
	 * User ID
	 *
	 * @var  integer
	 */
	public $uid = null;

	/**
	 * Current point balance
	 *
	 * @var  integer
	 */
	public $balance = null;

	/**
	 * Lifetime point earnings
	 *
	 * @var  integer
	 */
	public $earnings = null;

	/**
	 * Credit point balance
	 *
	 * @var  integer
	 */
	public $credit = null;

	/**
	 * Constructor
	 * Find the balance from the most recent transaction.
	 * If no balance is found, create an initial transaction.
	 *
	 * @param   integer  $user_id  User ID
	 * @return  void
	 */
	public function __construct($user_id)
	{
		$this->uid      = $user_id;
		$this->balance  = 0;
		$this->earnings = 0;
		$this->credit   = 0;

		$BA = Account::oneByUserId($this->uid);

		if ($BA->get('id'))
		{
			$this->balance  = $BA->get('balance');
			$this->earnings = $BA->get('earnings');
			$this->credit   = $BA->get('credit');
		}
		else
		{
			// no points are given initially
			$this->_saveBalance('creation');
		}
	}

	/**
	 * Get the current balance
	 *
	 * @return  integer
	 */
	public function summary()
	{
		return $this->balance;
	}

	/**
	 * Get the current credit balance
	 *
	 * @return  integer
	 */
	public function credit_summary()
	{
		return $this->credit;
	}

	/**
	 * Add points
	 *
	 * @param   integer  $amount  Amount to deposit
	 * @param   string   $desc    Transaction description
	 * @param   string   $cat     Transaction category
	 * @param   integer  $ref     ID of item transaction references
	 * @return  void
	 */
	public function deposit($amount, $desc, $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);

		if ($this->getError())
		{
			echo $this->getError();
			return;
		}

		$this->balance  += $amount;
		$this->earnings += $amount;

		if (!$this->_save('deposit', $amount, $desc, $cat, $ref))
		{
			echo $this->getError();
		}
	}

	/**
	 * Withdraw (spend) points
	 *
	 * @param   number   $amount  Amount to withdraw
	 * @param   string   $desc    Transaction description
	 * @param   string   $cat     Transaction category
	 * @param   integer  $ref     ID of item transaction references
	 * @return  void
	 */
	public function withdraw($amount, $desc, $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);

		if ($this->getError())
		{
			echo $this->getError();
			return;
		}

		if ($this->_creditCheck($amount))
		{
			$this->balance -= $amount;

			if (!$this->_save('withdraw', $amount, $desc, $cat, $ref))
			{
				echo $this->getError();
			}
		}
		else
		{
			echo $this->getError();
		}
	}

	/**
	 * Set points aside (credit)
	 *
	 * @param   integer  $amount  Amount to put on hold
	 * @param   string   $desc    Transaction description
	 * @param   string   $cat     Transaction category
	 * @param   integer  $ref     ID of item transaction references
	 * @return  void
	 */
	public function hold($amount, $desc, $cat, $ref)
	{
		$amount = $this->_amountCheck($amount);

		if ($this->getError())
		{
			echo $this->getError();
			return;
		}

		// Current order processing workflow (which requires manual order fulfillment on the backend) prevents race
		// condition with the check and update below from corrupting user point balance, but if
		// but if workflow is ever changed, a table/row level lock would need to be added to this function
		// and error code added to deal with multiple orders with insufficient balances.
		//
		// See https://hubzero.org/support/ticket/234 for details

		if ($this->_creditCheck($amount))
		{
			$this->credit += $amount;

			if (!$this->_save('hold', $amount, $desc, $cat, $ref))
			{
				echo $this->getError();
			}
		}
		else
		{
			echo $this->getError();
		}
	}

	/**
	 * Make credit adjustment
	 *
	 * @param   integer  $amount  Amount to credit
	 * @return  void
	 */
	public function credit_adjustment($amount)
	{
		$amount = intval($amount);

		$this->credit = ($amount > 0 ? $amount : 0);

		$this->_saveBalance('update');
	}

	/**
	 * Get a history of transactions
	 *
	 * @param   integer  $limit  Number of records to return
	 * @return  array
	 */
	public function history($limit=20)
	{
		return Transaction::history($limit, $this->uid);
	}

	/**
	 * Check that they have enough in their account to perform the transaction.
	 *
	 * @param   number   $amount  Amount to subtract from balance
	 * @return  boolean  True if they have enough credit
	 */
	public function _creditCheck($amount)
	{
		$b = $this->balance;
		$b -= $amount;
		$c = $this->credit;
		$ccheck = $b - $c;

		if ($b >= 0 && $ccheck >= 0)
		{
			return true;
		}

		$this->setError('Not enough points in user account to process transaction.');
		return false;
	}

	/**
	 * Check if an amount is greater than 0
	 *
	 * @param   integer  $amount  Amount to check
	 * @return  integer
	 */
	public function _amountCheck($amount)
	{
		$amount = intval($amount);

		if ($amount == 0)
		{
			$this->setError('Cannot process transaction with 0 points.');
		}

		return $amount;
	}

	/**
	 * Save a record
	 *
	 * @param   string   $type    Record type (inserting or updating)
	 * @param   integer  $amount  Amount to process
	 * @param   string   $desc    Transaction description
	 * @param   string   $cat     Transaction category
	 * @param   integer  $ref     ID of item transaction references
	 * @return  boolean  True on success
	 */
	public function _save($type, $amount, $desc, $cat, $ref)
	{
		if (!$this->_saveBalance($type))
		{
			return false;
		}

		if (!$this->_saveTransaction($type, $amount, $desc, $cat, $ref))
		{
			return false;
		}

		return true;
	}

	/**
	 * Save the current balance
	 *
	 * @param   string   $type  Record type (inserting or updating)
	 * @return  boolean  True on success
	 */
	public function _saveBalance($type)
	{
		if ($type == 'creation')
		{
			$model = Account::blank();
		}
		else
		{
			$model = Account::oneByUserId($this->uid);
		}

		$model->set([
			'uid'      => $this->uid,
			'balance'  => $this->balance,
			'earnings' => $this->earnings,
			'credit'   => $this->credit
		]);

		if (!$model->save())
		{
			$this->setError($model->getError());

			return false;
		}

		return true;
	}

	/**
	 * Record the transaction
	 *
	 * @param   string   $type    Record type (inserting or updating)
	 * @param   integer  $amount  Amount to process
	 * @param   string   $desc    Transaction description
	 * @param   string   $cat     Transaction category
	 * @param   integer  $ref     ID of item transaction references
	 * @return  boolean  True on success
	 */
	public function _saveTransaction($type, $amount, $desc, $cat, $ref)
	{
		$transaction = Transaction::blank()->set(array(
			'uid'         => $this->uid,
			'type'        => $type,
			'amount'      => $amount,
			'description' => $desc,
			'category'    => $cat,
			'referenceid' => $ref,
			'balance'     => $this->balance
		));

		if (!$transaction->save())
		{
			$this->setError($transaction->getError());

			return false;
		}

		return true;
	}
}
