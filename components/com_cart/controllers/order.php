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

ximport('Hubzero_Controller');
include_once(JPATH_COMPONENT . DS . 'lib' . DS . 'cartmessenger' . DS . 'CartMessenger.php');

/**
 * Courses controller class
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
		$params = &JComponentHelper::getParams(JRequest::getVar('option'));
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
		ximport('Hubzero_Cart_Cart');
		$cart = new Hubzero_Cart(NULL, true);
		
		// Verify token
		if (!$token || !$cart->verifyToken($token, $tId))
		{
			die('Error processing your order. Failed to verify security token.');
		}
		
		// Get transaction info 
		$tInfo = $cart->getTransactionFacts($tId);	
		//print_r($tId); die;
		//print_r($tInfo); die;
				
		if(empty($tInfo->info->tStatus) || $tInfo->info->tiCustomerStatus != 'unconfirmed' || $tInfo->info->tStatus != 'completed') {
			die('Error processing your order???');
			//JError::raiseError(404, JText::_('Error processing transaction.'));
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart');
			$app  = & JFactory::getApplication();
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
		ximport('Hubzero_Cart_Cart');
		$cart = new Hubzero_Cart();
				
		$transaction = $cart->liftTransaction();
		//print_r($transaction); die;
		
		if (!$transaction)
		{
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart');
			$app  = & JFactory::getApplication();
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
			$params = &JComponentHelper::getParams(JRequest::getVar('option'));
			// Get payment provider
			$paymentGatewayProivder = $params->get('paymentProvider');
			
			include_once(JPATH_COMPONENT . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php');
			$verificationVar = PaymentDispatcher::getTransactionIdVerificationVarName($paymentGatewayProivder);
			
			// redirect to thank you page
			$redirect_url = JRoute::_('index.php?option=' . 'com_cart') . '/order/complete/' . 
							'?' . $verificationVar . '=' . $token . '-' . $transaction->info->tId;
			$app  = & JFactory::getApplication();
			//echo 'redirect';
			$app->redirect($redirect_url);	
		}
	}
	
	/**
	 * Payment gateway postback check if everything checks out and complete transaction
	 * 
	 * @return     void
	 */
	public function postbackTask() 
	{
		
		$params = &JComponentHelper::getParams(JRequest::getVar('option'));
		
		// TESTING ***********************
		//$_POST = (unserialize('a:44:{s:8:"mc_gross";s:6:"110.00";s:22:"protection_eligibility";s:10:"Ineligible";s:14:"address_status";s:9:"confirmed";s:8:"payer_id";s:13:"VWBF5XUFK7EQY";s:3:"tax";s:4:"0.00";s:14:"address_street";s:17:"6739 wilkins Ave.";s:12:"payment_date";s:25:"11:37:01 Feb 14, 2013 PST";s:14:"payment_status";s:7:"Pending";s:7:"charset";s:12:"windows-1252";s:11:"address_zip";s:5:"15217";s:10:"first_name";s:4:"Ilya";s:6:"mc_fee";s:4:"3.49";s:20:"address_country_code";s:2:"US";s:12:"address_name";s:6:"Shunko";s:14:"notify_version";s:3:"3.7";s:6:"custom";s:4:"1233";s:12:"payer_status";s:8:"verified";s:8:"business";s:32:"XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";s:15:"address_country";s:13:"United States";s:12:"address_city";s:10:"Pittsburgh";s:8:"quantity";s:1:"1";s:11:"verify_sign";s:56:"AyXNPaYtSWIZ.Gvz3LBMXetylZ68A4PHsmhBNd7XvoTnI6O.jq6A3POn";s:11:"payer_email";s:15:"ilya@shunko.com";s:4:"memo";s:8:"Hui vam!";s:6:"txn_id";s:17:"9PJ32545H7925942E";s:12:"payment_type";s:7:"instant";s:19:"payer_business_name";s:6:"Shunko";s:9:"last_name";s:6:"Shunko";s:13:"address_state";s:2:"PA";s:14:"receiver_email";s:32:"XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";s:11:"payment_fee";s:4:"3.49";s:11:"receiver_id";s:13:"77EPVLSQQAXUW";s:14:"pending_reason";s:13:"paymentreview";s:8:"txn_type";s:10:"web_accept";s:9:"item_name";s:21:"myhub online purchase";s:11:"mc_currency";s:3:"USD";s:11:"item_number";s:0:"";s:17:"residence_country";s:2:"US";s:8:"test_ipn";s:1:"1";s:15:"handling_amount";s:4:"0.00";s:19:"transaction_subject";s:4:"1233";s:13:"payment_gross";s:6:"110.00";s:8:"shipping";s:4:"0.00";s:12:"ipn_track_id";s:13:"3dac021dad97c";}'));
				
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
		ximport('Hubzero_Cart_Cart');
		$cart = new Hubzero_Cart(NULL, true);
		
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
		ximport('Hubzero_Cart_Cart');
		$cart = new Hubzero_Cart(NULL, true);
		
		// Initialize logger
		$logger = new CartMessenger('Complete order');
		
		// Send emails to customer and admin
		$logger->emailOrderComplete($tInfo->info);
		
		return $cart->completeTransaction($tInfo);
	}
	
}