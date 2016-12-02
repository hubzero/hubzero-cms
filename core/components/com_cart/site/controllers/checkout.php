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
use Components\Storefront\Models\Warehouse;
use Components\Storefront\Models\Product;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'CurrentCart.php';
require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';

/**
 * Courses controller class
 */
class Checkout extends ComponentController
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
			$this->_task = 'checkout';
			$this->registerTask('__default', $this->_task);
		}

		$this->juser = User::getInstance();

		// Check if they're logged in
		if ($this->juser->get('guest'))
		{
			$this->login('Please login to continue');
			return;
		}

		parent::execute();
	}

	public function displayTask()
	{

		App::abort(404, Lang::txt('COM_CART_NO_CHECKOUT_STEP_FOUND'));
		return;

	}

	/**
	 * Checkout entry point. Begin checkout -- check, create, or update transaction and redirect to the next step
	 *
	 * @param	void
	 * @return	void
	 */
	public function checkoutTask()
	{
		$cart = new CurrentCart();

		// This is a starting point in checkout process. All existing transactions for this user
		// have to be removed and a new one has to be created.
		// Do the final check of the cart

		// Get the latest synced cart info, it will also enable cart syncing that was turned off before
		// (this should also kill old transaction info)
		$cart->getCartInfo(true);

		// Check if there are messages to display
		if ($cart->hasMessages())
		{
			// redirect back to cart to display all messages
			//$redirect_url = Route::url('index.php?option=' . 'com_cart');
			App::redirect(
					Route::url('index.php?option=' . $this->_option)
			);
		}

		// Check/create/update transaction here
		$transactionInfo = $cart->getTransaction();

		// Redirect to cart if no transaction items (no cart items)
		if (!$transactionInfo)
		{
			$cart->redirect('home');
		}

		// Redirect to the final step if transaction is ready to go to the payment phase (???)
		$cart->redirect('continue');

		//$this->printTransaction($transactionInfo);
	}

	/**
	 * Continue checkout -- decides where to take the checkout process next
	 *
	 * @param	void
	 * @return	void
	 */
	public function continueTask()
	{
		/* Decide where to go next */
		$cart = new CurrentCart();

		// Check/create/update transaction here
		$transactionInfo = $cart->getTransaction();

		// Redirect to cart if no transaction items (no cart items)
		if (!$transactionInfo)
		{
			$cart->redirect('checkout');
		}

		// Redirect to the next step
		$nextStep = $cart->getNextCheckoutStep()->step;
		$cart->redirect($nextStep);
	}

	/**
	 * User agreement acceptance
	 *
	 * @return     void
	 */
	public function eulaTask()
	{
		$cart = new CurrentCart();

		$errors = array();

		$transaction = $cart->liftTransaction();

		if (!$transaction)
		{
			// Redirect to cart if transaction cannot be lifted
			$cart->redirect('home');
		}

		$nextStep = $cart->getNextCheckoutStep();

		// Double check that the current step is indeed EULA, redirect if needed
		if ($nextStep->step != 'eula')
		{
			$cart->redirect($nextStep->step);
		}

		// Get the SKU id of the item being displayed (from meta)
		$sId = $nextStep->meta;

		// Get the eula text for the product or EULA (EULAs are assigned to products, and if needed, SKUS)

		$warehouse = new Warehouse();
		$skuInfo = $warehouse->getSkuInfo($sId);

		$this->view->productInfo = $skuInfo['info'];

		// Check if there is SKU EULA set
		if (!empty($skuInfo['meta']['eula']))
		{
			$productEula = $skuInfo['meta']['eula'];
		}
		else
		{
			// Get product id
			$pId = $skuInfo['info']->pId;
			// Get EULA
			$productEula = Product::getMetaValue($pId, 'eula');
		}

		$this->view->productEula = $productEula;

		$eulaSubmitted = Request::getVar('submitEula', false, 'post');

		if ($eulaSubmitted)
		{
			// check if agreed
			$eulaAccepted = Request::getVar('acceptEula', false, 'post');

			if (!$eulaAccepted)
			{
				$errors[] = array(Lang::txt('COM_CART_MUST_ACCEPT_EULA'), 'error');
			}
			else {
				// Save item's meta
				$itemMeta = new \stdClass();
				$itemMeta->eulaAccepted = true;
				//$itemMeta->machinesInstalled = 'n/a';
				$cart->setTransactionItemMeta($sId, json_encode($itemMeta));

				// Mark this step as completed
				$cart->setStepStatus('eula', $sId);

				// All good, continue
				$nextStep = $cart->getNextCheckoutStep()->step;
				$cart->redirect($nextStep);
			}
		}

		if (!empty($errors))
		{
			$this->view->notifications = $errors;
		}

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt('EULA')
			);
		}

		$this->view->display();
	}

	/**
	 * Notes comments
	 *
	 * @return     void
	 */
	public function notesTask()
	{
		$cart = new CurrentCart();
		$transaction = $cart->liftTransaction();

		if (!$transaction)
		{
			// Redirect to cart if transaction cannot be lifted
			$cart->redirect('home');
		}

		$cart->setStepStatus('notes', '', false);
		$nextStep = $cart->getNextCheckoutStep();

		// Double check that the current step is indeed EULA, redirect if needed
		if ($nextStep->step != 'notes')
		{
			$cart->redirect($nextStep->step);
		}

		$notesSubmitted = Request::getVar('submitNotes', false, 'post');

		if ($notesSubmitted)
		{
			$notes = Request::getVar('notes', false, 'post');

			// Save order's notes
			$cart->setTransactionNotes($notes);

			// Mark this step as completed
			$cart->setStepStatus('notes');

			// All good, continue
			$nextStep = $cart->getNextCheckoutStep()->step;
			$cart->redirect($nextStep);
		}

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt('Notes')
			);
		}

		$this->view->display();
	}

	/**
	 * Shipping step of the checkout
	 *
	 * @return     void
	 */
	public function shippingTask()
	{
		$cart = new CurrentCart();

		// initialize address set var
		$addressSet = false;

		$params = $this->getParams(array('action', 'saId'));

		$errors = array();

		if (!empty($params) && !empty($params->action) && !empty($params->saId) && $params->action == 'select')
		{
			try
			{
				$this->selectSavedShippingAddress($params->saId, $cart);
				$addressSet = true;
			}
			catch (\Exception $e)
			{
				$errors[] = array($e->getMessage(), 'error');
			}
		}

		$transaction = $cart->liftTransaction();

		if (!$transaction)
		{
			// Redirect to cart if transaction cannot be lifted
			$cart->redirect('home');
		}

		// It is OK to come back to shipping and change the address
		$cart->setStepStatus('shipping', '', false);
		$nextStep = $cart->getNextCheckoutStep();

		// Double check that the current step is indeed shipping, redirect if needed
		if ($nextStep->step != 'shipping')
		{
			$cart->redirect($nextStep->step);
		}

		// handle non-ajax form submit
		$shippingInfoSubmitted = Request::getVar('submitShippingInfo', false, 'post');

		if ($shippingInfoSubmitted)
		{
			$res = $cart->setTransactionShippingInfo();

			if ($res->status)
			{
				$addressSet = true;
			}
			else
			{
				foreach ($res->errors as $error)
				{
					$errors[] = array($error, 'error');
				}
			}
		}

		// Calculate shipping charge
		if ($addressSet)
		{
			// TODO Calculate shipping
			$shippingCost = 22.22;
			$cart->setTransactionShippingCost($shippingCost);

			$cart->setStepStatus('shipping');

			$nextStep = $cart->getNextCheckoutStep()->step;
			$cart->redirect($nextStep);
		}

		if (!empty($errors))
		{
			$this->view->notifications = $errors;
		}

		if (Pathway::count() <= 0)
		{
			Pathway::append(
					Lang::txt(strtoupper($this->_option)),
					'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
					Lang::txt('Shipping')
			);
		}

		$savedShippingAddresses = $cart->getSavedShippingAddresses($this->juser->id);
		$this->view->savedShippingAddresses = $savedShippingAddresses;
		$this->view->display();
	}

	/**
	 * Select saved shipping address
	 *
	 * @return     void
	 */
	private function selectSavedShippingAddress($saId, $cart)
	{
		// ajax vs non-ajax
		$cart->setSavedShippingAddress($saId);
	}

	/**
	 * Summary step of the checkout
	 *
	 * @return     void
	 */
	public function summaryTask()
	{
		$cart = new CurrentCart();

		$transaction = $cart->liftTransaction();

		if (!$transaction)
		{
			$cart->redirect('home');
		}

		// Generate security token
		$token = $cart->getToken();

		// Check if there are any steps missing. Redirect if needed
		$nextStep = $cart->getNextCheckoutStep()->step;

		if ($nextStep != 'summary')
		{
			$cart->redirect($nextStep);
		}

		$cart->finalizeTransaction();

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt('Review your order')
			);
		}

		$this->view->token = $token;
		$this->view->transactionItems = $transaction->items;
		$this->view->transactionInfo = $transaction->info;
		$this->view->display();
	}

	/**
	 * Confirm step of the checkout. Should be a pass-through page for JS-enabled browsers, requires a form submission to the payment gateway
	 *
	 * @return     void
	 */
	public function confirmTask()
	{
		$cart = new CurrentCart();

		$transaction = $cart->liftTransaction();
		if (!$transaction)
		{
			$cart->redirect('home');
		}

		// Get security token
		$transaction->token = $cart->getToken();

		// Check if there are any steps missing. Redirect if needed
		$nextStep = $cart->getNextCheckoutStep()->step;

		if ($nextStep != 'summary')
		{
			$cart->redirect($nextStep);
		}

		// Final step here before payment
		Cart::updateTransactionStatus('awaiting payment', $transaction->info->tId);

		// Generate payment code
		$params = Component::params(Request::getVar('option'));
		$paymentGatewayProivder = $params->get('paymentProvider');

		require_once dirname(dirname(__DIR__)) . DS . 'lib' . DS . 'payment' . DS . 'PaymentDispatcher.php';
		$paymentDispatcher = new \PaymentDispatcher($paymentGatewayProivder);
		$pay = $paymentDispatcher->getPaymentProvider();

		$pay->setTransactionDetails($transaction);

		$error = false;
		try
		{
			$paymentCode = $pay->getPaymentCode();
			$this->view->paymentCode = $paymentCode;
		}
		catch (\Exception $e)
		{
			$error = $e->getMessage();
		}

		if (!empty($error))
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Redirect to login page
	 *
	 * @return void
	 */
	private function login($message = '')
	{
		$return = base64_encode($_SERVER['REQUEST_URI']);
		App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				$message,
				'warning'
		);
		return;
	}

	/**
	 * Print transacttion info
	 *
	 * @return     void
	 */
	private function printTransaction($t)
	{
		echo '<div class="cartSection">';
		foreach ($t as $k => $v)
		{
			echo '<p>';
			echo $v['info']->pName;
			echo ' @ ';
			echo $v['info']->sPrice;
			echo ' x';
			echo $v['transactionInfo']->qty;
			echo ' @ ';
			echo $v['transactionInfo']->tiPrice;
			echo '</p>';
		}
		echo '</div>';
	}
}

