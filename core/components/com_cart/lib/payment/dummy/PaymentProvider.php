<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die('Restricted access');

/**
 * UPay payment provider
 */
class PaymentProvider
{
	/**
	 * List of button vars
	 *
	 * @var  array
	 */
	private $buttonVars;

	/**
	 * Credentials
	 *
	 * @var  array
	 */
	private $credentials;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->options = new \stdClass();
		// Default action is payment
		$this->options->postbackAction = 'payment';
	}

	/**
	 * Set transaction details
	 *
	 * @param   object  $transactionDetails
	 * @return  void
	 */
	public function setTransactionDetails($transactionDetails)
	{
		$this->buttonVars['custom'] = $transactionDetails->token . '-' . $transactionDetails->info->tId;
	}

	/**
	 * Get HTML code for payment button
	 *
	 * @return  string
	 */
	public function getPaymentCode()
	{
		$code  = '<form method="post" action="' . rtrim(Request::root(), '/') . '/cart/test/pay">';
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
	 * @param   array  $postMessage
	 * @return  int    associated transaction ID if $_POST data is valid, false otherwise
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
	 *
	 * @return  string
	 */
	public function getPostBackAction()
	{
		return $this->options->postbackAction;
	}

	/**
	 * Verify the payment -- make sure it matches the transaction awaiting payment
	 *
	 * @param   object  $tInfo  transaction info
	 * @return  bool
	 */
	public function verifyPayment($tInfo)
	{
		// This is where the amount received is verified against amount expected, etc.

		return true;
	}
}
