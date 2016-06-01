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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Store\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Utility\Sanitize;
use Hubzero\Bank\Teller;
use Components\Store\Tables\Store;
use Components\Store\Tables\Cart;
use Components\Store\Tables\Order;
use Components\Store\Tables\OrderItem;
use Exception;
use Component;
use Pathway;
use Request;
use Config;
use Route;
use Event;
use Lang;
use User;
use App;

/**
 * Controller class for the store
 */
class Shop extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the component parameters
		$aconfig = Component::params('com_answers');
		$this->infolink = $aconfig->get('infolink', '/kb/points/');

		parent::execute();
	}

	/**
	 * Build the breadcrumbs
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			switch ($this->_task)
			{
				case 'finalize':
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_CART'),
						'index.php?option=' . $this->_option . '&task=cart'
					);
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_CHECKOUT'),
						'index.php?option=' . $this->_option . '&task=checkout'
					);
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);
				break;
				case 'process':
					Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_CART'),
						'index.php?option=' . $this->_option . '&task=cart'
					);
				break;
				default:
					/*Pathway::append(
						Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);*/
				break;
			}
		}
	}

	/**
	 * Build and set the document title
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		$this->view->title = Lang::txt(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'display')
		{
			$this->view->title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		App::get('document')->setTitle($this->view->title);
	}

	/**
	 * Return proper text string
	 *
	 * @param   string  $num  The type of item being purchased
	 * @return  string
	 */
	private function _getPurchaseType($num)
	{
		switch ($num)
		{
			case '1': $out = Lang::txt('COM_STORE_MERCHANDISE'); break;
			case '2': $out = Lang::txt('COM_STORE_SERVICE');     break;
			default:  $out = Lang::txt('COM_STORE_MERCHANDISE'); break;
		}
		return $out;
	}

	/**
	 * Validate an email address
	 *
	 * @param   string   $email  Email address
	 * @return  boolean  True if valid email address
	 */
	private function _checkValidEmail($email)
	{
		if (preg_match("#^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$#i", $email))
		{
			return true;
		}
		return false;
	}

	/**
	 * Redirect to login form
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller), 'server');

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
	}

	/**
	 * Display items for sale
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$this->view->filters = array(
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
			'sortby' => Request::getVar('sortby', '')
		);

		// Get the most recent store items
		$obj = new Store($this->database);
		$this->view->rows = $obj->getItems('retrieve', $this->view->filters, $this->config);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view->title = $this->_title;
		$this->view->infolink = $this->infolink;

		$this->view->display();
	}

	/**
	 * Display the items in a user's shopping cart
	 *
	 * @return  void
	 */
	public function cartTask()
	{
		// Need to login to view cart
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		$this->view->setLayout('cart');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->infolink = $this->infolink;
		$this->view->msg = '';

		// Check if economy functions are unavailable
		$upconfig = Component::params('com_members');
		if (!$upconfig->get('bankAccounts'))
		{
			$this->view->rows  = 0;
			$this->view->funds = 0;
			$this->view->cost  = 0;
			$this->view->setError(Lang::txt('COM_STORE_MSG_STORE_CLOSED'));
			$this->view->display();
			return;
		}

		// Incoming
		$this->view->action = Request::getVar('action', '');
		$this->view->id = Request::getInt('item', 0);

		// Check if item exists
		$purchasetype = '';
		if ($this->view->id)
		{
			$objStore = new Store($this->database);
			$iteminfo = $objStore->getInfo($this->view->id);
			if (!$iteminfo)
			{
				$this->view->id = 0;
			}
			else
			{
				$purchasetype = $this->_getPurchaseType($iteminfo[0]->type);
			}
		}

		// Get cart object
		$item = new Cart($this->database);

		switch ($this->view->action)
		{
			case 'add':
				// Check if item is already there, then update quantity or save new
				$found = $item->checkCartItem($this->view->id, User::get('id'));

				if (!$found && $this->view->id)
				{
					$item->itemid = $this->view->id;
					$item->uid = User::get('id');
					$item->type = $purchasetype;
					$item->added = \Date::toSql();
					$item->quantity = 1;
					$item->selections = '';

					// store new content
					if (!$item->store())
					{
						throw new Exception($item->getError(), 500);
					}

					$this->view->msg = Lang::txt('COM_STORE_MSG_ADDED_TO_CART');
				}
			break;

			case 'update':
				// Update quantaties and selections
				$item->saveCart(array_map('trim', $_POST), User::get('id'));
			break;

			case 'remove':
				// Update quantaties and selections
				if ($this->view->id)
				{
					$item->deleteCartItem($this->view->id, User::get('id'));
				}
			break;

			case 'empty':
				// Empty all
				$item->deleteCartItem('', User::get('id'), 'all');
			break;

			default:
				// Do nothing
			break;
		}

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$this->view->funds = ($funds > 0) ? $funds : 0;

		// Calculate total
		$this->view->cost = $item->getCartItems(User::get('id'), 'cost');

		// Get cart items
		$this->view->rows = $item->getCartItems(User::get('id'));

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Go through the checkout/payment process
	 *
	 * @return     void
	 */
	public function checkoutTask()
	{
		// Check authorization
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->infolink = $this->infolink;
		$this->view->final = false;

		// Get cart object
		$item = new Cart($this->database);

		// Update quantaties and selections
		$item->saveCart(array_map('trim', $_POST), User::get('id'));

		// Calculate total
		$this->view->cost = $item->getCartItems(User::get('id'), 'cost');

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$this->view->funds = ($funds > 0) ? $funds : '0';

		if ($this->view->cost > $this->view->funds)
		{
			$this->cartTask();
			return;
		}

		// Get cart items
		$this->view->items = $item->getCartItems(User::get('id'));

		// Clean-up unavailable items
		$item->deleteUnavail(User::get('id'), $this->view->items);

		// Updated item list
		$this->view->items = $item->getCartItems(User::get('id'));

		// Output HTML
		$this->view->xprofile = \Hubzero\User::getInstance();
		$this->view->posted = array();

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Finalize the purchase process
	 *
	 * @return     void
	 */
	public function finalizeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Check authorization
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		$now = \Date::toSql();

		// Get cart object
		$item = new Cart($this->database);

		// Calculate total
		$cost = $item->getCartItems(User::get('id'),'cost');

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds = $balance - $credit;
		$funds = ($funds > 0) ? $funds : '0';

		// Get cart items
		$items = $item->getCartItems(User::get('id'));
		if (!$items or $cost > $funds)
		{
			$this->cartTask();
			return;
		}

		// Get shipping info
		$shipping = array_map('trim', $_POST);

		// make sure email address is valid
		$email = \Hubzero\Utility\Validate::email($shipping['email']) ? $shipping['email'] : User::get('email');

		// Format posted info
		$details  = Lang::txt('COM_STORE_SHIP_TO') . ':' . "\r\n";
		$details .= $shipping['name'] . "\r\n";
		$details .= Sanitize::stripAll($shipping['address']) . "\r\n";
		$details .= Lang::txt('COM_STORE_COUNTRY') . ': ' . $shipping['country'] . "\r\n";
		$details .= '----------------------------------------------------------' . "\r\n";
		$details .= Lang::txt('COM_STORE_CONTACT') . ': ' . "\r\n";
		if ($shipping['phone'])
		{
			$details .= $shipping['phone'] . "\r\n";
		}
		$details .= $email . "\r\n";
		$details .= '----------------------------------------------------------' . "\r\n";
		$details .= Lang::txt('COM_STORE_DETAILS') . ': ';
		$details .= ($shipping['comments']) ? "\r\n" . (Sanitize::stripAll($shipping['comments'])) : 'N/A';

		// Register a new order
		$order = new Order($this->database);
		$order->uid     = User::get('id');
		$order->total   = $cost;
		$order->status  = '0'; // order placed
		$order->ordered = $now;
		$order->email   = $email;
		$order->details = $details;

		// Store new content
		if (!$order->store())
		{
			throw new Exception($order->getError(), 500);
		}

		// Get order ID
		$objO = new Order($this->database);
		$orderid = $objO->getOrderID(User::get('id'), $now);

		if ($orderid)
		{
			// Transfer cart items to order
			foreach ($items as $itm)
			{
				$orderitem = new OrderItem($this->database);
				$orderitem->uid        = User::get('id');
				$orderitem->oid        = $orderid;
				$orderitem->itemid     = $itm->itemid;
				$orderitem->price      = $itm->price;
				$orderitem->quantity   = $itm->quantity;
				$orderitem->selections = $itm->selections;

				// Save order item
				if (!$orderitem->store())
				{
					throw new Exception($orderitem->getError(), 500);
				}
			}

			// Put the purchase amount on hold
			$BTL = new Teller(User::get('id'));
			$BTL->hold($order->total, Lang::txt('COM_STORE_BANKING_HOLD'), 'store', $orderid);

			$message = new \Hubzero\Mail\Message();
			$message->setSubject(Config::get('sitename') . ' '
				. Lang::txt('COM_STORE_EMAIL_SUBJECT_NEW_ORDER', $orderid));
			$message->addFrom(
				Config::get('mailfrom'),
				Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option))
			);

			// Plain text email
			$eview = new \Hubzero\Mail\View(array(
				'name'   => 'emails',
				'layout' => 'confirmation_plain'
			));
			$eview->option     = $this->_option;
			$eview->controller = $this->_controller;
			$eview->orderid    = $orderid;
			$eview->cost       = $cost;
			$eview->shipping   = $shipping;
			$eview->details    = $details;
			$eview->items      = $items;

			$plain = $eview->loadTemplate(false);
			$plain = str_replace("\n", "\r\n", $plain);

			$message->addPart($plain, 'text/plain');

			// HTML email
			$eview->setLayout('confirmation_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			$message->addPart($html, 'text/html');

			// Send e-mail
			$message->setTo(array(User::get('email')));
			$message->send();
		}

		// Empty cart
		$item->deleteCartItem('', User::get('id'), 'all');

		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error');
		}
		else
		{
			\Notify::message(Lang::txt('COM_STORE_SUCCESS_MESSAGE', $orderid), 'success');
		}

		App::redirect(Route::url('index.php?option=' . $this->_option));
		return;
	}

	/**
	 * Process the order
	 *
	 * @return     void
	 */
	public function processTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check authorization
		if (User::isGuest())
		{
			$this->loginTask();
			return;
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Get cart object
		$item = new Cart($this->database);

		// Calculate total
		$cost = $item->getCartItems(User::get('id'), 'cost');

		if (!$cost)
		{
			$this->setError(Lang::txt('COM_STORE_ERR_EMPTY_ORDER'));
		}

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds = $balance - $credit;
		$funds = ($funds > 0) ? $funds : '0';

		if ($cost > $funds)
		{
			$this->setError(Lang::txt('COM_STORE_MSG_NO_FUNDS'));
		}

		// Get cart items
		$items = $item->getCartItems(User::get('id'));

		// Get shipping info
		$this->view->posted = array_map('trim', $_POST);

		if (!$this->view->posted['name']
		 || !$this->view->posted['address']
		 || !$this->view->posted['country'])
		{
			$this->setError(Lang::txt('COM_STORE_ERR_BLANK_FIELDS'));
		}

		// Incoming
		$action = Request::getVar('action', '');

		// Output HTML
		if (!$this->getError() && $action != 'change')
		{
			// Instantiate a new view
			$this->view->setLayout('finalize');
			$this->view->final = true;
		}
		else
		{
			// Instantiate a new view
			$this->view->setLayout('checkout');
			$this->view->final = false;
		}

		// Output HTML
		$this->view->cost = $cost;
		$this->view->funds = $funds;
		$this->view->items = $items;
		$this->view->infolink = $this->infolink;
		$this->view->xprofile = User::getInstance();

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}
}

