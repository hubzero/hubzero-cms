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
use Components\Cart\Models\CurrentCart;

require_once PATH_CORE . DS. 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php';

/**
 * Cart plugin for Payment: Purdue University Account Number
 */
class plgCartPuaccount extends \Hubzero\Plugin\Plugin
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
			->set('user', $user)
			->set('transaction', $transaction)
			->set('url', Route::url('index.php?option=com_cart&controller=checkout/confirm'));

		$payment = array();
		$payment['options'] = $view->loadTemplate();
		$payment['title'] = $this->params->get('title', 'Purdue University Account Number');

		return $payment;
	}

	/**
	 * Return a list of filters that can be applied
	 *
	 * @param   object  $transaction
	 * @param   object  $user
	 * @return  bool
	 */
	public function onSelectedPayment($transaction, $user)
	{
		$provider = Request::getWord('paymentProvider', false, 'post');
		if ($provider != 'puaccount')
		{
			return false;
		}

		$parts = array(
			Request::getInt('account_part_1', 0, 'post'),
			Request::getInt('account_part_2', 0, 'post'),
			Request::getInt('account_part_3', 0, 'post')
		);

		// Check the number and make sure it looks legit
		$verify = $this->checkNumber($parts);

		$response = array();
		$response['status'] = $verify['status'];

		if ($verify['status'] == 'ok')
		{
			// all good, show the final confirmation page
			$view = $this->view('confirm', 'payment')
				->set('user', $user)
				->set('parts', $parts)
				->set('transaction', $transaction);
			$response['response'] = $view->loadTemplate();
		}
		else
		{
			// errors, show the form
			$view = $this->view('form', 'payment')
				->set('user', $user)
				->set('parts', $parts)
				->set('error', $verify['error'])
				->set('transaction', $transaction);
			$response['response'] = $view->loadTemplate();
		}

		$response['paymentInfo'] = 'PU Account# ' . $parts[0] . '-' . $parts[1] . '-' . $parts[2];

		//print_r($response); die;

		return $response;
	}

	public function onProcessPayment($transaction, $user)
	{
		$provider = Request::getWord('paymentProvider', false, 'post');
		if ($provider != 'puaccount')
		{
			return false;
		}

		$parts = array(
			Request::getInt('account_part_1', 0, 'post'),
			Request::getInt('account_part_2', 0, 'post'),
			Request::getInt('account_part_3', 0, 'post')
		);

		// Check the number and make sure it looks legit (again)
		$verify = $this->checkNumber($parts);

		// Here is some additional processing would take place if needed (API call to the payment processor) -- none in this case

		$response = array();
		$response['status'] = $verify['status'];

		if ($verify['status'] == 'ok')
		{
			$tInfo = Cart::getTransactionFacts($transaction->info->tId);
			/// complete transaction
			require_once PATH_CORE . DS. 'components' . DS . 'com_cart' . DS . 'lib' . DS . 'cartmessenger' . DS . 'CartMessenger.php';

			// Initialize logger
			$logger = new \CartMessenger('Payment complete');
			$logger->setMessage('Transaction completed. Transaction ID ' . $transaction->info->tId);
			$logger->setPostback($parts);
			$logger->log(\LoggingLevel::INFO);

			// Payment info
			$payment = array('PUaccount', 'Account# ' . $parts[0] . '-' . $parts[1] . '-' . $parts[2]);

			//print_r($payment); die;

			// Handle transaction according to items handlers
			Cart::completeTransaction($tInfo, $payment);

			// Send emails to customer and admin
			$logger = new \CartMessenger('Complete order');
			$logger->emailOrderComplete($tInfo);

			// redirect to thank you page
			// Generate security token
			$token = Cart::generateSecurityToken($transaction->info->tId);

			$redirect_url = Route::url('index.php?option=' . 'com_cart') . 'order/complete/' .
				'?provider=puaccount&custom=' . $token . '-' . $transaction->info->tId;
			App::redirect(
				$redirect_url
			);
		}
		else
		{
			// errors, show the form
			$view = $this->view('form', 'payment')
				->set('user', $user)
				->set('parts', $parts)
				->set('error', $verify['error'])
				->set('transaction', $transaction);
			$response['response'] = $view->loadTemplate();
		}

		//print_r($response); die;

		return $response;
	}

	public function onComplete($provider)
	{
		if ($provider != 'puaccount')
		{
			return false;
		}

		$response = array();
		$response['verificationVar'] = 'custom';
		return $response;
	}

	private function checkNumber($parts) {
		$response = array();
		$response['status'] = 'ok';

		foreach ($parts as $part)
		{
			if (!is_numeric($part))
			{
				$response['status'] = 'error';
				$response['error'] = 'The number must be numeric';
			}

			if (strlen($part) < 8)
			{
				$response['status'] = 'error';
				$response['error'] = 'Please check the account number';
			}
		}

		return $response;
	}
}
