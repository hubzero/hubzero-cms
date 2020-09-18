<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'CurrentCart.php';
require_once dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'cartmessenger' . DS . 'CartMessenger.php';

use Components\Cart\Models\Cart;
use Components\Cart\Models\CurrentCart;
use Filesystem;
use Request;
use Route;
use Event;
use Lang;
use App;
use Components\Cart\Lib\CartMessenger\Cartmessenger as CartMessenger;

/**
 * Cart order controller class
 */
class Order extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = Request::getCmd('task', '');

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
	 * @return  void
	 */
	public function homeTask()
	{
		App::abort(404);
	}

	/**
	 * This is a redirect page where the customer ends up after payment is complete
	 *
	 * @return  void
	 */
	public function completeTask()
	{
		// Get the plugins working
		$provider = Request::getString('provider', '', 'get');
		$pay = Event::trigger('cart.onComplete', array($provider));

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
			$customVar = Request::getString($verificationVar, '');

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

			// Verify token
			if (!$token || !Cart::verifySecurityToken($token, $tId))
			{
				App::abort(500, 'Error processing your order. Failed to verify security token.');
			}
		}

		// Get transaction info
		$tInfo = Cart::getTransactionFacts($tId);

		if (empty($tInfo->info->tStatus) || $tInfo->info->tiCustomerStatus != 'unconfirmed' || $tInfo->info->tStatus != 'completed')
		{
			Notify::error('Error processing your order...');

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
	 * @return  void
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
		$token = Request::getString('p0');

		if (!$token || !$cart->verifyToken($token))
		{
			App::abort(500, 'Error processing your order. Bad security token.');
		}

		// Check if the order total is 0
		if ($transaction->info->tiTotal != 0)
		{
			App::abort(500, 'Cannot process transaction. Order total is not zero.');
		}

		// Check if the transaction's status is pending
		if ($transaction->info->tStatus != 'pending')
		{
			App::abort(500, 'Cannot process transaction. Transaction status is invalid.');
		}

		if ($this->completeOrder($transaction))
		{
			// redirect to thank you page
			$redirect_url = Route::url('index.php?option=' . 'com_cart') . '/order/complete/?custom=' . $token . '-' . $transaction->info->tId;

			App::redirect(
				$redirect_url
			);
		}
	}

	/**
	 * Payment gateway postback: make sure everything checks out and complete transaction
	 *
	 * @return  void
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

		$params = Component::params(Request::getCmd('option'));

		if (empty($_POST) && !$test)
		{
			App::abort(404, Lang::txt('Page not found'));
		}

		// Initialize logger
		$logger = new CartMessenger('Payment Postback');

		// Get the plugins working
		$pay = Event::trigger('cart.onPostback', array($_POST, User::getRoot()));

		foreach ($pay as $response)
		{
			if ($response)
			{
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
				else
				{
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
	 * @param   object   $tInfo
	 * @param   boolean  $paymentInfo
	 * @return  void
	 */
	private function completeOrder($tInfo, $paymentInfo = false)
	{
		// Handle transaction according to items handlers
		Cart::completeTransaction($tInfo, $paymentInfo);

		// Send emails to customer and admin
		$logger = new CartMessenger('Complete order');
		$logger->emailOrderComplete($tInfo);

		return true;
	}
}
