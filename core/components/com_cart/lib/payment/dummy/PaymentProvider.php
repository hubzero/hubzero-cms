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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * UPay payment provider
 *
 * Long description (if any) ...
 */
class PaymentProvider
{
	private $buttonVars;
	private $credentials;

	public function __construct()
	{
		$this->options = new \stdClass();
		// Default action is payment
		$this->options->postbackAction = 'payment';
	}

	/**
	 * Set transaction details
	 *
	 */
	public function setTransactionDetails($transactionDetails)
	{
		$this->buttonVars['custom'] = $transactionDetails->token . '-' . $transactionDetails->info->tId;
	}

	/**
	 * Get HTML code for payment button
	 *
	 */
	public function getPaymentCode()
	{
		$code  = '<form method="post" action="' . JURI::root() . 'cart/test/pay">';
		$code .= '<input type="hidden" value="dodummypayment" name="cmd">';

		foreach ($this->buttonVars as $k => $v)
		{
			$code .= '<input type="hidden" value="' . $v . '" name="' . $k . '">';
		}

		$code .= '<input type="submit" value="PAY">';
		$code .= '</form>';
		return $code;
	}

	/* ------------------------ Post back functions ---------------------------- */

	/**
	 * Set post back info ($_POST)
	 *
	 * @param 	array $_POST
	 * @return 	int associated transaction ID if $_POST data is valid, false otherwise
	 */
	public function setPostBack($postMessage)
	{
		// Get transaction ID
		$customData = explode('-', $postMessage['custom']);
		//$token = $customData[0];
		$tId = $customData[1];
		return $tId;
	}

	/**
	 * Get the post back action (payment, cancel transaction...)
	 */
	public function getPostBackAction()
	{
		return $this->options->postbackAction;
	}

	/**
	 * Verify the payment -- make sure it matches the transaction awaiting payment
	 *
	 * @param 	object transaction info
	 * @return 	bool
	 */
	public function verifyPayment($tInfo)
	{
		// This is where the amount received is verified against amount expected, etc.

		return true;
	}
}
