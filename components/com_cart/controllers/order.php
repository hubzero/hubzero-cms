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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_COMPONENT . DS . 'lib' . DS . 'cartmessenger' . DS . 'CartMessenger.php');
include_once(JPATH_COMPONENT . DS . 'models' . DS . 'cart.php');

/**
 * Cart order controller class
 */
class CartControllerOrder extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = JRequest::getVar('task', '');

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
		$params =  JComponentHelper::getParams(JRequest::getVar('option'));
		$paymentGatewayProivder = $params->get('paymentProvider');

		// Get the transaction ID variable name to pull from URL
		include_once(JPATH_COMPONENT . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
		$verificationVar = PaymentDispatcher::getTransactionIdVerificationVarName($paymentGatewayProivder);

		// Check the GET values passed
		$customVar = JRequest::getVar($verificationVar, '');

		$tId = false;
		if (strstr($customVar, '-'))
		{
			$customData = explode('-', $customVar);
			$token = $customData[0];
			$tId = $customData[1];
		}
		else
		{
			$token = $customVar;
		}
		//print_r($tId); die;

		// Lookup the order
		$cart = new CartModelCart(NULL, true);

		// Verify token
		if (!$token || !$cart->verifyToken($token, $tId))
		{
			die('Error processing your order. Failed to verify security token.');
		}

		// Get transaction info
		$tInfo = $cart->getTransactionFacts($tId);
		//print_r($tId); die;
		//print_r($tInfo);

		if(empty($tInfo->info->tStatus) || $tInfo->info->tiCustomerStatus != 'unconfirmed' || $tInfo->info->tStatus != 'completed') {
			die('Error processing your order.');
			//JError::raiseError(404, JText::_('Error processing transaction.'));
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart');
			$app  =  JFactory::getApplication();
			$app->redirect($redirect_url);
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
		// Get the current active trancsaction
		$cart = new CartModelCart();

		$transaction = $cart->liftTransaction();
		//print_r($transaction); die;

		if (!$transaction)
		{
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart');
			$app  =  JFactory::getApplication();
			$app->redirect($redirect_url);
		}

		// get security token (Parameter 0)
		$token = JRequest::getVar('p0');

		if (!$token || !$cart->verifyToken($token))
		{
			die('Error processing your order. Bad security token.');
		}

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
			$params =  JComponentHelper::getParams(JRequest::getVar('option'));
			// Get payment provider
			$paymentGatewayProivder = $params->get('paymentProvider');

			include_once(JPATH_COMPONENT . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
			$verificationVar = PaymentDispatcher::getTransactionIdVerificationVarName($paymentGatewayProivder);

			// redirect to thank you page
			$redirect_url = JRoute::_('index.php?option=' . 'com_cart') . '/order/complete/' .
							'?' . $verificationVar . '=' . $token . '-' . $transaction->info->tId;
			$app  =  JFactory::getApplication();
			//echo 'redirect';
			$app->redirect($redirect_url);
		}
	}

	/**
	 * Payment gateway postback make sure everything checks out and complete transaction
	 *
	 * @return     void
	 */
	public function postbackTask()
	{
		$params =  JComponentHelper::getParams(JRequest::getVar('option'));

		if (empty($_POST))
		{
			JError::raiseError(404, JText::_('Page not found'));
		}

		// Get payment provider
		$paymentGatewayProivder = $params->get('paymentProvider');

		include_once(JPATH_COMPONENT . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
		$paymentDispatcher = new PaymentDispatcher($paymentGatewayProivder);
		$pay = $paymentDispatcher->getPaymentProvider();

		$postBackTransactionId = $pay->setPostBack($_POST);

		if (!$postBackTransactionId)
		{
			// error postback not verified (TO DO)
			die('not verified');
		}

		// TESTING ***********************
		//$postBackTransactionId = 1;

		// Initialize static cart
		$cart = new CartModelCart(NULL, true);

		// Initialize logger
		$logger = new CartMessenger('Payment Postback');

		// Get transaction info
		$tInfo = $cart->getTransactionFacts($postBackTransactionId);
		//print_r($tInfo); die;

		// Check if it exists
		if (!$tInfo)
		{
			// Transaction doesn't exist, log error
			$error = 'Incoming payment for the transaction that does not exist: ' . $postBackTransactionId;

			$logger->setMessage($error);
			$logger->setPostback($_POST);
			$logger->log(LoggingLevel::ERROR);
			return false;

		}

		// Check if it can be processed
		if ($tInfo->info->tStatus != 'awaiting payment')
		{
			// Transaction cannot be processed, log error
			$error = 'Transaction cannot be processed: ' . $postBackTransactionId . '. Current transaction status is "' . $tInfo->info->tStatus . '"';

			$logger->setMessage($error);
			$logger->setPostback($_POST);
			$logger->log(LoggingLevel::ERROR);
			return false;
		}

		// verify payment
		if (!$pay->verifyPayment($tInfo))
		{
			// Since payment has not been verified get error.
			$error = $pay->getError()->msg;

			$error .= ' Transaction ID: ' . $postBackTransactionId;

			// Log error
			$logger->setMessage($error);
			$logger->setPostback($_POST);
			$logger->log(LoggingLevel::ERROR);

			// Handle error
			$cart->handleTransactionError($postBackTransactionId, $error);

			return false;
		}

		// No error -- mark the transaction as paid
		$message = 'Transaction completed';
		$message .= ' Transaction ID: ' . $postBackTransactionId;

		$logger->setMessage($message);
		$logger->setPostback($_POST);
		$logger->log(LoggingLevel::INFO);

		return($this->completeOrder($tInfo));

	}

	/**
	 * Complete transaction
	 *
	 * @return     void
	 */
	private function completeOrder($tInfo)
	{
		// Initialize static cart
		$cart = new CartModelCart(NULL, true);

		// Initialize logger
		$logger = new CartMessenger('Complete order');

		// Send emails to customer and admin
		$logger->emailOrderComplete($tInfo->info);

		return $cart->completeTransaction($tInfo);
	}

}
