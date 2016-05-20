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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cart\Site\Controllers;

use Components\Cart\Models\Cart;
use Components\Cart\Models\CurrentCart;
use Filesystem;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'CurrentCart.php';
require_once dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'cartmessenger' . DS . 'CartMessenger.php';

/**
 * Cart order controller class
 */
class Order extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = Request::getVar('task', '');

		if (empty($this->_task))
		{
			$this->_task = 'home';
			$this->registerTask('__default', $this->_task);
		}

		parent::execute();
	}

	/**
	 * Default task
	 *
	 * @return     void
	 */
	public function homeTask()
	{
		die('no direct access');
	}

	/**
	 * This is a redirect page where the customer ends up after payment is complete
	 *
	 * @return     void
	 */
	public function completeTask()
	{
		// Get payment provider
		$params = Component::params(Request::getVar('option'));
		$paymentGatewayProivder = $params->get('paymentProvider');

		// Get the transaction ID variable name to pull from URL
		require_once(dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
		$verificationVar = \PaymentDispatcher::getTransactionIdVerificationVarName($paymentGatewayProivder);

		if ($verificationVar)
		{
			// Check the GET values passed
			$customVar = Request::getVar($verificationVar, '');

			$tId = false;
			if (strstr($customVar, '-')) {
				$customData = explode('-', $customVar);
				$token = $customData[0];
				$tId = $customData[1];
			} else {
				$token = $customVar;
			}

			// Verify token
			if (!$token || !Cart::verifySecurityToken($token, $tId))
			{
				die('Error processing your order. Failed to verify security token.');
			}
		}

		// Get transaction info
		$tInfo =  Cart::getTransactionFacts($tId);
		//print_r($tId); die;
		//print_r($tInfo);die;

		if (empty($tInfo->info->tStatus) || $tInfo->info->tiCustomerStatus != 'unconfirmed' || $tInfo->info->tStatus != 'completed')
		{
			die('Error processing your order...');
			App::redirect(
					Route::url('index.php?option=' . $this->_option)
			);
		}

		// Transaction ok
		// Reset the lookup to prevent displaying the page multiple times
		//$cart->updateTransactionCustomerStatus('confirmed', $tId);

		// Display message
		$this->view->transactionInfo = $tInfo->info;
		$this->view->display();
	}

	/**
	 * Place order (for orders with zero balances)
	 *
	 * @return     void
	 */
	public function placeTask()
	{
		// Get the current active transaction
		$cart = new CurrentCart();

		$transaction = $cart->liftTransaction();

		if (!$transaction)
		{
			App::redirect(
				Route::url('index.php?option=' . 'com_cart')
			);
		}

		// get security token (Parameter 0)
		$token = Request::getVar('p0');

		if (!$token || !$cart->verifyToken($token))
		{
			die('Error processing your order. Bad security token.');
		}

		//print_r($transaction); die;

		// Check if the order total is 0
		if ($transaction->info->tiTotal != 0)
		{
			die('Cannot process transaction. Order total is not zero.');
		}

		// Check if the transaction's status is pending
		if ($transaction->info->tStatus != 'pending')
		{
			die('Cannot process transaction. Transaction status is invalid.');
		}
		//print_r($transaction); die;

		if ($this->completeOrder($transaction))
		{
			// Get the transaction ID variable name to pull from URL
			$params = Component::params(Request::getVar('option'));
			// Get payment provider
			$paymentGatewayProivder = $params->get('paymentProvider');

			require_once(dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
			$verificationVar = \PaymentDispatcher::getTransactionIdVerificationVarName($paymentGatewayProivder);

			// redirect to thank you page
			$redirect_url = Route::url('index.php?option=' . 'com_cart') . '/order/complete/' .
				'?' . $verificationVar . '=' . $token . '-' . $transaction->info->tId;
			App::redirect(
					$redirect_url
			);
		}
	}

	/**
	 * Payment gateway postback: make sure everything checks out and complete transaction
	 *
	 * @return     void
	 */
	public function postbackTask()
	{
		$test = false;
		// TESTING ***********************
		if ($test)
		{
			$postBackTransactionId = 215;
		}

		$params = Component::params(Request::getVar('option'));

		if (empty($_POST) && !$test)
		{
			App::abort(404, Lang::txt('Page not found'));
		}

		// Initialize logger
		$logger = new \CartMessenger('Payment Postback');

		// Get payment provider
		if (!$test)
		{
			$paymentGatewayProivder = $params->get('paymentProvider');

			require_once(dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
			$paymentDispatcher = new \PaymentDispatcher($paymentGatewayProivder);
			$pay = $paymentDispatcher->getPaymentProvider();

			// Extract the transaction id from postback information
			$postBackTransactionId = $pay->setPostBack($_POST);

			if (!$postBackTransactionId) {
				// Transaction id couldn't be extracted
				$error = 'Post back did not have the valid transaction ID ';

				$logger->setMessage($error);
				$logger->setPostback($_POST);
				$logger->log(\LoggingLevel::ERROR);
				return false;
			}
		}
		// test
		else
		{
			require_once(dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
			$paymentDispatcher = new \PaymentDispatcher('DUMMY AUTO PAYMENT');
			$pay = $paymentDispatcher->getPaymentProvider();
		}

		// Get transaction info
		$tInfo =  Cart::getTransactionFacts($postBackTransactionId);
		//print_r($tInfo); die;

		// Check if it exists
		if (!$tInfo)
		{
			// Transaction doesn't exist, log error
			$error = 'Incoming payment for the transaction that does not exist: ' . $postBackTransactionId;

			$logger->setMessage($error);
			$logger->setPostback($_POST);
			$logger->log(\LoggingLevel::ERROR);
			return false;
		}

		// Check if the transaction can be processed (it can only be processed if the transaction is awaiting payment)
		if ($tInfo->info->tStatus != 'awaiting payment')
		{
			// Transaction cannot be processed, log error
			$error = 'Transaction cannot be processed: ' . $postBackTransactionId . '. Current transaction status is "' . $tInfo->info->tStatus . '"';

			$logger->setMessage($error);
			$logger->setPostback($_POST);
			$logger->log(\LoggingLevel::ERROR);
			return false;
		}

		// Get the action. Post back will normally be triggered on payment success, but can also be the cancel post back
		$postBackAction = $pay->getPostBackAction();

		if ($postBackAction == 'payment' || $test)
		{
			// verify payment
			if (!$test && !$pay->verifyPayment($tInfo))
			{
				// Payment has not been verified, get verification error
				$error = $pay->getError()->msg;

				$error .= ' Transaction ID: ' . $postBackTransactionId;

				// Log error
				$logger->setMessage($error);
				$logger->setPostback($_POST);
				$logger->log(\LoggingLevel::ERROR);

				// Handle error
				Cart::handleTransactionError($postBackTransactionId, $error);

				return false;
			}

			// No error
			$message = 'Transaction completed. ';
			$message .= 'Transaction ID: ' . $postBackTransactionId;

			// Log info
			if (!$test)
			{
				$logger->setMessage($message);
				$logger->setPostback($_POST);
				$logger->log(\LoggingLevel::INFO);
			}

			// Finalize order -- whatever needs to be done
			$this->completeOrder($tInfo);
		}
		elseif ($postBackAction == 'cancel')
		{
			// Cancel transaction
			$message = 'Transaction cancelled. ';
			$message .= 'Transaction ID: ' . $postBackTransactionId;

			// Log info
			if (!$test)
			{
				$logger->setMessage($message);
				$logger->setPostback($_POST);
				$logger->log(\LoggingLevel::INFO);
			}

			// Release the transaction
			Cart::releaseTransaction($postBackTransactionId);
		}
		else
		{
			// No supported action, log error
			$error = 'Post back action is invalid: ' . $postBackAction;

			$logger->setMessage($error);
			$logger->setPostback($_POST);
			$logger->log(\LoggingLevel::ERROR);
			return false;
		}
	}

	/**
	 * Complete transaction
	 *
	 * @return     void
	 */
	private function completeOrder($tInfo)
	{
		// Handle transaction according to items handlers
		Cart::completeTransaction($tInfo);

		// Send emails to customer and admin
		$logger = new \CartMessenger('Complete order');
		$logger->emailOrderComplete($tInfo->info);

		return true;
	}

}
