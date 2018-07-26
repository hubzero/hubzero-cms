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

use Components\Cart\Models\Cart;
require_once Component::path('com_cart') . DS . 'models' . DS . 'Cart.php';

/**
 * Cart plugin for Payment: UPay
 */
class plgCartUpay extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Render payment options
	 *
	 * @param   object  $cart
	 * @param   object  $user
	 * @return  array
	 */
	public function onRenderPaymentOptions($cart, $user)
	{
		$view = $this->view('default', 'payment')
			->set('url', Route::url('index.php?option=com_cart&controller=checkout/confirm'));

		$payment = array();
		$payment['options'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'UPay');

		return $payment;
	}

	/**
	 * Post back
	 *
	 * @param   array  $postData
	 * @return  mixed
	 */
	public function onPostback($postData)
	{
		$provider = Request::getWord('provider', false, 'post');
		if ($provider != 'upay')
		{
			return false;
		}

		// get the transaction Id
		// Get transaction ID
		$tId = $postData['EXT_TRANS_ID'];

		$response = array();
		$response['status'] = 'ok';

		if (!$tId)
		{
			// Transaction id couldn't be extracted
			$response['status'] = 'error';
			$response['error'] = 'Postback did not have the valid transaction ID ';
		}

		$tInfo = Cart::getTransactionFacts($tId);

		if (!$tInfo)
		{
			// Transaction doesn't exist, set error
			$response['status'] = 'error';
			$response['error'] =  'Incoming payment for the transaction that does not exist: ' . $tId;
		}

		// Verify the postback
		// Verify the verification var
		if ($this->params->get('paymentValidationKey') != $postData['posting_key'])
		{
			$response['status'] = 'error';
			$response['error'] =  'Payment Validation Key does not match the configured key. Verification failed.';
		}

		// Verify the amount
		// Get expected payment
		$expectedPayment = $tInfo->info->tiTotal;
		$paymentReceived = $postData['pmt_amt'];

		if ($expectedPayment != $paymentReceived)
		{
			$response['status'] = 'error';
			$response['error'] =  'Payment received(' . $paymentReceived . ') does not match the expected payment(' . $expectedPayment . ')';
		}

		if ($response['status'] == 'ok')
		{
			$message = 'Transaction completed. ';
			$message .= 'Transaction ID: ' . $tId;
			$response['msg'] = $message;

			$response['tInfo'] = $tInfo;
			$response['payment'] = array('upay', false);
		}

		return $response;
	}

	/**
	 * Complete payment
	 *
	 * @param   object  $transaction
	 * @param   object  $user
	 * @return  mixed
	 */
	public function onSelectedPayment($transaction, $user)
	{
		$provider = Request::getWord('paymentProvider', false, 'post');
		if ($provider != 'upay')
		{
			return false;
		}

		$siteDetails = new \stdClass();
		$siteDetails->siteId = $this->params->get('paymentSiteId');

		$this->setTransactionDetails($transaction);

		// Payment selected mark transaction as awaiting payment
		Cart::updateTransactionStatus('awaiting payment', $transaction->info->tId);

		$view = $this->view('code', 'payment')
			->set('user', $user)
			->set('postUrl', $this->getPostURL())
			->set('transaction', $transaction)
			->set('siteDetails', $siteDetails)
			->set('transactionDetails', $this->transactionDetails);

		$payment = array();
		$payment['status'] = 'ok';
		$payment['paymentInfo'] = 'UPay payment provider';
		$payment['response'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'UPay');

		return $payment;
	}

	/**
	 * Get the Posting URL
	 *
	 * @return  string
	 */
	private function getPostURL()
	{
		if ($this->params->get('env') == 'live')
		{
			$url = 'https://secure.touchnet.com/C21261_upay/web/index.jsp';
		}
		else
		{
			$url = 'https://secure.touchnet.com:8443/C21261test_upay/web/index.jsp';
		}

		return $url;
	}

	/**
	 * Set transaction details
	 *
	 * @param   object  $transactionDetails
	 * @return  void
	 */
	private function setTransactionDetails($transactionDetails)
	{
		$hubName  = Config::get('sitename');

		$params = Component::params(Request::getCmd('option'));

		$this->options = new \stdClass();
		$this->options->transactionName = "$hubName online purchase";

		$this->transactionDetails = array();
		$this->transactionDetails['EXT_TRANS_ID'] = $transactionDetails->info->tId;
		$this->transactionDetails['EXT_TRANS_ID_LABEL'] = $this->options->transactionName;
		$this->transactionDetails['AMT'] = $transactionDetails->info->tiTotal;

		$base = $this->params->get('validationKey') . $this->transactionDetails['EXT_TRANS_ID'] . $this->transactionDetails['AMT'];
		$validationKey =  base64_encode(md5($base, true));

		$this->transactionDetails['VALIDATION_KEY'] = $validationKey;
		$this->transactionDetails['SUCCESS_LINK'] = Request::base() . 'cart' . DS . 'order' . DS . 'complete?tId=' .
			$transactionDetails->token . '-' . $transactionDetails->info->tId;
	}

	/**
	 * Complete payment
	 *
	 * @param   object  $provider
	 * @return  mixed
	 */
	public function onComplete($provider)
	{
		if (!Request::getString('UPAY_SITE_ID', '', 'get'))
		{
			return false;
		}

		$response = array();
		$response['verificationVar'] = 'tId';
		return $response;
	}
}
