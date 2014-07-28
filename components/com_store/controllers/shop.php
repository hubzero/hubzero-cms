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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Controller class for the store
 */
class StoreControllerShop extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the component parameters
		$aconfig = JComponentHelper::getParams('com_answers');
		$this->infolink = $aconfig->get('infolink', '/kb/points/');

		parent::execute();
	}

	/**
	 * Build the breadcrumbs
	 *
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			switch ($this->_task)
			{
				case 'finalize':
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_CART'),
						'index.php?option=' . $this->_option . '&task=cart'
					);
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_CHECKOUT'),
						'index.php?option=' . $this->_option . '&task=checkout'
					);
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);
				break;
				case 'process':
					$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_CART'),
						'index.php?option=' . $this->_option . '&task=cart'
					);
				break;
				default:
					/*$pathway->addItem(
						JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
						'index.php?option=' . $this->_option . '&task=' . $this->_task
					);*/
				break;
			}
		}
	}

	/**
	 * Build and set the document title
	 *
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$this->view->title = JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'display')
		{
			$this->view->title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->view->title);
	}

	/**
	 * Return proper text string
	 *
	 * @param      string $num The type of item being purchased
	 * @return     void
	 */
	private function _getPurchaseType($num)
	{
		switch ($num)
		{
			case '1': $out = JText::_('COM_STORE_MERCHANDISE'); break;
			case '2': $out = JText::_('COM_STORE_SERVICE');     break;
			default:  $out = JText::_('COM_STORE_MERCHANDISE'); break;
		}
		return $out;
	}

	/**
	 * Validate an email address
	 *
	 * @param      string  $email Email address
	 * @return     boolean True if valid email address
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
	 * @return     void
	 */
	public function loginTask()
	{
		$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller), 'server');

		$this->setRedirect(
			JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
	}

	/**
	 * Display items for sale
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get configuration
		$jconfig = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']  = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['sortby'] = JRequest::getVar('sortby', '');

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

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Display the items in a user's shopping cart
	 *
	 * @return     void
	 */
	public function cartTask()
	{
		// Need to login to view cart
		if ($this->juser->get('guest'))
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
		$upconfig = JComponentHelper::getParams('com_members');
		if (!$upconfig->get('bankAccounts'))
		{
			$this->view->juser = $this->juser;
			$this->view->rows  = 0;
			$this->view->funds = 0;
			$this->view->cost  = 0;
			$this->view->setError(JText::_('COM_STORE_MSG_STORE_CLOSED'));
			$this->view->display();
			return;
		}

		// Incoming
		$this->view->action = JRequest::getVar('action', '');
		$this->view->id = JRequest::getInt('item', 0);

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
				$found = $item->checkCartItem($this->view->id, $this->juser->get('id'));

				if (!$found && $this->view->id)
				{
					$item->itemid = $this->view->id;
					$item->uid = $this->juser->get('id');
					$item->type = $purchasetype;
					$item->added = JFactory::getDate()->toSql();
					$item->quantity = 1;
					$item->selections = '';

					// store new content
					if (!$item->store())
					{
						JError::raiseError(500, $item->getError());
						return;
					}

					$this->view->msg = JText::_('COM_STORE_MSG_ADDED_TO_CART');
				}
			break;

			case 'update':
				// Update quantaties and selections
				$item->saveCart(array_map('trim', $_POST), $this->juser->get('id'));
			break;

			case 'remove':
				// Update quantaties and selections
				if ($this->view->id)
				{
					$item->deleteCartItem($this->view->id, $this->juser->get('id'));
				}
			break;

			case 'empty':
				// Empty all
				$item->deleteCartItem('', $this->juser->get('id'), 'all');
			break;

			default:
				// Do nothing
			break;
		}

		// Check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds   = $balance - $credit;
		$this->view->funds = ($funds > 0) ? $funds : 0;

		// Calculate total
		$this->view->cost = $item->getCartItems($this->juser->get('id'), 'cost');

		// Get cart items
		$this->view->rows = $item->getCartItems($this->juser->get('id'));

		// Output HTML
		$this->view->juser = $this->juser;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		if ($this->juser->get('guest'))
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
		$item->saveCart(array_map('trim', $_POST), $this->juser->get('id'));

		// Calculate total
		$this->view->cost = $item->getCartItems($this->juser->get('id'), 'cost');

		// Check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
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
		$this->view->items = $item->getCartItems($this->juser->get('id'));

		// Clean-up unavailable items
		$item->deleteUnavail($this->juser->get('id'), $this->view->items);

		// Updated item list
		$this->view->items = $item->getCartItems($this->juser->get('id'));

		// Output HTML
		$this->view->juser = $this->juser;
		$this->view->xprofile = new \Hubzero\User\Profile;
		$this->view->xprofile->load($this->juser->get('id'));
		$this->view->posted = array();

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
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
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Check authorization
		if ($this->juser->get('guest'))
		{
			$this->loginTask();
			return;
		}

		$now = JFactory::getDate()->toSql();

		// Get cart object
		$item = new Cart($this->database);

		// Calculate total
		$cost = $item->getCartItems($this->juser->get('id'),'cost');

		// Check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds = $balance - $credit;
		$funds = ($funds > 0) ? $funds : '0';

		// Get cart items
		$items = $item->getCartItems($this->juser->get('id'));
		if (!$items or $cost > $funds)
		{
			$this->cartTask();
			return;
		}

		// Get shipping info
		$shipping = array_map('trim',$_POST);

		// make sure email address is valid
		$validemail = $this->_checkValidEmail($shipping['email']);
		$email = ($validemail) ? $shipping['email'] : $this->juser->get('email');

		// Format posted info
		$details  = JText::_('COM_STORE_SHIP_TO') . ':' . "\r\n";
		$details .= $shipping['name'] . "\r\n";
		$details .= \Hubzero\Utility\Sanitize::stripAll($shipping['address']) . "\r\n";
		$details .= JText::_('COM_STORE_COUNTRY') . ': ' . $shipping['country'] . "\r\n";
		$details .= '----------------------------------------------------------' . "\r\n";
		$details .= JText::_('COM_STORE_CONTACT') . ': ' . "\r\n";
		if ($shipping['phone'])
		{
			$details .= $shipping['phone'] . "\r\n";
		}
		$details .= $email . "\r\n";
		$details .= '----------------------------------------------------------' . "\r\n";
		$details .= JText::_('COM_STORE_DETAILS').': ';
		$details .= ($shipping['comments']) ? "\r\n" . (\Hubzero\Utility\Sanitize::stripAll($shipping['comments'])) : 'N/A';

		// Register a new order
		$order = new Order($this->database);
		$order->uid     = $this->juser->get('id');
		$order->total   = $cost;
		$order->status  = '0'; // order placed
		$order->ordered = $now;
		$order->email   = $email;
		$order->details = $details;

		// Store new content
		if (!$order->store())
		{
			JError::raiseError(500, $order->getError());
			return;
		}

		// Get order ID
		$objO = new Order($this->database);
		$orderid = $objO->getOrderID($this->juser->get('id'), $now);

		if ($orderid)
		{
			// Transfer cart items to order
			foreach ($items as $itm)
			{
				$orderitem = new OrderItem($this->database);
				$orderitem->uid        = $this->juser->get('id');
				$orderitem->oid        = $orderid;
				$orderitem->itemid     = $itm->itemid;
				$orderitem->price      = $itm->price;
				$orderitem->quantity   = $itm->quantity;
				$orderitem->selections = $itm->selections;

				// Save order item
				if (!$orderitem->store())
				{
					JError::raiseError(500, $orderitem->getError());
					return;
				}
			}

			// Put the purchase amount on hold
			$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
			$BTL->hold($order->total, JText::_('COM_STORE_BANKING_HOLD'), 'store', $orderid);

			$jconfig = JFactory::getConfig();

			// Compose confirmation "from"
			$hub = array(
				'email' => $jconfig->getValue('config.mailfrom'),
				'name'  => $jconfig->getValue('config.sitename') . ' ' . JText::_(strtoupper($this->_option))
			);

			// Compose confirmation subject
			$subject = JText::_(strtoupper($this->_name)) . ': ' . JText::_('COM_STORE_ORDER') . ' #' . $orderid;

			// Compose confirmation message
			$eview = new \Hubzero\Component\View(array(
				'name'   => 'emails',
				'layout' => 'confirmation'
			));
			$eview->option = $this->_option;
			$eview->sitename = $jconfig->getValue('config.sitename');
			$eview->orderid = $orderid;
			$eview->cost = $cost;
			$eview->now = $now;
			$eview->details = $details;

			$message = $eview->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			// Send confirmation
			JPluginHelper::importPlugin('xmessage');
			$dispatcher = JDispatcher::getInstance();
			if (!$dispatcher->trigger('onSendMessage', array('store_notifications', $subject, $message, $hub, array($this->juser->get('id')), $this->_option)))
			{
				$this->setError(JText::_('COM_STORE_ERROR_MESSAGE_FAILED'));
			}
		}

		// Empty cart
		$item->deleteCartItem('', $this->juser->get('id'), 'all');

		// Instantiate a new view
		$this->view->setLayout('completed');

		$this->view->infolink = $this->infolink;

		// Output HTML
		$this->view->juser = $this->juser;
		$this->view->orderid = $orderid;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Process the order
	 *
	 * @return     void
	 */
	public function processTask()
	{
		// Check authorization
		if ($this->juser->get('guest'))
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
		$cost = $item->getCartItems($this->juser->get('id'), 'cost');

		if (!$cost)
		{
			$this->setError(JText::_('COM_STORE_ERR_EMPTY_ORDER'));
		}

		// Check available user funds
		$BTL = new \Hubzero\Bank\Teller($this->database, $this->juser->get('id'));
		$balance = $BTL->summary();
		$credit  = $BTL->credit_summary();
		$funds = $balance - $credit;
		$funds = ($funds > 0) ? $funds : '0';

		if ($cost > $funds)
		{
			$this->setError(JText::_('COM_STORE_MSG_NO_FUNDS'));
		}

		// Get cart items
		$items = $item->getCartItems($this->juser->get('id'));

		// Get shipping info
		$this->view->posted = array_map('trim', $_POST);

		if (!$this->view->posted['name']
		 || !$this->view->posted['address']
		 || !$this->view->posted['country'])
		{
			$this->setError(JText::_('COM_STORE_ERR_BLANK_FIELDS'));
		}

		// Incoming
		$action = JRequest::getVar('action', '');

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
		$this->view->juser = $this->juser;
		$this->view->xprofile = new \Hubzero\User\Profile;
		$this->view->xprofile->load($this->juser->get('id'));

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}
}

