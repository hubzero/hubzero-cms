<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
		$this->options = new stdClass();
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