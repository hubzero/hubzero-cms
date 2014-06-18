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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Paypal Standard
 *
 * Long description (if any) ...
 */
class PaymentProvider
{
	private $buttonVars;
	private $credentials;
	private $postMessage;
	private $error;

	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return  void
	 */
	public function __construct()
	{
		$jconfig = JFactory::getConfig();
		$hubName  = $jconfig->getValue('config.sitename');

		$params =  JComponentHelper::getParams(JRequest::getVar('option'));

		$paymentOptions->transactionName = "$hubName online purchase";
		$paymentOptions->businessName = $params->get('PPS_businessName');
		$this->setPaymentOptions($paymentOptions);

		$paymentGatewayCredentials->user = $params->get('PPS_user');
		$paymentGatewayCredentials->password = $params->get('PPS_password');
		$paymentGatewayCredentials->signature = $params->get('PPS_signature');
		$this->setCredentials($paymentGatewayCredentials);
	}

	/**
	 * Set transaction details
	 *
	 */
	public function setTransactionDetails($transactionDetails)
	{
		$this->buttonVars[] = 'custom=' . $transactionDetails->token . '-' . $transactionDetails->info->tId;
		$this->buttonVars[] = 'amount=' . $transactionDetails->info->tiTotal;
	}

	/**
	 * Set payment options
	 *
	 */
	private function setPaymentOptions($paymentOptions)
	{
		$this->buttonVars[] = 'item_name=' . $paymentOptions->transactionName;
		$this->buttonVars[] = 'business=' . $paymentOptions->businessName;
	}

	/**
	 * Set credentials
	 *
	 */
	private function setCredentials($paymentGatewayCredentials)
	{
		$this->credentials = $paymentGatewayCredentials;
	}

	/**
	 * Get HTML code for payment button
	 *
	 */
	public function getPaymentCode()
	{
		$path = JPATH_COMPONENT . '/lib/payment/paypal_standard/lib';
		set_include_path($path);
		require_once('services/PayPalAPIInterfaceService/PayPalAPIInterfaceServiceService.php');

		$createButtonRequest = new BMCreateButtonRequestType();
		$createButtonRequest->ButtonCode = 'ENCRYPTED';
		$createButtonRequest->ButtonType = 'BUYNOW';
		$createButtonRequest->ButtonVar = $this->buttonVars;

		$createButtonReq = new BMCreateButtonReq();
		$createButtonReq->BMCreateButtonRequest = $createButtonRequest;

		$paypalService = new PayPalAPIInterfaceServiceService();

		$credentials = new PPSignatureCredential($this->credentials->user, $this->credentials->password, $this->credentials->signature);

		try
		{
			$createButtonResponse = $paypalService->BMCreateButton($createButtonReq, $credentials);
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
			exit;
		}

		if ($createButtonResponse->Ack == 'Failure')
		{
			// Log error here
			//print_r($createButtonResponse); die;

			// Throw exception
			throw new Exception($createButtonResponse->Errors[0]->LongMessage);
		}

		//print_r($createButtonResponse);

		return $createButtonResponse->Website;
	}


	/* ------------------------ Postback functions ---------------------------- */

	/**
	 * Set postback info ($_POST)
	 *
	 * @param 	array $_POST
	 * @return 	int associated transaction ID if $_POST data is valid, false otherwise
	 */
	public function setPostBack($postMessage)
	{
		$this->postMessage = $postMessage;
		$isValid = $this->validatePaypalMessage();

		if (!$isValid)
		{
			return false;
		}

		// Get transaction ID
		$customData = explode('-', $postMessage['custom']);
		//$token = $customData[0];
		$tId = $customData[1];
		return $tId;
	}

	/**
	 * Verify the payment -- make sure it matches the transaction awaiting payment
	 *
	 * @param 	object transaction info
	 * @return 	bool
	 */
	public function verifyPayment($tInfo)
	{
		// Compare amounts

		// PaypalPayment received
		$ppPayment = $this->postMessage['payment_gross'];

		// Transaction payment expected
		$tAmount = $tInfo->info->tiTotal;

		if ($tAmount == $ppPayment)
		{
			return true;
		}

		// Generate error message
		if ($tAmount > $ppPayment)
		{
			$moreLess = JText::_('COM_CART_POSTBACK_INCORRECT_AMOUNT_LESS');
		}
		else
		{
			$moreLess = JText::_('COM_CART_POSTBACK_INCORRECT_AMOUNT_MORE');
		}

		$errorMessage = sprintf(JText::_('COM_CART_POSTBACK_INCORRECT_AMOUNT_ERROR'), $ppPayment, $moreLess, $tAmount);

		// Set error
		$this->error->msg = $errorMessage;

		return false;
	}

	/**
	 * Get error
	 *
	 * @param 	void
	 * @return 	obj error
	 */
	public function getError()
	{
	 	if (empty($this->error))
		{
			return false;
		}
		else
		{
			return $this->error;
		}
	}

	/**
	 * Verify that the $_POST is an actual postback from PayPal
	 *
	 * @param 	void
	 * @return 	bool
	 */
	private function validatePaypalMessage()
	{
		$req = 'cmd=_notify-validate';
		foreach ($this->postMessage as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		// post back to PayPal system to validate
		$url = (!isset($this->postMessage['test_ipn'])) ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';

		$curl_result = '';
		$curl_err = '';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$curl_result = @curl_exec($ch);
		$curl_err = curl_error($ch);
		curl_close($ch);

		// we should get VERIFIED on success
		if (strpos($curl_result, "VERIFIED")!== false)
		{
			return true;
		}
		return false;
	}

}