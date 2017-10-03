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
use Components\Store\Models\Store;
use Components\Store\Models\Cart;
use Components\Store\Models\Order;
use Components\Store\Models\Orderitem;
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
	 * @return  void
	 */
	public function execute()
	{
		// Get the component parameters
		$this->infolink = Component::params('com_answers')->get('infolink', '/kb/points/');

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
			case '1':
				$out = Lang::txt('COM_STORE_MERCHANDISE');
			break;
			case '2':
				$out = Lang::txt('COM_STORE_SERVICE');
			break;
			default:
				$out = Lang::txt('COM_STORE_MERCHANDISE');
			break;
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
		$filters = array(
			'limit'  => Request::getInt('limit', Config::get('list_limit')),
			'start'  => Request::getInt('limitstart', 0),
			'sortby' => Request::getVar('sortby', '')
		);

		// Get the most recent store items
		$rows = Store::all()
			->limit($filters['limit'])
			->start($filters['start'])
			->rows();

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('rows', $rows)
			->set('title', $this->_title)
			->set('infolink', $this->infolink)
			->display();
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
			return $this->loginTask();
		}

		$this->view->setLayout('cart');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->set('infolink', $this->infolink);
		$msg = '';

		// Check if economy functions are unavailable
		$upconfig = Component::params('com_members');

		if (!$upconfig->get('bankAccounts'))
		{
			$this->view
				->set('funds', 0)
				->set('rows', 0)
				->set('cost', 0)
				->set('msg', $msg)
				->setError(Lang::txt('COM_STORE_MSG_STORE_CLOSED'))
				->display();
			return;
		}

		// Incoming
		$action = Request::getVar('action', '');
		$id = Request::getInt('item', 0);

		// Check if item exists
		$purchasetype = '';

		if ($id)
		{
			$iteminfo = Store::oneOrFail($id);

			$purchasetype = $this->_getPurchaseType($iteminfo->type);
		}

		switch ($action)
		{
			case 'add':
				// Check if item is already there, then update quantity or save new
				$item = Cart::oneByItemAndUser($id, User::get('id'));

				if (!$item->get('id') && $id)
				{
					$item->set('itemid', $id);
					$item->set('uid', User::get('id'));
					$item->set('type', $purchasetype);
					$item->set('added', \Date::toSql());
				}

				$item->set('quantity', $item->get('quantity', 0) + 1);

				// store new content
				if (!$item->save())
				{
					App::abort(500, $item->getError());
				}

				$msg = Lang::txt('COM_STORE_MSG_ADDED_TO_CART');
			break;

			case 'update':
				// Update quantaties and selections
				$items = Cart::allByUser(User::get('id'));

				if ($items)
				{
					$posteditems = array_map('trim', $_POST);
					foreach ($items as $item)
					{
						if ($item->get('type') != 2)
						{
							if (isset($posteditems['size' . $item->itemid]))
							{
								$item->selections->set('size', $posteditems['size' . $item->itemid]);
							}
							if (isset($posteditems['color' . $item->itemid]))
							{
								$item->selections->set('color', $posteditems['color' . $item->itemid]);
							}
							if (isset($posteditems['num' . $item->itemid]))
							{
								$item->set('quantity', $posteditems['num' . $item->itemid]);
							}

							$item->set('selections', $item->selections->toString());
							$item->save();
						}
					}
				}
			break;

			case 'remove':
				// Update quantaties and selections
				if ($id)
				{
					$item = Cart::oneByItemAndUser($id, User::get('id'));
					$item->destroy();
				}
			break;

			case 'empty':
				// Empty all
				Cart::destroyByUser(User::get('id'));
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
		$funds   = abs($funds);

		// Get cart items
		$items = Cart::allByUser(User::get('id'));

		// Calculate total
		$cost = 0;

		foreach ($items as $item)
		{
			$cost += ($item->get('quantity', 1) * $item->item->get('price'));
		}

		// Output HTML
		$this->view
			->set('funds', $funds)
			->set('rows', $items)
			->set('cost', $cost)
			->set('msg', $msg)
			->set('id', $id)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Go through the checkout/payment process
	 *
	 * @return  void
	 */
	public function checkoutTask()
	{
		// Check authorization
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Update quantaties and selections
		$items = Cart::allByUser(User::get('id'));

		if ($items)
		{
			$posteditems = array_map('trim', $_POST);

			foreach ($items as $item)
			{
				// Clean-up unavailable items
				if (!$item->item->available)
				{
					$item->destroy();
					continue;
				}

				if ($item->get('type') != 2)
				{
					if (isset($posteditems['size' . $item->itemid]))
					{
						$item->selections->set('size', $posteditems['size' . $item->itemid]);
					}
					if (isset($posteditems['color' . $item->itemid]))
					{
						$item->selections->set('color', $posteditems['color' . $item->itemid]);
					}
					if (isset($posteditems['num' . $item->itemid]))
					{
						$item->set('quantity', $posteditems['num' . $item->itemid]);
					}

					$item->set('selections', $item->selections->toString());
					$item->save();
				}
			}
		}

		// Calculate total
		$cost = 0;

		foreach ($items as $item)
		{
			$cost += ($item->get('quantity', 1) * $item->item->get('price'));
		}

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$funds   = abs($funds);

		if ($cost > $funds)
		{
			return $this->cartTask();
		}

		// Output HTML
		$posted = array();

		$this->view
			->set('items', $items)
			->set('posted', $posted)
			->set('cost', $cost)
			->set('infolink', $this->infolink)
			->set('final', false)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Finalize the purchase process
	 *
	 * @return  void
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
			return $this->loginTask();
		}

		$now = \Date::toSql();

		$items = Cart::allByUser(User::get('id'));

		// Calculate total
		$cost = 0;

		foreach ($items as $item)
		{
			$cost += ($item->get('quantity', 1) * $item->item->get('price'));
		}

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$funds   = abs($funds);

		// Get cart items
		if (!$items->count() or $cost > $funds)
		{
			return $this->cartTask();
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
		$order = Order::blank();
		$order->set(array(
			'uid'     => User::get('id'),
			'total'   => $cost,
			'status'  => 0, // order placed
			'ordered' => $now,
			'email'   => $email,
			'details' => $details
		));

		// Store new content
		if (!$order->save())
		{
			App::abort(500, $order->getError());
		}

		// Get order ID
		$orderid = $order->get('id');

		if ($orderid)
		{
			// Transfer cart items to order
			foreach ($items as $itm)
			{
				$orderitem = Orderitem::blank();
				$orderitem->set(array(
					'uid'        => User::get('id'),
					'oid'        => $orderid,
					'itemid'     => $itm->itemid,
					'price'      => $itm->price,
					'quantity'   => $itm->quantity,
					'selections' => $itm->selections
				));

				// Save order item
				if (!$orderitem->save())
				{
					App::abort(500, $orderitem->getError());
				}
			}

			// Put the purchase amount on hold
			$BTL = new Teller(User::get('id'));
			$BTL->hold($order->total, Lang::txt('COM_STORE_BANKING_HOLD'), 'store', $orderid);

			$message = new \Hubzero\Mail\Message();
			$message->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_STORE_EMAIL_SUBJECT_NEW_ORDER', $orderid));
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
		Cart::destroyByUser(User::get('id'));

		if ($this->getError())
		{
			Notify::message($this->getError(), 'error');
		}
		else
		{
			Notify::message(Lang::txt('COM_STORE_SUCCESS_MESSAGE', $orderid), 'success');
		}

		App::redirect(Route::url('index.php?option=' . $this->_option));
	}

	/**
	 * Process the order
	 *
	 * @return  void
	 */
	public function processTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Check authorization
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Get cart items
		$items = Cart::allByUser(User::get('id'));

		// Calculate total
		$cost = 0;

		foreach ($items as $item)
		{
			$cost += ($item->get('quantity', 1) * $item->get('price'));
		}

		if (empty($items))
		{
			$this->setError(Lang::txt('COM_STORE_ERR_EMPTY_ORDER'));
		}

		// Check available user funds
		$BTL = new Teller(User::get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$funds   = abs($funds);

		if ($cost > $funds)
		{
			$this->setError(Lang::txt('COM_STORE_MSG_NO_FUNDS'));
		}

		// Get shipping info
		$posted = array_map('trim', $_POST);

		if (!$posted['name']
		 || !$posted['address']
		 || !$posted['country'])
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
			$this->view->set('final', true);
		}
		else
		{
			// Instantiate a new view
			$this->view->setLayout('checkout');
			$this->view->set('final', false);
		}

		// Output HTML
		$this->view
			->set('cost', $cost)
			->set('funds', $funds)
			->set('items', $items)
			->set('posted', $posted)
			->set('infolink', $this->infolink)
			->setErrors($this->getErrors())
			->display();
	}
}
