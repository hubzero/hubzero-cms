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
	private $options;
	private $siteDetails;
	private $transactionDetails;

	/**
	 * Constructor
	 *
	 * @param 	object transaction info
	 * @param	string payment gateway provider
	 * @param 	object payment gateway credentials
	 * @param 	object payment options
	 * @return  void
	 */
	public function __construct()
	{
		$hubName  = Config::get('sitename');

		$params = Component::params(Request::getVar('option'));

		$this->options = new stdClass();
		// Default action is payment
		$this->options->postbackAction = 'payment';
		$this->options->transactionName = "$hubName online purchase";
		$this->options->env = $params->get('paymentProviderEnv');
		$this->options->validationKey = $params->get('UPAY_VALIDATION_KEY');
		// Posting Key should be configured in uPay to be the same as a validation key (UPAY_VALIDATION_KEY)
		$this->options->postingKey = $this->options->validationKey;

		$this->siteDetails = new stdClass();
		$this->siteDetails->siteId = $params->get('UPAY_SITE_ID');
	}

	/**
	 * Set transaction details
	 *
	 */
	public function setTransactionDetails($transactionDetails)
	{
		$this->transactionDetails = array();
		$this->transactionDetails['EXT_TRANS_ID'] = $transactionDetails->info->tId;
		$this->transactionDetails['EXT_TRANS_ID_LABEL'] = $this->options->transactionName;
		$this->transactionDetails['AMT'] = $transactionDetails->info->tiTotal;
		$this->transactionDetails['VALIDATION_KEY'] = $this->generateValidationKey();
		$this->transactionDetails['SUCCESS_LINK'] = Request::base() . 'cart' . DS . 'order' . DS . 'complete?tId=' . $transactionDetails->token . '-' . $transactionDetails->info->tId;
	}

	/**
	 * Get HTML code for payment button
	 *
	 */
	public function getPaymentCode()
	{
		$code  = '<form method="post" action="' . $this->getPostURL() . '">';
		$code .= '<input type="hidden" value="' . $this->siteDetails->siteId . '" name="UPAY_SITE_ID">';

		foreach ($this->transactionDetails as $k => $v)
		{
			$code .= '<input type="hidden" value="' . $v . '" name="' . $k . '">';
		}

		$code .= '<input type="submit" value="PAY">';
		$code .= '</form>';
		return $code;
	}

	/* ------------------------ Post back functions ---------------------------- */

	/**
	 * Set the postback info ($_POST) that came from the payment gateway (whatever uPay posted back)
	 *
	 * @param 	array $_POST
	 * @return 	int transaction ID if $_POST data is valid, false otherwise
	 */
	public function setPostBack($postBack)
	{
		$this->postBack = $postBack;

		// Check if the post back is kosher (really comes from uPay).
		if ($postBack['posting_key'] != $this->options->postingKey)
		{
			return false;
		}

		// Get transaction ID from the data received
		$tId = $postBack['EXT_TRANS_ID'];
		if (empty($tId) || !is_numeric($tId))
		{
			return false;
		}

		// Extract the post back action (if different from payment)
		if ($postBack['pmt_status'] == 'cancelled')
		{
			$this->options->postbackAction = 'cancel';
		}

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

		// Get payment received
		$payment = $this->postBack['pmt_amt'];

		// Transaction payment expected
		$tAmount = $tInfo->info->tiTotal;

		if ($tAmount == $payment)
		{
			return true;
		}

		// Generate error message
		if ($tAmount > $payment)
		{
			$moreLess = Lang::txt('COM_CART_POSTBACK_INCORRECT_AMOUNT_LESS');
		}
		else
		{
			$moreLess = Lang::txt('COM_CART_POSTBACK_INCORRECT_AMOUNT_MORE');
		}

		$errorMessage = sprintf(Lang::txt('COM_CART_POSTBACK_INCORRECT_AMOUNT_ERROR'), $payment, $moreLess, $tAmount);

		// Set error
		$this->error = new stdClass();
		$this->error->msg = $errorMessage;

		return false;
	}

	/* ------------------------ Private helper functions ---------------------------- */

	/**
	 * Get the Posting URL
	 *
	 */
	private function getPostURL()
	{
		//return('http://www.conmerge.com/temp/post.php');
		if ($this->options->env == 'LIVE')
		{
			return 'https://secure.touchnet.com/C21261_upay/web/index.jsp';
		}
		else
		{
			//return 'http://www.conmerge.com/temp/post.php';
			return 'https://secure.touchnet.com:8443/C21261test_upay/web/index.jsp';
		}
	}

	/**
	 * Generate the validation key
	 *
	 */
	private function generateValidationKey()
	{
		$base = $this->options->validationKey . $this->transactionDetails['EXT_TRANS_ID'] . $this->transactionDetails['AMT'];
		return base64_encode(md5($base, true));
	}
}