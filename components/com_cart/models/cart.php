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
 * @package   Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Shopping cart
 */
class CartModelCart
{
	// Database instance
	var $db = NULL;

	// Cart ID
	var $crtId = NULL;

	// Session cart
	var $cart = NULL;

	// Syncing enabled?
	var $sync = true;

	// Debug moode
	var $debug = false;

	// Move to config (TODO)
	// Transaction time to live: TTL -- transaction active
	var $transactionTTL = 60;	// 1 hour
	// Transaction kill age -- age at which trancaction gets deleted forever
	var $transactionKillAge = 43200; // 30 days
	var $securitySalt = 'ERDCVcvk$sad!ccsso====++!w';

	/**
	 * Cart constructor
	 *
	 * @param int 	Cart ID -- optional. If not provided will try to locate an existing cart based either on user ID, session, or cookie -- otherwise it will create a new cart.
	 *				If crtId is provided it will attempt to load the cart requested.
	 * @param bool 	Wheter the cart is loaded in static mode
	 * @return void
	 */
	public function __construct($crtId = NULL, $staticMode = false)
	{
		// Initialize DB
		$this->_db = JFactory::getDBO();

		// Load language file
		JFactory::getLanguage()->load('com_cart');

		// Load current user cart, no specific cart requested
		if (!$crtId && !$staticMode)
		{
			// Get user
			$juser = JFactory::getUser();

			// get cart from session
			$cart = $this->liftSessionCart();

			// If no session cart, try to locate a cookie cart (only for not logged in users)
			if (!$cart && $juser->get('guest'))
			{
				$cart = $this->liftCookie();
			}

			// Check if there is a session or cookie cart
			if ($cart)
			{
				// If cart found and user is logged in, verify if the cart is linked to the user cart in the DB
				if (!$juser->get('guest'))
				{
					if (empty($this->cart->linked) || !$this->cart->linked)
					{
						// link carts if not linked (this should only happen when user logs in with a cart created while not logged in)
						// if linking fails create a new cart
						if (!$this->linkCarts())
						{
							$this->createCart();
						}
					}
				}
				// Make sure cart is marked as unlinked
				else
				{
					$this->cart->linked = false;
				}
			}
			// If no session, but user is logged in
			elseif (!$juser->get('guest'))
			{
				// lookup the saved cart in the DB and if found lift the cart
				if (!$this->liftUserCart($juser->id))
				{
					// No session, no DB cart -- create new cart
					$this->createCart();
				}
			}
			// No session, no cookie, no user -- create new cart
			else
			{
				$this->createCart();
			}
		}
		// Load specific cart
		elseif (!empty($crtId))
		{
			$this->setCart($crtId);
		}

	}

	/**
	 * Set syncing
	 *
	 * @param bool $mode
	 * @return void
	 */
	public function setSync($mode = true)
	{
		$this->sync = $mode;
	}

	/**
	 * Add SKU to cart
	 *
	 * @param SKU ID
	 * @param int Quantity
	 * @return void
	 */
	public function add($sId, $qty = 1)
	{
		$this->doItem($sId, 'add', $qty);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Update/set SKU in cart
	 *
	 * @param SKU ID
	 * @param int Quantity
	 * @return void
	 */
	public function update($sId, $qty = 1, $retainOldValue = false)
	{
		$this->doItem($sId, 'set', $qty, $retainOldValue);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Add SKU to cart
	 *
	 * @param SKU ID
	 * @param int Quantity
	 * @return void
	 */
	public function delete($sId, $retainOldValue = false)
	{
		$this->doItem($sId, 'set', 0);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Get session info about cart
	 *
	 * @param bool $updateDb Flag whether the sessinon cart should be synced with DB first
	 * @return array of items in the cart
	 */
	public function getCartInfo($sync = false)
	{
		if ($sync)
		{
			// Enable syncing just in case, since this is an explicit call to get a synced cart
			$this->sync = true;
			$this->syncSessionCart();
		}

		return $this->cart;
	}

	/**
	 * Check if there are any changes to cart items' inventory or pricing since last visit
	 * @param false
	 * @return bool
	 */
	public function cartChanged()
	{
		return(!empty($this->cart->hasChanges) && $this->cart->hasChanges);
	}

	/**
	 * Get any changes to cart items' inventory or pricing since last visit -- works like a flash variable -- gets messages once and then resets the state
	 * @param false
	 * @return array of change messages
	 */
	public function getCartChanges()
	{
		// Load cart items info
		$sql = "SELECT * FROM `#__cart_cart_items` crti WHERE crti.`crtId` = {$this->crtId}";
		$this->_db->setQuery($sql);
		$items = $this->_db->loadObjectList();

		// Initiate changes array
		$changes = array();

		$cartItems = $this->cart->items;
		//print_r($cartItems); die('+');

		foreach ($items as $item)
		{
			$itemName = false;
			if (!empty($cartItems[$item->sId]['info']->pName))
			{
				$itemName = $cartItems[$item->sId]['info']->pName;
			}
			if (!empty($cartItems[$item->sId]['options']))
			{
				foreach ($cartItems[$item->sId]['options'] as $option)
				{
					$itemName .= ', ' . $option;
				}
			}

			if ($item->crtiAvailable == 0)
			{
				if (!$itemName)
				{
					$itemName = $item->crtiName;
				}
				$changes[] = $itemName . ' is no longer available';

				// skip the rest
				continue;
			}

			if (!empty($item->crtiOldQty) && $item->crtiOldQty > $item->crtiQty)
			{
				if ($item->crtiQty)
				{
					$changes[] = $itemName . ' inventory reduced from ' . $item->crtiOldQty . ' to ' . $item->crtiQty;
				}
				else {
					$changes[] = $itemName . ' is no longer in stock';
				}
			}

			if (!empty($item->crtiOldPrice) && $item->crtiOldPrice != $item->crtiPrice)
			{
				$changes[] = $itemName . ' price changed from ' . $item->crtiOldPrice . ' to ' . $item->crtiPrice;
			}
		}

		// Reset changes flag
		$this->cart->hasChanges = false;

		// Reset all messages
		if (!empty($changes))
		{
			// - delete zero inventory items and unavailable skus
			$sql = "DELETE FROM `#__cart_cart_items` WHERE (`crtiQty` = 0  OR `crtiAvailable` = 0) AND `crtId` = {$this->crtId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			// clear old info since the message has already been displayed
			$sql = "UPDATE `#__cart_cart_items` SET `crtiOldQty` = NULL, `crtiOldPrice` = NULL WHERE `crtId` = {$this->crtId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			$this->updateSession();
			return $changes;
		}

		return false;
	}

	// Debug function
	public function printCartInfo()
	{
		$cart = $this->cart;

		echo '<div style="border:1px solid #ccc; margin: 30px 0; padding: 20px;">';
		echo '<h3>Cart info:</h3>';

		echo '<p><strong>Cart ID</strong>: ' . $this->crtId . '</p>';

		if (!empty($this->cart))
		{
			foreach ($this->cart as $key => $val)
			{
				if ($key != 'items')
				{
					echo '<p><strong>' . $key . '</strong>: ';
					echo $val;
					echo '</p>';
				}
			}
		}
		echo '</div>';
	}

	/**
	 * Redirect to cart different cart pages
	 *
	 * @param string Where to redirect
	 * @return void
	 */
	public function redirect($where)
	{
		if ($where == 'home')
		{
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart');
		}
		else
		{
			$redirect_url  = JRoute::_('index.php?option=' . 'com_cart') . '/checkout/' . $where;
		}

		$app  =  JFactory::getApplication();
		$app->redirect($redirect_url);
	}

	/**
	 * Determine the next step for checkout process
	 *
	 * Shipping (if shippable) -> Registration info (if needed) -> Summary page
	 *
	 * @param void
	 * @return string method name
	 */
	public function getNextCheckoutStep()
	{
		// Get DB steps for this transaction
		$sql = "SELECT `tsStep` FROM `#__cart_transaction_steps` ts WHERE ts.`tId` = {$this->cart->tId} AND ts.`tsStatus` < 1 ORDER BY tsId DESC";
		$this->_db->setQuery($sql);
		$nextStep = $this->_db->loadResult();

		// If all steps are completed go to confirm
		if (!$nextStep)
		{
			$nextStep = 'summary';
		}

		//echo $nextStep; die;
		return $nextStep;
	}

	/**
	 * Get steps for a transaction
	 *
	 * @param void
	 * @return string method name
	 */
	private function getCheckoutSteps()
	{
		// Get DB steps for this transaction
		$sql = "SELECT `tsStep` FROM `#__cart_transaction_steps` ts WHERE ts.`tId` = {$this->cart->tId} ORDER BY tsId DESC";
		$this->_db->setQuery($sql);
		$steps = $this->_db->loadResultArray();

		return $steps;
	}


	/* ------------------------------------------------------------ Transaction functions ----------------------------------------------------------- */

	/**
	 * Get existing or create a new transaction (if doesn't exist)
	 *
	 * @param void
	 * @return array of items in the transaction, FALSE on failed attempt
	 */
	public function getTransaction()
	{
		// Locate the existing transaction if possible
		if (empty($this->cart->tId))
		{
			// Create a transaction if no existing transaction found
			if (!$this->createTransaction())
			{
				return false;
			}
		}
		else
		{
			// $this->updateTransaction();
		}

		return $this->liftTransaction();
	}

	/**
	 * Check if transaction is still valid and get transaction details and items
	 *
	 * @param void
	 * @return array of items in the transaction or false on failed attempt
	 */
	public function liftTransaction()
	{
		// Get transaction info
		$tInfo = $this->getTransactionInfo();

		if (!$tInfo || $tInfo->tAge > $this->transactionKillAge)
		{
			// no transaction found
			return false;
		}

		// Only pending and released transactions can be lifted
		if ($tInfo->tStatus != 'pending' && $tInfo->tStatus != 'released')
		{
			return false;
		}

		//print_r($tInfo); die;

		// See if transaction is expired
		if ($tInfo->tAge > $this->transactionTTL)
		{
			// if transaction has not yet been processed as expired (status is still 'pending') release the transaction
			if ($tInfo->tStatus == 'pending')
			{
				$this->releaseTransaction($this->cart->tId);
			}

			// If expired see if it can be still processed
			if (!$this->rebuildTransaction())
			{
				return false;
			}
		}

		// Can be purchased -- get transaction items
		$transaction->items = $this->getTransactionItems($this->cart->tId);
		//print_r($transaction->items); die('^^');

		if (!empty($transaction->items))
		{
			// Calculate the vital numbers for the transaction
			$transactionTotalAmount = 0;

			foreach ($transaction->items as $transactionItem)
			{
				$transactionTotalAmount += ($transactionItem['transactionInfo']->tiPrice * $transactionItem['transactionInfo']->qty);
			}

			$tInfo->tTotalAmount = $transactionTotalAmount;
		}

		$transaction->info = $tInfo;
		$this->tInfo = $tInfo;

		return $transaction;
	}

	/**
	 * Get main transaction facts (total, other verification info)
	 *
	 * @param void
	 * @return array of items in the transaction or false on failed attempt
	 */
	public function getTransactionFacts($tId)
	{
		if (!is_numeric($tId))
		{
			return false;
		}

		$items = $this->getTransactionItems($tId);
		if (!$items)
		{
			return false;
		}

		// Can be purchased -- get transaction items
		$transaction->items = $items;

		/*
		// Calculate the vital numbers for the transaction
		$transactionTotalAmount = 0;
		foreach ($transaction->items as $transactionItem)
		{
			$transactionTotalAmount += ($transactionItem['transactionInfo']->tiPrice * $transactionItem['transactionInfo']->qty);
		}
		*/

		$tInfo = $this->getTransactionInfo($tId);
		// Overwrite subtotal
		//$tInfo->tiSubtotal = $transactionTotalAmount;

		// Calculate grand total
		$tInfo->tiTotalAmount = $tInfo->tiSubtotal + $tInfo->tiTax + $tInfo->tiShipping;

		$transaction->info = $tInfo;

		//print_r($transaction); die;

		return $transaction;
	}

	/**
	 * Releases locked transaction items back to inventory and marks the transaction status as 'released'
	 *
	 * @param int Transaction ID
	 * @return void
	 */
	public function releaseTransaction($tId)
	{
		// Check if the transaction can be released (status is pending)
		// Get info
		$sql = "SELECT t.`tStatus` FROM `#__cart_transactions` t WHERE t.`tId` = {$tId}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		if (!$this->_db->getNumRows())
		{
			return false;
		}

		$tStatus = $this->_db->loadResult();

		// Get transaction items
		$tItems = $this->getTransactionItems($tId);

		// Go through each item and return the quantity back to inventory if needed
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		if (!empty($tItems))
		{
			foreach ($tItems as $sId => $itemInfo)
			{
				$qty = $itemInfo['transactionInfo']->qty;
				$warehouse->updateInventory($sId, $qty, 'add');
			}
		}
		// update status
		$this->updateTransactionStatus('released');

		//die('releasing');
	}

	/**
	 * Checks if transaction shipping info is correct and saves it
	 *
	 * @param void (gets info from POST)
	 * @return array status and messages
	 */
	public function setTransactionShippingInfo()
	{
		$errors = array();
		// success by default
		$status = 1;

		// Check required fields
		$requiredFields = array('shippingToFirst', 'shippingToLast', 'shippingAddress', 'shippingCity', 'shippingState', 'shippingZip');

		foreach ($requiredFields as $field)
		{
			$fieldValue = JRequest::getVar($field, false, 'post');
			if (empty($fieldValue))
			{
				$errors[] = JText::_('COM_CART_FILL_REQUIRED_FIELDS');
				break;
			}
		}

		// Check values
		if (empty($errors) && !Cart_Helper::validZip(JRequest::getVar('shippingZip', false, 'post', 'string')))
		{
			$errors[] = JText::_('COM_CART_INCORRECT_ZIP');
		}

		if (empty($errors))
		{
			// save shipping info
			$shippingToFirst = Cart_Helper::escapeDb(JRequest::getVar('shippingToFirst', false, 'post', 'string'));
			$shippingToLast = Cart_Helper::escapeDb(JRequest::getVar('shippingToLast', false, 'post', 'string'));
			$shippingAddress = Cart_Helper::escapeDb(JRequest::getVar('shippingAddress', false, 'post', 'string'));
			$shippingCity = Cart_Helper::escapeDb(JRequest::getVar('shippingCity', false, 'post', 'string'));
			$shippingState = Cart_Helper::escapeDb(JRequest::getVar('shippingState', false, 'post', 'string'));
			$shippingZip = Cart_Helper::escapeDb(JRequest::getVar('shippingZip', false, 'post', 'string'));

			if ($this->debug)
			{
				echo '<br>saving transaction shipping info';
			}

			$sqlUpdateValues = "`tiShippingToFirst` = '{$shippingToFirst}', `tiShippingToLast` = '{$shippingToLast}',
								`tiShippingAddress` = '{$shippingAddress}', `tiShippingCity` = '{$shippingCity}',
								`tiShippingState` = '{$shippingState}', `tiShippingZip` = '{$shippingZip}'";

			$sql = "INSERT INTO `#__cart_transaction_info`
					SET `tId` = {$this->cart->tId}, {$sqlUpdateValues}
					ON DUPLICATE KEY UPDATE {$sqlUpdateValues}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			// mark shipping step as completed
			//$this->setStepStatus('shipping');

			$saveAddress = Cart_Helper::escapeDb(JRequest::getVar('saveAddress', false, 'post', 'string'));
			// Save the address for future use if requested
			if ($saveAddress)
			{
				$sqlUpdateValues = str_replace('tiShipping', 'sa', $sqlUpdateValues);

				// Get user
				$juser = JFactory::getUser();
				$uId = $juser->id;

				$sql = "INSERT IGNORE INTO `#__cart_saved_addresses`
						SET `uidNumber` = {$uId}, {$sqlUpdateValues}";
				$this->_db->setQuery($sql);
				$this->_db->query();
			}

		}
		// Set errors and status
		else
		{
			$status = 0;
			$ret->errors = $errors;
		}

		$ret->status = $status;
		return $ret;
	}

	/**
	 * Saves transaction shipping cost and shipping discounts
	 *
	 * @param 	double 		$shippingCost shipping cost
	 * @return 	bool		true
	 */
	public function setTransactionShippingCost($shippingCost)
	{

		if (empty($this->tInfo))
		{
			throw new Exception(JText::_('No transaction info.'));
		}

		//print_r($this); die;
		$shippingDiscountAmount = 0;

		// Get shipping coupon (if any and apply it)
		$perks = unserialize($this->tInfo->tiPerks);

		if (!empty($perks['shipping']))
		{
			$shippingPerk = $perks['shipping'];
			// Calculate the shipping discount
			if ($shippingPerk->discountUnit == 'percentage')
			{
				if ($shippingPerk->discount > 100)
				{
					$shippingPerk->discount = 100;
				}

				$shippingDiscountAmount = $shippingCost * ($shippingPerk->discount / 100);
			}
			elseif ($shippingPerk->discountUnit == 'absolute')
			{
				if ($shippingCost < $shippingPerk->discount)
				{
					$shippingPerk->discount = $shippingCost;
				}

				$shippingDiscountAmount = $shippingPerk->discount;
			}
		}

		//print_r($perks);
		//echo $shippingDiscountAmount;
		//die;

		$sql = "UPDATE `#__cart_transaction_info` SET
				`tiShipping` = " . $this->_db->quote($shippingCost) . ",
				`tiShippingDiscount` = " . $this->_db->quote($shippingDiscountAmount) . "
				WHERE `tId` = " . $this->_db->quote($this->cart->tId);

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$this->_db->query();
	}

	/**
	 * Gets all transaction related info
	 *
	 * @param void
	 * @return object, false on no results
	 */
	public function getTransactionInfo($tId = NULL)
	{

		if (!$tId)
		{
			// If session expired tId will not be saved
			if (empty($this->cart->tId))
			{
				// Try to find if there is a pending transaction for this cart in DB and use it
				$where = "t.`crtId` = {$this->cart->crtId} AND t.`tStatus` = 'pending'";
			}
			else
			{
				$where = "t.`tId` = {$this->cart->tId}";
			}
		}
		else
		{
			$where = "t.`tId` = {$tId}";
		}

		// Get info
		$sql = "SELECT t.*, TIMESTAMPDIFF(MINUTE, t.`tLastUpdated`, NOW()) AS tAge, ti.*
				FROM `#__cart_transactions` t LEFT JOIN `#__cart_transaction_info` ti ON t.`tId` = ti.`tId`
				WHERE {$where}";
		$this->_db->setQuery($sql);
		$this->_db->query();
		//echo $this->_db->_sql; die;

		if (!$this->_db->getNumRows())
		{
			return false;
		}

		//echo $this->_db->_sql;
		$transactionInfo = $this->_db->loadObject();

		if (!$tId)
		{
			// Set transaction id session value (needed for expired session)
			$this->cart->tId = $transactionInfo->tId;

			// Get steps
			$steps = $this->getCheckoutSteps();

			$transactionInfo->steps = $steps;
		}

		//print_r($transactionInfo);
		return $transactionInfo;
	}

	/**
	 * Handle the error processing the transaction
	 *
	 * @param	int transaction ID
	 * @param 	object error
	 * @return	void
	 */
	public function handleTransactionError($tId, $error)
	{
		// Release transaction items back to inventory
		$this->releaseTransaction($tId);

		// Update status to 'error processing'
		$this->updateTransactionStatus('error processing', $tId);
	}

	/**
	 * Complete the transaction, mark it as completed, done, success...
	 *
	 * @param	object transaction info
	 * @return 	void
	 */
	public function completeTransaction($tInfo)
	{
		$tId = $tInfo->info->tId;
		$crtId = $tInfo->info->crtId;

		$transactionItems = unserialize($tInfo->info->tiItems);

		//print_r($transactionItems); die;

		// Handle all actions for each item
		include_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'ProductHandler.php');

		foreach ($transactionItems as $sId => $item)
		{
			$productHandler = new Cart_ProductHandler($item, $crtId);
			$productHandler->handle();
		}

		// Mark transaction as completed
		$this->updateTransactionStatus('completed', $tId);

		// Remove items from cart
		$this->removeTransactionItemsFromCart($tInfo);

		// remove coupons from cart
		$this->removeTransactionCouponsFromCart($tInfo);

		// Clean up cart...
		// - delete zero and negative qty items in the cart
		$sql = "DELETE FROM `#__cart_cart_items` WHERE `crtiQty` <= 0 AND `crtId` = {$tInfo->info->crtId}";
		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Set customer status
	 *
	 * @param	string 	$status new status
	 * @param	int 	$tId transactino ID
	 * @return 	void
	 */
	public function updateTransactionCustomerStatus($status, $tId = NULL)
	{
		if (!$tId)
		{
			$tId = $this->cart->tId;
		}

		if (!$tId || !is_numeric($tId))
		{
			return false;
		}

		// update status
		$sql = "UPDATE `#__cart_transaction_info` SET `tiCustomerStatus` = " . $this->_db->quote($status) . " WHERE `tId` = " . $this->_db->quote($tId);

		$this->_db->setQuery($sql);
		$this->_db->query();

		$affectedRows = $this->_db->getAffectedRows();

		if(!$affectedRows) {
			return false;
		}

		return true;
	}

	/**
	 * Gets all saved shipping addresses for this user
	 *
	 * @param void
	 * @return array
	 */
	public function getSavedShippingAddresses()
	{
		$juser = JFactory::getUser();
		$uId = $juser->id;
		$uId = Cart_Helper::escapeDb($uId);

		// Get all addresses
		$sql = "SELECT * FROM `#__cart_saved_addresses` WHERE `uidNumber` = {$uId}";
		$this->_db->setQuery($sql);
		$shippingAddresses = $this->_db->loadObjectList();

		if (empty($shippingAddresses))
		{
			return false;
		}

		//print_r($shippingAddresses); die;
		return $shippingAddresses;
	}

	/**
	 * Set selected saved shipping addresses for this user
	 *
	 * @param int saved address ID
	 * @return bool
	 */
	public function setSavedShippingAddress($saId)
	{
		// check if the address correct
		if (!is_numeric($saId))
		{
			throw new Exception(JText::_('COM_CART_INCORRECT_SAVED_SHIPPING_ADDRESS'));
		}

		$sql = "SELECT * FROM `#__cart_saved_addresses` WHERE `saId` = " . $this->_db->quote($saId);
		$this->_db->setQuery($sql);
		$this->_db->query();

		if ($this->_db->getNumRows() < 1)
		{
			throw new Exception(JText::_('COM_CART_INCORRECT_SAVED_SHIPPING_ADDRESS'));
		}

		$sql = "UPDATE `#__cart_transaction_info` ti, (SELECT * FROM `#__cart_saved_addresses` WHERE `saId` = " . $this->_db->quote($saId) . ") sa
				SET
				ti.`tiShippingToFirst` = sa.`saToFirst`,
				ti.`tiShippingToLast` = sa.`saToLast`,
				ti.`tiShippingAddress` = sa.`saAddress`,
				ti.`tiShippingCity` = sa.`saCity`,
				ti.`tiShippingState` = sa.`saState`,
				ti.`tiShippingZip` = sa.`saZip`

				WHERE ti.`tId` = {$this->cart->tId}";
		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$this->_db->query();

		//$this->setStepStatus('shipping');
		return true;
	}

	/**
	 * Generate unique security token to verify the place order calls
	 *
	 * @param 		void
	 * @return		string	token
	 */
	public function getToken()
	{
		if (empty($this->tInfo))
		{
			throw new Exception(JText::_('No transaction info.'));
		}

		return md5($this->securitySalt . $this->tInfo->tId);
	}

	/**
	 * Verify security token
	 *
	 * @param 		string	token
	 * @return		bool
	 */
	public function verifyToken($token, $tId = false)
	{
		if (empty($this->tInfo) && empty($tId))
		{
			throw new Exception(JText::_('No transaction info.'));
		}

		if (empty($tId))
		{
			$tId = $this->tInfo->tId;
		}

		return md5($this->securitySalt . $tId) == $token;
	}

	/**
	 * Finalizes transaction in the DB
	 *
	 * @param 		void
	 * @return		void
	 */
	public function finalizeTransaction()
	{
		if (empty($this->tInfo))
		{
			throw new Exception(JText::_('No transaction info.'));
		}

		//print_r($this->tInfo); die('--');

		$tiTotal = $this->tInfo->tiSubtotal + $this->tInfo->tiShipping - $this->tInfo->tiShippingDiscount - $this->tInfo->tiDiscounts;

		$sql = "UPDATE `#__cart_transaction_info` SET
				`titotal` = " . $this->_db->quote($tiTotal) . "
				WHERE `tId` = " . $this->_db->quote($this->cart->tId);

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$this->_db->query();
	}

	/**
	 * Mark step for current transaction as completed (default) or not-completed
	 *
	 * @param 	string Step
	 * @param 	bool Completed (true) ar not completed (false)
	 * @return 	bool
	 */
	public function setStepStatus($step, $status = true)
	{
		$allowedSteps = array('shipping');

		if (!in_array($step, $allowedSteps))
		{
			return false;
		}

		$sql = "UPDATE `#__cart_transaction_steps`
				SET `tsStatus` = " .  $this->_db->quote($status) . "
				WHERE `tId` = {$this->cart->tId} AND `tsStep` = '{$step}'";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	/**
	 * Get user ID associated with the cart
	 * @param int $crtId cart ID
	 * @return int user ID, false if no cart found
	 */
	public function getCartUser($crtId)
	{
		$sql = "SELECT `uidNumber` AS uId FROM `#__cart_carts` WHERE `crtId` = '{$crtId}'";
		$this->_db->setQuery($sql);
		$uId = $this->_db->loadResult();

		if (!empty($uId))
		{
			return $uId;
		}
		return false;
	}

	/* ------------------------------------------------------------ Coupon functions ----------------------------------------------------------- */

	/**
	 * Add coupon to cart
	 * @param 	string		$couponCode coupon code
	 * @return	bool		true on sucess
	 */
	public function addCoupon($couponCode)
	{
		// Check if coupon is valid and active (throws exception if invalid)
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php');
		$coupons = new StorefrontModelCoupons;

		// Get coupons
		$this->cartCoupons = $this->getCoupons();

		// Check if coupon has already been applied
		if($this->isCouponApplied($couponCode)) {
			throw new Exception(JText::_('COM_CART_COUPON_ALREADY_APPLIED'));
		}

		$cnId = $coupons->isValid($couponCode);

		// Apply coupon, add item to cart if needed/possible (throws exception if not applicable)
		$this->apply($cnId);

		// If user is logged in subtract coupon use count. If not logged in subtraction will happen when user logs in
		$juser = JFactory::getUser();
		if ($juser->id)
		{
			$coupons->apply($cnId);
		}

		// Add coupon
		$sql = "INSERT INTO `#__cart_coupons` (`crtId`, `cnId`, `crtCnAdded`, `crtCnStatus`)
				VALUES ({$this->crtId}, " . $this->_db->quote($cnId) . ", NOW(), 'active')";

		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	/**
	 * Check if coupon applies to the cart and add a SKU to cart if needed and possible
	 * @param 	int			$cnId coupon ID
	 * @return	bool		true on cuccess, exception on failure
	 */
	public function apply($cnId)
	{
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php');
		$coupon = StorefrontModelCoupons::getCouponInfo($cnId, true, true, true, true);

		if (!$coupon->info->itemCoupon)
		{
			// All non-item coupons apply
			return true;
		}

		$cartInfo = $this->getCartInfo();
		$cartItems = $cartInfo->items;

		//print_r($cartItems); die;

		// Go through each coupon object and try to find a match in a cart
		foreach ($coupon->objects as $couponObject)
		{
			foreach ($cartItems as $sId => $cartItem)
			{
				if (($coupon->info->cnObject == 'sku' && $sId == $couponObject->cnoObjectId) ||
					($coupon->info->cnObject == 'product' && $cartItem['info']->pId == $couponObject->cnoObjectId))
				{
					// return true as soon as at least one match found
					return true;
				}
			}
		}

		// No item match, check if there is a way to map to a single SKU for this coupon and add this SKU to cart

		// Only one obect may be defined to map to a single SKU
		if (sizeof($coupon->objects) == 1)
		{
			$couponObject = $coupon->objects[0];

			if ($coupon->info->cnObject == 'sku')
			{
				// Add SKU to cart
				//echo 'add ' . $couponObject->cnoObjectId . ' to cart'; die;
				$this->add($couponObject->cnoObjectId);
				return true;
			}
			elseif ($coupon->info->cnObject == 'product')
			{
				// Check product SKUs
				include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
				$warehouse = new StorefrontModelWarehouse();
				$productOptions = $warehouse->getProductOptions($couponObject->cnoObjectId);

				// See if the product has only one SKU, then add this SKU to cart (There is no way do decide what SKU to add if there are several of them)
				if (sizeof($productOptions->skus) == 1)
				{
					// Get product's SKU
					$sId = array_shift($productOptions->skus);
					$sId = $sId['info']->sId;

					// Add SKU to cart
					//echo 'add ' . $sId . ' to cart'; die;
					$this->add($sId);
					return true;
				}
			}
		}

		// Coupon is not applicable
		throw new Exception(JText::_('COM_CART_CANNOT_APPLY_COUPON'));
	}

	/**
	 * Get coupons applied to the cart
	 * @param 	void
	 * @return	array		coupons
	 */
	public function getCoupons()
	{
		$sql = "SELECT cnId FROM `#__cart_coupons` WHERE `crtId` = {$this->crtId} AND crtCnStatus = 'active' ORDER BY `crtCnAdded`";
		$this->_db->setQuery($sql);
		//echo $this->_db->_sql; die;
		$cnIds = $this->_db->loadResultArray();

		//print_r($cnIds); die;

		// Get coupon types
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php');
		$coupons = StorefrontModelCoupons::getCouponsInfo($cnIds);

		//print_r($coupons); die;

		return $coupons;
	}

	/**
	 * Checks if coupon is applied to the cart
	 * @param 	string		$couponCode coupon code
	 * @return	bool
	 */
	private function isCouponApplied($cnCode)
	{
		// Get coupons if needed
		if (empty($this->cartCoupons))
		{
			$this->cartCoupons = $this->getCoupons();
		}

		// iterate through coupons and return true if coupon already applied
		foreach ($this->cartCoupons as $coupon)
		{
			//print_r($coupongroup); die('44');
			if ($coupon->cnCode == $cnCode)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Do the maintenance of coupons -- make sure all coupons are valid and in order, remove unnecesary coupons...
	 * Return all perks available for the cart
	 *
	 * @param 	object 		$cartInfo
	 * @param	object		$cartCoupons
	 * @return 	array		perks
	 */
	public function getCouponPerks()
	{
		$cartInfo = $this->getCartInfo();
		$cartCoupons = $this->getCoupons();

		/*
		echo "Cart info: \n";
		print_r($cartInfo);
		echo "\n============== \n\n";
		*/

		/*
		echo "Cart coupons: \n";
		print_r($cartCoupons);
		echo "\n============== \n\n";
		*/

		$cartItems = $cartInfo->items;
		// initialize perks
		$perks = array();

		// Since coupons in $cartCoupons are expected to be ordered with item coupons coming first, item coupons will be processed first in bulk
		// set the flag if currently processing item coupons
		$itemCoupon = true;

		// Initialize $itemsDiscountsTotal -- the total sum of all item discount amounts
		$itemsDiscountsTotal = 0;
		// Initialize $genericDiscountsTotal -- the total sum of all non-item discounts
		$genericDiscountsTotal = 0;

		/*
			Ititialize a global SKU/Other object to coupon mapping.
			The coupons are ordered by time applied, so if there is more than one coupon for one SKU,
			we need to apply the most recently applied only. As we iterate through coupons we only want to record the recent one.
			Same for non item coupons -- there is only one coupon per object type is allowed (shipping, total order discount, etc...)
		*/
		$couponMappings = array();

		/*
			Ititialize a global SKU to discount mapping.
			Just like coupons we need to apply only the most recently applied discount and discard old ones
		*/
		$itemCouponDiscountMappings = array();

		//print_r($cartCoupons); die;

		// go through each coupon in the cart
		foreach ($cartCoupons as $cn) {

			// Check if coupon expired
			if ($cn->cnExpired)
			{
				continue;
			}

			// Check if coupon applies to cart items
			if (!$cn->itemCoupon)
			{
				$itemCoupon = false;
			}

			$coupon = StorefrontModelCoupons::getCouponInfo($cn->cnId, $itemCoupon); // Load objects for itemCoupons only

			// check if coupon applies and if it does, get the perk info
			/*
			echo "Coupon info: \n";
			print_r($coupon);
			echo "\n============== \n\n";
			*/

			// First check coupon conditions
			// Big TODO

			// get object type
			$couponObjectType = $cn->cnObject;

			switch ($couponObjectType)
			{
				case 'sku':
					break;
				case 'product':
					break;
				case 'order':
					break;
				case 'shipping':
					break;
				default:
					throw new Exception(JText::_('Invalid coupon. Invalid object type.'));
			}

			// Get coupon action in case we need to apply it.
			// Right now there is only one action -- discount
			$couponAction = $coupon->action->cnaAction;

			if ($couponAction == 'discount')
			{
				// find out whether it is a absolute amount or percentage
				if (substr($coupon->action->cnaVal, -1) == '%')
				{
					$couponDiscountUnit = 'percentage';
					$couponDiscount = substr($coupon->action->cnaVal, 0, strlen($coupon->action->cnaVal) - 1);
				}
				else
				{
					$couponDiscountUnit = 'absolute';
					$couponDiscount = $coupon->action->cnaVal;
				}

				// make sure discount is numeric
				if (!is_numeric($couponDiscount))
				{
					throw new Exception(JText::_('Invalid coupon. Invalid discount amount ' . $couponDiscount . '.'));
				}

			}
			else {
				throw new Exception(JText::_('Invalid coupon. Invalid action type.'));
			}

			// Check if we need to match against the object type to make sure the coupon is applicable
			if ($itemCoupon)
			{
				// check if there are options available
				if (empty($coupon->objects))
				{
					throw new Exception(JText::_('Invalid coupon. No object found.'));
				}

				// Go through each coupon object and try to find a match in a cart
				foreach ($coupon->objects as $couponObject)
				{
					foreach ($cartItems as $sId => $cartItem)
					{
						// try to find a match
						if (($couponObjectType == 'sku' && $sId == $couponObject->cnoObjectId) ||
							($couponObjectType == 'product' && $cartItem['info']->pId == $couponObject->cnoObjectId))
						{
							// Initialize the perk
							unset($perk);
							$perk->name = $cn->cnDescription;
							$perk->forSku = $sId;
							$perk->couponId = $cn->cnId;

							// Save current/overwrite previous mapping
							$couponMappings[$sId] = $cn->cnId;

							// figure out the perk
							if ($couponAction == 'discount')
							{
								/*
								See if there is a limit of same products this can be applied to.
								*/
								$objectsLimit = $couponObject->cnoObjectsLimit;
								$applyPerkToQty = $cartItem['cartInfo']->qty;
								if ($applyPerkToQty > $objectsLimit)
								{
									$applyPerkToQty = $objectsLimit;
								}

								/*
								TODO Also ssee a note about figuring out the most expensive items if there is a limit.
								*/

								// Calculate discount
								if ($couponDiscountUnit == 'absolute')
								{
									// make sure $couponDiscount is not more than the item price
									if ($cartItem['info']->sPrice < $couponDiscount)
									{
										$couponDiscount = $cartItem['info']->sPrice;
									}

									$discountAmount = $couponDiscount * $applyPerkToQty;
								}
								elseif ($couponDiscountUnit == 'percentage')
								{
									// make sure $couponDiscount is <= 100%
									if ($couponDiscount > 100)
									{
										$couponDiscount = 100;
									}

									$discountAmount = ($cartItem['info']->sPrice * $applyPerkToQty) *  ($couponDiscount / 100);
								}

								$perk->discount = round($discountAmount, 2, PHP_ROUND_HALF_DOWN);

								$itemCouponDiscountMappings[$sId] = $perk->discount;

								$perks['items'][$perk->forSku] = $perk;
							}
							else
							{
								throw new Exception(JText::_('Invalid coupon. Only discounts are afailable for skus and products'));
							}

							if ($couponObjectType == 'sku')
							{
								break;
							}
							//no break for products -- keep going maybe there are several SKUs of the same product in the cart
						}
						// No match
					}
				}

				// calculate total item discounts, the final correct value will be calculated on the last iteration of the parent loop
				foreach ($itemCouponDiscountMappings as $mappedDiscount)
				{
					$itemsDiscountsTotal += $mappedDiscount;
				}
			}
			// Coupon is generic, not item based and not shipping.
			// All item coupons have been processed by this time, save to calculate total discounts
			elseif ($couponObjectType != 'shipping') {
				// Initialize the perk
				unset($perk);
				$perk->name = $cn->cnDescription;
				$perk->couponId = $cn->cnId;

				$couponMappings[$couponObjectType] = $cn->cnId;

				// figure out the perk
				if ($couponAction == 'discount')
				{
					switch ($couponObjectType)
					{
						case 'order':
							$amountToDiscount = $cartInfo->totalCart - $itemsDiscountsTotal;
							break;
					}

					// Quit if there is no amount to discount
					if (!$amountToDiscount) {
						//break;
					}

					/*
					TODO see if there is a limit of same products this can be applied to.
					Also ssee a note about figuring out the most expensive items if there is a limit.
					*/

					// Calculate discount
					if ($couponDiscountUnit == 'absolute')
					{
						$discountAmount = $couponDiscount;
					}
					elseif ($couponDiscountUnit == 'percentage')
					{
						// make sure $couponDiscount is <= 100%
						if ($couponDiscount > 100)
						{
							$couponDiscount = 100;
						}

						$discountAmount = $amountToDiscount * ($couponDiscount / 100);
					}

					$perk->discount = round($discountAmount, 2);

					$perks['generic'][$couponObjectType] = $perk;
				}

				// calculate total generic discounts, the final correct value will be calculated on the last iteration of the parent loop
				foreach ($perks['generic'] as $perkDiscount)
				{
					$genericDiscountsTotal += $perkDiscount->discount;
				}
			}

			// Coupon is for shipping -- handled differently to be applied later after the shipping cost is calculated
			elseif($couponObjectType == 'shipping')
			{
				// Initialize the perk
				unset($perk);
				$perk->name = $cn->cnDescription;
				$perk->couponId = $cn->cnId;

				$couponMappings[$couponObjectType] = $cn->cnId;

				$perk->discountUnit = $couponDiscountUnit;
				$perk->discount = $couponDiscount;

				$perks['shipping'] = $perk;
			}

		}

		// Do the coupon maintenance
		/*
			At this point $couponMappings has all the coupons that need to be applied to cart -- all other unsued coupons have to be released
		*/

		// Go through each coupon again and see if it needs to be applied. If not -- release it
		foreach ($cartCoupons as $cn)
		{
			if ($cn->cnExpired)
			{
				// TODO record expired coupon to display later
				$this->removeCoupon($cn->cnId);
			}
			if (!in_array($cn->cnId, $couponMappings))
			{
				//echo 'remove ' . $cn->cnId . '<br>';
				$this->removeCoupon($cn->cnId);
			}
		}

		$perksInfo->itemsDiscountsTotal = $itemsDiscountsTotal;
		$perksInfo->genericDiscountsTotal = $genericDiscountsTotal;
		$perksInfo->discountsTotal = $itemsDiscountsTotal + $genericDiscountsTotal;
		$perks['info'] = $perksInfo;

		//echo "Perks: \n";
		//print_r($perks);
		//die;

		return $perks;
	}

	/**
	 * Remove coupon from cart
	 * @param 	int		$cnId coupon ID
	 * @return	bool		true on sucess
	 */
	public function removeCoupon($cnId)
	{
		$coupons = new StorefrontModelCoupons;

		// If user is logged in return coupon back to the coupons pool.
		$juser = JFactory::getUser();
		if ($juser->id)
		{
			//print_r($juser); die;
			$coupons->recycle($cnId);
		}

		// Remove coupon
		$sql = "DELETE FROM `#__cart_coupons` WHERE `cnId` = " . $this->_db->quote($cnId) . " AND `crtId` = " . $this->_db->quote($this->crtId);

		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$this->_db->query();

		return true;
	}

	/**
	 * Handle memberships
	 *
	 * @param 	object 		$cartInfo
	 * @param	object		$cartCoupons
	 * @return 	object		membership info
	 */
	public function getMembershipInfo()
	{
		$cartInfo = $this->getCartInfo();

		/*
		echo "Cart info: \n";
		print_r($cartInfo);
		echo "\n============== \n\n";
		*/

		$cartItems = $cartInfo->items;
		//print_r($cartItems); die;

		// init membership info
		$memberships = array();

		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');
		$ms = new StorefrontModelMemberships();

		// Get membership types
		$membershipTypes = $ms->getMembershipTypes();
		//print_r($membershipTypes); die('*');

		// Go through each product and see if the type is membership
		foreach ($cartItems as $sId => $item)
		{
			if (in_array($item['info']->ptId, $membershipTypes) && !empty($item['meta']['ttl']))
			{
				$membershipSIdInfo = $ms->getNewExpirationInfo($this->crtId, $item);

				/*
				// If membership lookup existing membership (if any)
				$membershipInfo = $ms->getMembershipInfo($this->crtId, $item['info']->pId);

				// Calculate correct TTL for one SKU
				$ttl = $ms->getTtl($item['meta']['ttl'], $item['cartInfo']->qty);

				// Calculate the new expiration date
				if ($membershipInfo && $membershipInfo['crtmActive'])
				{
					// New expiration date is an old not-expired date + TTL
					$membershipSIdInfo->newExpires = strtotime('+ ' . $ttl, strtotime($membershipInfo['crtmExpires']));
					$membershipSIdInfo->existingExpires = strtotime($membershipInfo['crtmExpires']);
					//echo date('l dS \o\f F Y h:i:s A', strtotime('+ 10 YEAR', strtotime($membershipInfo['crtmExpires']))); die;
				}
				else
				{
					// New expiration date is now + TTL
					$membershipSIdInfo->newExpires = strtotime('+ ' . $ttl);
				}
				*/

				$memberships[$sId] = $membershipSIdInfo;
				unset($membershipSIdInfo);
			}

		}

		//print_r($memberships); die('--');
		return $memberships;
	}

/* ----------------------------------------------------------------------------------------------------------------------------------------------- */
/* -------------------------------------------------------------- Private functinos -------------------------------------------------------------- */
/* ----------------------------------------------------------------------------------------------------------------------------------------------- */



	/**
	 * Remove transaction items from the cart associated with it
	 *
	 * @param	object transaction info
	 * @return 	void
	 */
	private function removeTransactionItemsFromCart($tInfo)
	{
		// remove each item from the cart
		foreach ($tInfo->items as $sId => $item)
		{
			$this->removeItem($sId, $item['transactionInfo']->qty, $tInfo->info->crtId);
		}
	}

	/**
	 * Remove transaction coupons from the cart associated with it
	 *
	 * @param	object transaction info
	 * @return 	void
	 */
	private function removeTransactionCouponsFromCart($tInfo)
	{
		$perks = $tInfo->info->tiPerks;

		if (empty($perks))
		{
			return true;
		}

		$perks = unserialize($perks);

		//print_r($perks); die;

		// remove each coupon from the cart
		$couponIds = array();
		foreach ($perks as $k => $val)
		{
			if ($k != 'info')
			{
				if (is_array($val))
				{
					foreach ($val as $coupon)
					{
						$couponIds[] = $coupon->couponId;
					}
				}
				else
				{
					$couponIds[] = $val->couponId;
				}
			}
		}

		$sqlCoupons = '0';

		foreach ($couponIds as $cnId)
		{
			$sqlCoupons .= ' OR `cnId` = ' . $this->_db->quote($cnId);
		}

		$sql = "UPDATE `#__cart_coupons` SET `crtCnStatus` = 'applied'
				WHERE ({$sqlCoupons}) AND `crtId` = {$tInfo->info->crtId}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		//die;
	}

	/**
	 * Get all items in the cart if cart exists, if cart doesn't exist reset session data and create a new cart
	 * This is primarily for cases when session cart doesn't exist anymore. In most cases cart will be there.
	 *
	 * @param void
	 * @return array of items in the cart
	 */
	private function getItems()
	{
		// Just in case check if current cart exists. If not -- delete session cart and create a new one
		if (!$this->cartExists($this->crtId))
		{
			$this->clearSessionCart();
			$this->createCart();
			// call itself
			return $this->getItems();
		}
		return $this->getCartItems();
	}

	/**
	 * Update cart based on current availability (and pricing TODO) and return all items in the cart, update cart's 'lastUpdated' field
	 *
	 * @param void
	 * @return array of items in the cart
	 */
	private function getCartItems()
	{
		if ($this->debug)
		{
			echo "<br>Getting items from DB";
		}

		$sql = "SELECT `sId`, `crtiQty`, `crtiPrice` FROM `#__cart_cart_items` crti WHERE crti.`crtId` = {$this->crtId}";
		$this->_db->setQuery($sql);
		$allSkuInfo = $this->_db->loadObjectList('sId');
		// Get just skuId's
		$skus = $this->_db->loadResultArray();

		//print_r($skus); die;


		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$skuInfo = $warehouse->getSkusInfo($skus);

		//print_r($allSkuInfo); die;
		//print_r($skuInfo); die;

		$cartItems = array();

		// Note that some items become unavailable/out of stock or prices may change, need to account for this
		foreach ($allSkuInfo as $sId => $sku)
		{
			// See if the SKU is available at all
			if (empty($skuInfo[$sId]))
			{
				// Product not available anymore, set as unavailable
				$this->markItemUnavailable($sId);
				$this->cart->hasChanges = true;

				continue;
			}

			$inf = $skuInfo[$sId]['info'];
			$cartItems[$sId] = $skuInfo[$sId];

			$updated = false;

			// Check if there is enough inventory
			if ($inf->sTrackInventory && ($inf->sInventory < $sku->crtiQty))
			{
				if (!$inf->sInventory)
				{
					// 'This product is not in stock anymore
					// remove item, retain value though for changes messaging
					$this->doItem($sId, 'set', 0, true);
					$cartInfo->qty = 0;
				}
				else
				{
					// Inventory level dropped for this product, retain value for changes messaging
					$this->doItem($sId, 'set', $inf->sInventory, true);
					$cartInfo->qty = $inf->sInventory;
				}

				// Set card changed flag
				$this->cart->hasChanges = true;

				// Mark as updated to prevent double updating
				$updated = true;
			}
			else
			{
				$cartInfo->qty = $sku->crtiQty;
			}

			// Check if price changed and not yet been updated (through the $this->update call)
			if ($inf->sPrice != $sku->crtiPrice && !$updated)
			{
				// Product price changed
				$this->doItem($sId, 'sync');

				// Set card changed flag
				$this->cart->hasChanges = true;
			}

			$cartItems[$sId]['info'] = $inf;
			$cartItems[$sId]['cartInfo'] = $cartInfo;

			unset($cartInfo);
		}

		$this->updateSession();

		// update 'lastUpdated
		$sql = "UPDATE `#__cart_carts` SET `crtLastUpdated` = NOW() WHERE `crtId` = {$this->crtId}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return $cartItems;

	}

	/**
	 * Get a single item in the cart
	 *
	 * @param SKU ID
	 * @return object SKU cart info
	 */
	private function getCartItem($sId)
	{
		$sql = "SELECT `crtiQty` FROM `#__cart_cart_items` WHERE `sId` = {$sId} AND `crtId` = {$this->crtId}";
		$this->_db->setQuery($sql);
		$skuCartInfo = $this->_db->loadObject();

		if ($skuCartInfo) {
			return $skuCartInfo;
		}
		return false;
	}

	/**
	 * Get cart info from session, no DB call, simply use session data
	 *
	 * @param void
	 * @return bool
	 */
	private function liftSessionCart()
	{
		if ($this->debug)
		{
			echo "<br>Lifting session cart";
		}
		$session = JFactory::getSession();
		$cart = $session->get('cart');

		if ($cart)
		{
			$this->cart = $cart;
			$this->crtId = $cart->crtId;
			return true;
		}
		return false;
	}

	/**
	 * Sync session cart with the latest info from DB
	 *
	 * @param void
	 * @return void
	 */
	private function syncSessionCart()
	{
		// Return if sync mode is off
		if (!$this->sync)
		{
			return;
		}

		// Clean old transactions (if any)
		$this->cleanOldTransactions();

		// Get data from DB
		$cartItems = $this->getItems();

		//print_r($cartItems); die;

		if ($this->debug)
		{
			echo "<br>Updating session cart";
		}

		// Format data and update instance cart
		$this->cart->totalItems = 0;
		$this->cart->totalCart = 0;
		$this->cart->items = $cartItems;
		$this->cart->lastUpdated = time();

		foreach ($cartItems as $sId => $sku)
		{
			$price = $sku['info']->sPrice;
			$qty = $sku['cartInfo']->qty;

			$this->cart->totalItems += $qty;
			$this->cart->totalCart += $qty * $price;
		}

		$this->cart->crtId = $this->crtId;

		// Update session
		$this->updateSession();

	}

	/**
	 * Update session with instance cart info (no product syncing though
	 *
	 * @param void
	 * @return void
	 */
	private function updateSession()
	{
		$session = JFactory::getSession();
		$session->set('cart', $this->cart);
	}

	/**
	 * Update session cart with the latest info from DB
	 *
	 * @param void
	 * @return void
	 */
	private function clearSessionCart()
	{
		$session = JFactory::getSession();
		$session->clear('cart');
	}

	/**
	 * Get cart info from cookie
	 *
	 * @param void
	 * @return bool
	 */
	private function liftCookie()
	{
		if ($this->debug)
		{
			echo "<br>Lifting cookie cart";
		}
		// If cookie exists, check if this is not pointing to a members' cart and load it.
		$crtId = JRequest::getVar('cartId', '', 'COOKIE');

		if (!empty($crtId) && !$this->cartIsLinked($crtId))
		{
			// Load cart
			return $this->liftCart($crtId);
		}
		return false;
	}

	/**
	 * Check if cart is linked to any member's ID
	 *
	 * @param $crtId Cart ID
	 * @return bool
	 */
	private function cartIsLinked($crtId)
	{
		$sql = "SELECT COUNT(`crtId`) FROM `#__cart_carts` WHERE `crtId` = {$crtId} AND `uidNumber` IS NOT NULL";
		$this->_db->setQuery($sql);
		$isLinked = $this->_db->loadResult();

		if ($isLinked)
		{
			return true;
		}
		return false;
	}

	/**
	 * Find a cart in the DB for a given user and lift it
	 * @param $uId User ID
	 * @return bool True if cart exists and can be lifted, false if no user cart exists or cart cannot be lifted
	 */
	private function liftUserCart($uId)
	{
		if ($this->debug)
		{
			echo "<br>Lifting user cart";
		}

		$crtId = $this->getUserCartId($uId);

		if ($crtId)
		{
			return $this->liftCart($crtId, true);
		}
		return false;
	}

	/**
	 * Check if user's cart exists and return its ID
	 * @param $uId User ID
	 * @return int Cart ID, false if no user cart exists
	 */
	private function getUserCartId($uId)
	{
		$sql = "SELECT `crtId` FROM `#__cart_carts` WHERE `uidNumber` = '{$uId}'";
		$this->_db->setQuery($sql);
		$crtId = $this->_db->loadResult();

		if (!empty($crtId))
		{
			return $crtId;
		}
		return false;
	}

	/**
	 * Lift requested cart
	 * For logged in users use liftUserCart function instead.
	 *
	 * @param int $crtId Cart ID
	 * @param bool $linkedCart -- whether the session linked flag should be set (for logged in users)
	 * @return bool
	 */
	private function liftCart($crtId, $linkedCart = false)
	{
		if ($linkedCart)
		{
			$this->cart->linked = 1;
		}
		else {
			$this->cart->linked = 0;
		}
		$this->crtId = $crtId;

		// Update session cart
		$this->syncSessionCart();

		return true;
	}

	/**
	 * Create a new cart
	 *
	 * @param void
	 * @return void
	 */
	private function createCart()
	{
		if ($this->debug)
		{
			echo "<br>Creating new cart";
		}

		$juser = JFactory::getUser();

		$uId = 'NULL';
		if (!$juser->get('guest'))
		{
			$uId = $juser->id;
			$cart->linked = 1;
		}
		else {
			$cart->linked = 0;
		}

		$sql = "INSERT INTO `#__cart_carts` SET `crtCreated` = NOW(), `crtLastUpdated` = NOW(), `uidNumber` = {$uId}";
		$this->_db->setQuery($sql);
		$this->_db->query();
		$crtId = $this->_db->insertid();

		$session = JFactory::getSession();
		$cart->crtId = $crtId;
		$this->crtId = $cart->crtId;
		$session->set('cart', $cart);

		// Set cookie for non logged-in users to recover the cart
		if ($juser->get('guest'))
		{
			if ($this->debug)
			{
				echo "<br>Setting a cookie";
			}
			setcookie("cartId", $crtId, time() + 60 * 60 * 24 * 90); // 90 days
		}
	}

	/**
	 * Set cart to a specified cart ID. First checks if the cart exists.
	 *
	 * @param int Cart ID
	 * @return bool
	 */
	private function setCart($crtId)
	{
		if ($this->cartExists($crtId))
		{
			$this->crtId = $crtId;
			return true;
		}
		return false;
	}

	/**
	 * Check if the cart exists.
	 *
	 * @param int Cart ID
	 * @return bool
	 */
	private function cartExists($crtId)
	{
		$sql = "SELECT `crtId` FROM `#__cart_carts` WHERE `crtId` = '{$crtId}'";

		$this->_db->setQuery($sql);
		$crtId = $this->_db->loadResult();

		if (!empty($crtId))
		{
			return true;
		}
		return false;
	}

	/**
	 * Link a session cart with user's cart. If these carts are different -- merge them. If there is only a session cart -- make it user's cart
	 *
	 * @param void
	 * @return bool
	 */
	private function linkCarts()
	{
		if ($this->debug)
		{
			echo "<br>Linking carts";
		}

		// Kill the old cookie
		setcookie("cartId", '', time() - 60 * 60 * 24 * 90); // -90 days

		// Get user
		$juser = JFactory::getUser();

		// Check if session cart is not someone else's. Otherwise load user's cart and done
		if ($this->cartIsLinked($this->crtId))
		{
			if (!$this->liftUserCart($juser->id))
			{
				return false;
			}
			return true;
		}

		// Get user's cart
		$userCartId = $this->getUserCartId($juser->id);

		// If no user cart -- make the session cart a user's cart

		// Get coupons
		$coupons = $this->getCoupons();

		if (!$userCartId)
		{
			$sql = "UPDATE `#__cart_carts` SET `uidNumber` = {$juser->id} WHERE `crtId` = {$this->crtId}";
			$existingCnIds = array();
		}
		// Merge session and user carts
		else
		{
			// need to apply each coupon (mark as used) before merging
			$cnSql = '0';
			$allCouponsIds = array();
			foreach ($coupons as $coupon)
			{
				$cnSql .= ',' . $coupon->cnId;
				$allCouponsIds[] = $coupon->cnId;
			}

			// Find all coupons in the user's cart that are already applied and don't need to be reapplied
			$sql = "SELECT `cnId` FROM `#__cart_coupons` WHERE `crtId` = " . $this->_db->quote($userCartId) . " AND `cnId` IN (" . $cnSql . ") AND `crtCnStatus` = 'active'";
			$this->_db->setQuery($sql);
			$this->_db->query();
			$existingCnIds = $this->_db->loadResultArray();

			// merge items -- simply add all session cart items to user cart
			$sql = "INSERT INTO `#__cart_cart_items` (`crtId`, `sId`, `crtiQty`)
					SELECT {$userCartId}, `sId`, `crtiQty` FROM `#__cart_cart_items` ii WHERE ii.crtId = {$this->crtId}
					ON DUPLICATE KEY UPDATE `#__cart_cart_items`.`crtiQty` = `#__cart_cart_items`.`crtiQty` + ii.`crtiQty`";
			$this->_db->setQuery($sql);
			//echo $this->_db->_sql; die;
			$this->_db->query();

			// merge coupons
			$couponsIdsToMerge = array_diff($allCouponsIds, $existingCnIds);
			$mergeSql = '0';
			foreach ($couponsIdsToMerge as $cnId)
			{
				$mergeSql .= ',' . $cnId;
			}
			$sql = "INSERT INTO `#__cart_coupons` (`crtId`, `cnId`, `crtCnAdded`, `crtCnStatus`)
					SELECT {$userCartId}, `cnId`, `crtCnAdded`, 'active' FROM `#__cart_coupons` cc WHERE cc.crtId = {$this->crtId} AND `cnId` IN (" . $mergeSql . ")";
			$this->_db->setQuery($sql);
			//echo $this->_db->_sql; die;
			$this->_db->query();

			// kill old cart
			$this->kill($this->crtId);

			// Update current cart ID
			$this->crtId = $userCartId;
		}

		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php');
		$storefrontCoupons = new StorefrontModelCoupons;

		// go through each coupon and apply all that are not applied (if merging, or all if making the session cart a user's cart)
		foreach ($coupons as $coupon)
		{
			if (!in_array($coupon->cnId, $existingCnIds))
			{
				$storefrontCoupons->apply($coupon->cnId);
			}
		}

		// Mark cart as linked
		$this->cart->linked = true;
		// Update session
		$this->syncSessionCart();

		return true;
	}

	/**
	 * Update cart SKU, set as unavailable (if SKU or product get deleted)
	 *
	 * @param 	int		SKU ID
	 * @return void
	 */
	private function markItemUnavailable($sId)
	{
		$sql = "UPDATE `#__cart_cart_items` SET `crtiAvailable` = 0 WHERE `sId` = " . $this->_db->quote($sId);
		$this->_db->setQuery($sql);
		//echo $this->_db->_sql;
		$this->_db->query();
	}

	/**
	 * Update/add SKU/quantity to cart, update the price in the cart, save old price and inventory level (if requested)
	 *
	 * @param SKU ID
	 * @param mode Update Method: add - adds to the existing quantity, set - ignores existing quantity and sets a new value, sync - simply checks/updates inventory and pricing
	 * @param int Quantity
	 * @param bool Retain old value -- flag determening whether the old qty should be saved (only when it goes down); price get saved in either case
	 * @return void
	 */
	private function doItem($sId, $mode = 'add', $qty = 1, $retainOldValue = false)
	{
		// Check quantity: must be a positive integer
		if (!is_numeric($qty) || $qty < 0)
		{
			throw new Exception(JText::_('COM_CART_INCORRECT_QTY'));
		}
		elseif ($qty == 0 && !$retainOldValue)
		{
			// Delete if quantity is set to zero
			if ($mode == 'set')
			{
				$this->deleteItem($sId);
				return;
			}
			else {
				throw new Exception(JText::_('COM_CART_INCORRECT_QTY'));
			}
		}

		// Check if there is enough inventory (if tracking inventory) taking into account current quantity in the cart

		// Get the quantity already in the cart (if appending or simply syncing)
		if ($mode == 'add' || $mode == 'sync')
		{
			$skuCartInfo = $this->getCartItem($sId);
		}
		// If not adding, but setting, ignore cart value
		else
		{
			$skuCartInfo->crtiQty = 0;
		}

		// Get SKU pricing and inventory level & policies
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();
		$allSkuInfo = $warehouse->getSkusInfo(array($sId));

		$skuInfo = $allSkuInfo[$sId]['info'];
		$skuName = $skuInfo->pName;
		if (!empty($allSkuInfo[$sId]['options']) && count($allSkuInfo[$sId]['options']))
		{
			foreach ($allSkuInfo[$sId]['options'] as $oName)
			{
				$skuName .= ', ' . $oName;
			}
		}

		// Check inventory rules (sync mode doesn't check inventory level, just pricing)
		if ($mode != 'sync')
		{
			// Don't allow purchasing multiple products for those that are not allowed
			if (!$skuInfo->sAllowMultiple && ((!empty($skuCartInfo->crtiQty) && $skuCartInfo->crtiQty > 0) || ($qty > 1))) {
				throw new Exception(JText::_('COM_CART_NO_MULTIPLE_ITEMS'));
			}
			// Make sure there is enough inventory
			elseif ($skuInfo->sTrackInventory)
			{
				// See if qty can be added
				if ($qty > $skuInfo->sInventory)
				{
					throw new Exception(JText::_('COM_CART_NOT_ENOUGH_INVENTORY'));
				}
				elseif (!empty($skuCartInfo->crtiQty) && ($qty + $skuCartInfo->crtiQty > $skuInfo->sInventory))
				{
					// This is how much they can add: $skuInfo->sInventory - $skuCartInfo->crtiQty
					throw new Exception(JText::_('COM_CART_ADD_TOO_MANY_CART'));
				}
			}
		}

		// Insert new values, if exists save the previous price (for possible price changes messaging) and old inventory level (if needed)
		$sql = "INSERT INTO `#__cart_cart_items`
				(`crtId`, `sId`, `crtiQty`, `crtiOldQty`, `crtiPrice`, `crtiOldPrice`, `crtiName`)
				VALUES
				({$this->crtId}, '{$sId}', {$qty}, NULL, {$skuInfo->sPrice}, NULL, " . $this->_db->quote($skuName) . ")
				ON DUPLICATE KEY UPDATE `crtiOldPrice` = `crtiPrice`, `crtiPrice` = {$skuInfo->sPrice}, `crtiName` = " . $this->_db->quote($skuName);

		// Check if old value has to be retained
		if ($retainOldValue)
		{
			$sql .= ", `crtiOldQty` = `crtiQty`";
		}
		else
		{
			$sql .= ", `crtiOldQty` = NULL";
		}

		// add to the existing qty value
		if ($mode == 'add')
		{
			$sql .= ", `crtiQty` = `crtiQty` + {$qty}";
		}
		// set new qty value
		elseif ($mode == 'set')
		{
			$sql .= ", `crtiQty` = {$qty}";
		}
		// keep the qty value if syncing

		$this->_db->setQuery($sql);
		// echo $this->_db->_sql;
		$this->_db->query();
	}

	/**
	 * Delete SKU from cart
	 *
	 * @param SKU ID
	 * @return void
	 */
	private function deleteItem($sId)
	{
		// delete cart item
		$sql = "DELETE FROM `#__cart_cart_items` WHERE `sId` = '{$sId}' AND `crtId` = {$this->crtId}";

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Remove given quantity of SKU from cart
	 *
	 * @param 	int SKU ID
	 * @param 	int Qty
	 * @return 	void
	 */
	private function removeItem($sId, $qty, $crtId)
	{
		// Update cart item
		$sql = "UPDATE `#__cart_cart_items` SET `crtiQty` = `crtiQty` - {$qty} WHERE `sId` = '{$sId}' AND `crtId` = {$crtId}";

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Delete cart and all cart items
	 *
	 * @param int Cart ID
	 * @return void
	 */
	private function kill($crtId)
	{
		if ($this->debug)
		{
			echo "<br>Killing cart";
		}

		// delete all cart items
		$sql = "DELETE FROM `#__cart_cart_items` WHERE `crtId` = {$crtId}";

		$this->_db->setQuery($sql);
		$this->_db->query();

		// delete cart itself
		$sql = "DELETE FROM `#__cart_carts` WHERE `crtId` = {$crtId}";

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/* ------------------------------------------------------------ Transaction functions ----------------------------------------------------------- */

	/**
	 * Create new transaction
	 *
	 * @param void
	 * @return bool
	 */
	private function createTransaction()
	{
		// Clean old transactions
		$this->cleanOldTransactions();

		// Check if there are items in the cart
		if (empty($this->cart->items))
		{
			return false;
		}

		// Create transaction record
		$sql = "INSERT INTO `#__cart_transactions` SET `crtId` = {$this->crtId}, `tCreated` = NOW(), `tLastUpdated` = NOW(), `tStatus` = 'pending'";
		$this->_db->setQuery($sql);
		$this->_db->query();
		$tId = $this->_db->insertid();

		$this->cart->tId = $tId;

		// Create transaction info record
		$sql = "INSERT INTO `#__cart_transaction_info` SET `tId` = {$tId}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$this->_populateTransaction();
		return true;
	}

	/**
	 * Populate transaction items and required steps -- cart must be synced.
	 *
	 * @param void
	 * @return void
	 */
	private function _populateTransaction()
	{
		// Get all cart items
		$cartItems = $this->cart->items;

		// Add cart items to transaction
		$sqlValues = '';

		// Initialize required steps
		$steps = array();

		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$transactionSubtotalAmount = 0;

		foreach ($cartItems as $sId => $skuInfo)
		{
			if ($sqlValues)
			{
				$sqlValues .= ', ';
			}
			$sqlValues .= "({$this->cart->tId}, {$sId}, {$skuInfo['cartInfo']->qty}, {$skuInfo['info']->sPrice})";

			$transactionSubtotalAmount += ($skuInfo['info']->sPrice * $skuInfo['cartInfo']->qty);

			// check steps
			if ($skuInfo['info']->ptId == 1 && !in_array('shipping', $steps))
			{
				$steps[] = 'shipping';
			}
		}

		// lock items
		$warehouse->updateInventory($sId, $skuInfo['cartInfo']->qty, 'subtract');

		// populate items
		$sql = "INSERT INTO `#__cart_transaction_items` (`tId`, `sId`, `tiQty`, `tiPrice`) VALUES {$sqlValues}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		// populate steps
		foreach ($steps as $step)
		{
			$sql = "INSERT INTO `#__cart_transaction_steps` (`tId`, `tsStep`) VALUES ({$this->cart->tId}, '{$step}')";
			$this->_db->setQuery($sql);
			$this->_db->query();
		}

		// get perks
		$perks = $this->getCouponPerks();
		$perksTotalDiscount = $perks['info']->discountsTotal;

		// get memberships
		$membershipInfo = $this->getMembershipInfo();

		// Initialize Meta container for all related serialized info
		$meta['membershipInfo'] = $membershipInfo;

		// Update transaction info
		$sql = "UPDATE `#__cart_transaction_info`
				SET
				`tiPerks` = " . $this->_db->quote(serialize($perks)) . ",
				`tiMeta` = " . $this->_db->quote(serialize($meta)) . ",
				`tiItems` = " . $this->_db->quote(serialize($cartItems)) . ",
				`tiSubtotal` = " . $this->_db->quote($transactionSubtotalAmount) . ",
				`tiDiscounts` = " . $this->_db->quote($perksTotalDiscount) . "
				WHERE `tId` = " . $this->_db->quote($this->cart->tId);

		//print_r($sql); die;

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Get all items in the transaction
	 *
	 * @param int transaction ID
	 * @return array of items in the transaction, false if no items in transaction
	 */
	private function getTransactionItems($tId)
	{
		if ($this->debug)
		{
			echo "<br>Getting items from DB for transaction";
		}

		$sql = "SELECT `sId`, `tiQty`, `tiPrice` FROM `#__cart_transaction_items` ti WHERE ti.`tId` = {$tId}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		if (!$this->_db->getNumRows())
		{
			return false;
		}

		$allSkuInfo = $this->_db->loadObjectList('sId');
		$skus = $this->_db->loadResultArray();

		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		$skuInfo = $warehouse->getSkusInfo($skus);
		//print_r($skuInfo); die;

		// Update skuInfo with transaction info
		foreach ($skuInfo as $sId => $sku)
		{
			$transactionInfo->qty = $allSkuInfo[$sId]->tiQty;
			$transactionInfo->tiPrice = $allSkuInfo[$sId]->tiPrice;
			$skuInfo[$sId]['transactionInfo'] = $transactionInfo;
			unset($transactionInfo);
		}

		if (empty($skuInfo))
		{
			return false;
		}

		return $skuInfo;
	}


	/**
	 * Attempts to rebuild a transaction if there is enough inventory and prices have not changed
	 *
	 * @param void
	 * @return bool Success or failure
	 */
	private function rebuildTransaction()
	{
		// Go through each item in the transaction and see if it is available in quantity needed and if price has not changed
		$tItems = $this->getTransactionItems($this->cart->tId);

		foreach ($tItems as $item)
		{
			// Check price
			if ($item['info']->sPrice != $item['transactionInfo']->tiPrice)
			{
				// price changed
				return false;
			}
			// Check inventory
			if ($item['info']->sInventory < $item['transactionInfo']->qty)
			{
				// Not enough inventory
				return false;
			}
		}

		// lock transaction items
		include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();

		foreach ($tItems as $sId => $item)
		{
			$warehouse->updateInventory($sId, $item['transactionInfo']->qty, 'subtract');
		}

		$this->updateTransactionStatus('pending');

		return true;
	}

	/**
	 * Update current transaction status
	 *
	 * @param string status
	 * @return bool Success or failure
	 */
	public function updateTransactionStatus($status, $tId = NULL)
	{
		if (!$tId)
		{
			$tId = $this->cart->tId;
		}

		if (!$tId)
		{
			return false;
		}

		// update status
		$sql = "UPDATE `#__cart_transactions` SET `tStatus` = '{$status}' WHERE `tId` = {$tId}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$affectedRows = $this->_db->getAffectedRows();

		if(!$affectedRows) {
			return false;
		}

		return true;
	}

	/**
	 * Kill all cart transactions
	 *
	 * @param void
	 * @return void
	 */
	private function cleanOldTransactions()
	{
		if (empty($this->cart->crtId))
		{
			return true;
		}

		// Get all pending transactions for this cart
		$sql = "SELECT `tId` FROM `#__cart_transactions` WHERE `crtId` = {$this->cart->crtId} AND `tStatus` = 'pending'";
		$this->_db->setQuery($sql);
		$tIds = $this->_db->loadResultArray();

		foreach ($tIds as $tId)
		{
			$this->releaseTransaction($tId);
			$this->killTransaction($tId);
		}

		$this->cart->tId = NULL;
	}

	/**
	 * Kill transaction
	 *
	 * @param int tId transaction ID to kill
	 * @return void
	 */
	private function killTransaction($tId)
	{
			$sql = "DELETE FROM `#__cart_transactions` WHERE `tId` = {$tId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			$sql = "DELETE FROM `#__cart_transaction_items` WHERE `tId` = {$tId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			$sql = "DELETE FROM `#__cart_transaction_info` WHERE `tId` = {$tId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			$sql = "DELETE FROM `#__cart_transaction_steps` WHERE `tId` = {$tId}";
			$this->_db->setQuery($sql);
			$this->_db->query();
	}
}