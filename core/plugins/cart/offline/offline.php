<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Cart\Models\Cart;

require_once Component::path('com_cart') . DS . 'models' . DS . 'Cart.php';

/**
 * Cart plugin for Payment: Offline
 */
class plgCartOffline extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $transaction
	 * @param   object  $user
	 * @return  array
	 */
	public function onRenderPaymentOptions($transaction, $user)
	{
		$view = $this->view('default', 'payment')
			->set('url', Route::url('index.php?option=com_cart&controller=checkout/confirm'));

		$payment = array();
		$payment['options'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'Offline');

		return $payment;
	}

	/**
	 * On selected payment
	 *
	 * @param   object  $transaction
	 * @param   object  $user
	 * @return  bool
	 */
	public function onSelectedPayment($transaction, $user)
	{
		$provider = Request::getWord('paymentProvider', false, 'post');

		if ($provider != 'offline')
		{
			return false;
		}

		// Payment selected mark transaction as awaiting payment
		Cart::updateTransactionStatus('awaiting payment', $transaction->info->tId);

		$view = $this->view('code', 'payment')
			->set('user', $user)
			->set('transaction', $transaction);

		$payment = array();
		$payment['status'] = 'ok';
		$payment['paymentInfo'] = 'Offline testing payment provider';
		$payment['response'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'Offline');

		return $payment;
	}

	/**
	 * Return a list of filters that can be applied
	 *
	 * @param   object  $transaction
	 * @param   object  $user
	 * @return  bool
	 */
	public function onPostback($postData)
	{
		$provider = Request::getWord('provider', false, 'post');
		if ($provider != 'offline')
		{
			return false;
		}

		// get the transaction Id
		// Get transaction ID
		$customData = explode('-', $postData['custom']);
		$tId = $customData[1];

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

		// Some verification would take place here...

		if ($response['status'] == 'ok')
		{
			$message = 'Transaction completed. ';
			$message .= 'Transaction ID: ' . $tId;
			$response['msg'] = $message;

			$response['tInfo'] = $tInfo;
			$response['payment'] = array('Offline', false);
		}

		return $response;
	}

	/**
	 * On complete
	 *
	 * @param   string  $provider
	 * @return  mixed
	 */
	public function onComplete($provider)
	{
		if ($provider != 'offline')
		{
			return false;
		}

		$response = array();
		$response['verificationVar'] = 'custom';
		return $response;
	}
}
