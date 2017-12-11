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
		// Get the plugins working
		$provider = Request::getVar('provider', '', 'get');
		$pay = Event::trigger('cart.onComplete', array($provider));

		//print_r($provider); die;

		$verificationVar = false;

		foreach ($pay as $response)
		{
			if ($response)
			{
				$verificationVar = $response['verificationVar'];

				break;
			}
		}

		if (empty($verificationVar))
		{
			$verificationVar = 'custom';
		}

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

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
			Pathway::append(
				Lang::txt('Order complete'),
				'index.php?option=' . $this->_option
			);
		}

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
			// redirect to thank you page
			$redirect_url = Route::url('index.php?option=' . 'com_cart') . '/order/complete/' .
				'?custom=' . $token . '-' . $transaction->info->tId;
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
		// some payment providers (Dummy, PayPal will do the postback, need to call the plugins and handle appropriately)

		$test = false;
		// TESTING ***********************
		if ($test)
		{
			$postBackTransactionId = 116;
		}

		$params = Component::params(Request::getVar('option'));

		if (empty($_POST) && !$test)
		{
			App::abort(404, Lang::txt('Page not found'));
		}

		// Initialize logger
		$logger = new \CartMessenger('Payment Postback');

		//echo 'postbackResponse'; die;
		//print_r($_POST); die;

		// Get the plugins working
		$pay = Event::trigger('cart.onPostback', array($_POST, User::getRoot()));

		foreach ($pay as $response)
		{
			if ($response)
			{
				//print_r($response['payment']); die;

				if ($response['status'] == 'ok')
				{
					$logger->setMessage($response['msg']);
					$logger->setPostback($_POST);
					$logger->log(\LoggingLevel::INFO);

					if (empty($response['payment']))
					{
						$response['payment'] = false;
					}

					$this->completeOrder($response['tInfo'], $response['payment']);
				}
				else {
					$logger->setMessage($response['error']);
					$logger->setPostback($_POST);
					$logger->log(\LoggingLevel::ERROR);
				}

				break;
			}
		}
	}

	/**
	 * Complete transaction
	 *
	 * @return     void
	 */
	private function completeOrder($tInfo, $paymentInfo = false)
	{
		// Handle transaction according to items handlers
		Cart::completeTransaction($tInfo, $paymentInfo);

		// Send emails to customer and admin
		$logger = new \CartMessenger('Complete order');
		$logger->emailOrderComplete($tInfo);

		return true;
	}

}
