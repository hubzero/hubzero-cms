<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

use Request;
use User;
use Components\Cart\Models\Cart;
use Components\Cart\Models\CurrentCart;
use Components\Storefront\Models\Warehouse;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'CurrentCart.php';

/**
 * Cart controller class
 */
class Orders extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return     void
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

		// Check if they're logged in
		if (User::isGuest())
		{
			Request::setVar('task', 'login');
		}

		parent::execute();
	}

	/**
	 * Display default page
	 *
	 * @return     void
	 */
	public function homeTask()
	{
		// Incoming
		$this->view->filters = array(
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
		);

		$cart = new CurrentCart();

		// Get all completed transactions count
		$this->view->total = $cart->getTransactions(array('count' => true));

		// Get all completed transactions
		$transactions = $cart->getTransactions($this->view->filters);

		// Get transactions' info
		if ($transactions)
		{
			foreach ($transactions as $transaction)
			{
				$transactionInfo = Cart::getTransactionInfo($transaction->tId);

				// Figure out if the items int the transactions are still available
				$tItems = unserialize($transactionInfo->tiItems);

				foreach ($tItems as $item)
				{
					// Check if the product is still available
					$warehouse = new Warehouse();
					$skuInfo = $warehouse->getSkuInfo($item['info']->sId, false);
					$item['info']->available = true;
					if (!$skuInfo)
					{
						// product no longer available
						$item['info']->available = false;
					}
				}

				$transactionInfo->tiItems = $tItems;
				$transaction->tInfo = $transactionInfo;
			}
		}

		$this->view->transactions = $transactions;

		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
			Pathway::append(
				Lang::txt('COM_CART_ORDERS'),
				'index.php?option=' . $this->_option
			);
		}

		$this->view->display();
	}

	/**
	 * Redirect to the login page with the return set
	 *
	 * @return     void
	 */
	public function loginTask()
	{
		$rtrn = Request::getString('REQUEST_URI', Route::url('index.php?option=' . $this->_controller), 'server');
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
		return;
	}
}
