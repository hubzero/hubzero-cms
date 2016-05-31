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

namespace Components\Cart\Models;

use Components\Cart\Models\Cart;
use Components\Cart\Helpers\CartHelper;
use Hubzero\Base\Model;
use User;

require_once 'Cart.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'Helper.php';

/**
 * Current user shopping cart
 */
class CurrentCart extends Cart
{
	// Session cart
	var $cart = NULL;

	// Syncing enabled?
	var $sync = true;

	// Cookie max age
	var $cookieTTL = 7776000; // 60 * 60 * 24 * 90 = 90 days

	/**
	 * Cart constructor
	 *
	 * @param void
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		// Get user
		$user = User::getInstance();

		//Set the user scope
		$this->warehouse->addAccessLevels($user->getAuthorisedViewLevels());

		$this->cart = new \stdClass();

		/* Load current user cart */

		/* Check if there is a session or cookie cart */

		// Get cart from session
		$cart = $this->liftSessionCart();

		// If no session cart, try to locate a cookie cart (only for not logged in users)
		if (!$cart && $user->isGuest())
		{
			$cart = $this->liftCookie();
		}

		if ($cart)
		{
			// If cart found and user is logged in, verify if the cart is linked to the user cart in the DB
			if (!$user->isGuest())
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
			} // Make sure cart is marked as unlinked is the user is not logged in
			else
			{
				$this->cart->linked = 0;
			}
		} // If no session & cookie cart found, but user is logged in
		elseif (!$user->isGuest())
		{
			// Try to get the saved cart in the DB
			if (!$this->liftUserCart($user->get('id')))
			{
				// If no session, no cookie, no DB cart -- create a brand new cart
				$this->createCart();
			}
		} // No session, no cookie -- create new cart
		else
		{
			$this->createCart();
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
		parent::add($sId, $qty);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Update/set SKU in cart
	 *
	 * @param int   SKU ID
	 * @param int   Quantity
	 * @param bool  Retain old value
	 * @return void
	 */
	public function update($sId, $qty = 1, $retainOldValue = false)
	{
		parent::update($sId, $qty, $retainOldValue);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Delete SKU from cart
	 *
	 * @param SKU ID
	 * @return void
	 */
	public function delete($sId)
	{
		parent::delete($sId);

		// Update session
		$this->syncSessionCart();
	}

	/**
	 * Get session info about cart
	 *
	 * @param bool $updateDb Flag whether the session cart should be synced with DB first
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
	 * Check if the cart is empty
	 *
	 * @param void
	 * @return bool
	 */
	public function isEmpty()
	{
		return (empty($this->getCartInfo()->items));
	}

	/**
	 * Get any changes to cart items' inventory or pricing since last visit.
	 * Works like a flash variable -- gets messages once and then resets the state
	 * @param false
	 * @return array of change messages
	 */
	private function getCartChanges()
	{
		// Load cart items info
		$sql = "SELECT * FROM `#__cart_cart_items` crti WHERE crti.`crtId` = {$this->crtId}";
		$this->_db->setQuery($sql);
		$items = $this->_db->loadObjectList();

		// Initiate changes array
		$changes = array();

		$cartItems = $this->cart->items;

		foreach ($items as $item)
		{
			// Build item name
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
				$changes[] = array($itemName . ' is no longer available', 'info');

				// skip the rest
				continue;
			}

			if (!empty($item->crtiOldQty) && $item->crtiOldQty > $item->crtiQty)
			{
				if ($item->crtiQty)
				{
					$changes[] = array($itemName . ' inventory reduced from ' . $item->crtiOldQty . ' to ' . $item->crtiQty, 'info');
				} else
				{
					$changes[] = array($itemName . ' is no longer in stock', 'info');
				}
			}

			if (!empty($item->crtiOldPrice) && $item->crtiOldPrice != $item->crtiPrice)
			{
				$changes[] = array($itemName . ' price changed from ' . $item->crtiOldPrice . ' to ' . $item->crtiPrice, 'info');
			}
		}

		// Reset changes flag
		$this->cart->hasChanges = false;

		// Reset all messages
		if (!empty($changes))
		{
			// Delete zero inventory items and unavailable SKUs
			$sql = "DELETE FROM `#__cart_cart_items` WHERE (`crtiQty` = 0  OR `crtiAvailable` = 0) AND `crtId` = {$this->crtId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			// Clear old info since the message has already been displayed
			$sql = "UPDATE `#__cart_cart_items` SET `crtiOldQty` = NULL, `crtiOldPrice` = NULL WHERE `crtId` = {$this->crtId}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			$this->updateSession();
			return $changes;
		}

		throw new \Exception('Failed attempt to get cart changes. No changes detected. This shouldn\'t happen.');
		return false;
	}

	/**
	 * Check if there are any changes to cart items' inventory or pricing since last visit
	 * @param false
	 * @return bool
	 */
	private function cartChanged()
	{
		return (!empty($this->cart->hasChanges) && $this->cart->hasChanges);
	}

	/**
	 * Check if there are any messages to display (cart changes or other set messages)
	 * @param   void
	 * @return  bool
	 */
	public function hasMessages()
	{
		return ((!empty($this->cart->hasMessages) && $this->cart->hasMessages) ||
				$this->cartChanged()
		);
	}

	/**
	 * Set a cart message
	 * @param   string  Message
	 * @param   string  Message type, used to set the CSS class name
	 * @return  void
	 */
	public function setMessage($msg, $type = 'info')
	{
		$this->cart->messages[] = array($msg, $type);
		$this->cart->hasMessages = true;
	}

	/**
	 * Get all cart messages, both changes and set messages
	 * Works like a flash variable -- gets messages once and then resets the state/messages
	 * @param   void
	 * @return  void
	 */
	public function getMessages()
	{
		if (!$this->hasMessages())
		{
			return false;
		}

		// Get messages
		$messages = array();
		if ((!empty($this->cart->hasMessages) && $this->cart->hasMessages))
		{
			$messages = $this->cart->messages;
			// Reset messages flag
			$this->cart->hasMessages = false;
			// Erase messages
			unset($this->cart->messages);
		};

		// Get cart changes
		$changes = array();
		if ($this->cartChanged())
		{
			$changes = $this->getCartChanges();
		}

		// Combine set messages and changes messages
		$messages = array_merge($messages, $changes);

		return $messages;
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
			$redirect_url  = Route::url('index.php?option=' . 'com_cart');
		}
		else
		{
			$redirect_url  = Route::url('index.php?option=' . 'com_cart') . 'checkout/' . $where;
		}

		App::redirect(
				$redirect_url
		);
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
		$sql = "SELECT `tsStep`, `tsMeta` FROM `#__cart_transaction_steps` ts WHERE ts.`tId` = {$this->cart->tId} AND ts.`tsStatus` < 1 ORDER BY tsId DESC";
		$this->_db->setQuery($sql);
		$nextStep = $this->_db->loadObject();

		// Initialize stepInfo
		$stepInfo = new \stdClass();

		if (empty($nextStep))
		{
			$stepInfo->step = 'summary';
		}
		else {
			$stepInfo->step = $nextStep->tsStep;
			$stepInfo->meta = $nextStep->tsMeta;
		}

		return $stepInfo;
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
		$steps = $this->_db->loadColumn();

		return $steps;
	}

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
		$tInfo = $this->getTransactionData();

		if (!$tInfo || $tInfo->tAge > $this->transactionKillAge)
		{
			// No transaction found
			return false;
		}

		// Only pending and released transactions can be lifted
		if (!$tInfo || ($tInfo->tStatus != 'pending' && $tInfo->tStatus != 'released'))
		{
			return false;
		}

		// See if transaction is expired
		if ($tInfo->tAge > $this->transactionTTL)
		{
			// If transaction has not yet been processed as expired (status is still 'pending') release the transaction
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
		$transaction = new \stdClass();
		$transaction->items = $this->getTransactionItems($this->cart->tId);

		if (!empty($transaction->items))
		{
			// Calculate the important numbers for the transaction
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
			$fieldValue = Request::getVar($field, false, 'post');
			if (empty($fieldValue))
			{
				$errors[] = Lang::txt('COM_CART_FILL_REQUIRED_FIELDS');
				break;
			}
		}

		// Check values
		if (empty($errors) && !CartHelper::validZip(Request::getVar('shippingZip', false, 'post', 'string')))
		{
			$errors[] = Lang::txt('COM_CART_INCORRECT_ZIP');
		}

		// Init return object
		$ret = new \stdClass();

		if (empty($errors))
		{
			// save shipping info
			$shippingToFirst = $this->_db->quote(Request::getVar('shippingToFirst', false, 'post', 'string'));
			$shippingToLast = $this->_db->quote(Request::getVar('shippingToLast', false, 'post', 'string'));
			$shippingAddress = $this->_db->quote(Request::getVar('shippingAddress', false, 'post', 'string'));
			$shippingCity = $this->_db->quote(Request::getVar('shippingCity', false, 'post', 'string'));
			$shippingState = $this->_db->quote(Request::getVar('shippingState', false, 'post', 'string'));
			$shippingZip = $this->_db->quote(Request::getVar('shippingZip', false, 'post', 'string'));

			if ($this->debug)
			{
				echo '<br>saving transaction shipping info';
			}

			$sqlUpdateValues = "`tiShippingToFirst` = {$shippingToFirst}, `tiShippingToLast` = {$shippingToLast},
								`tiShippingAddress` = {$shippingAddress}, `tiShippingCity` = {$shippingCity},
								`tiShippingState` = {$shippingState}, `tiShippingZip` = {$shippingZip}";

			$sql = "INSERT INTO `#__cart_transaction_info`
					SET `tId` = {$this->cart->tId}, {$sqlUpdateValues}
					ON DUPLICATE KEY UPDATE {$sqlUpdateValues}";
			$this->_db->setQuery($sql);
			$this->_db->query();

			$saveAddress = $this->_db->quote(Request::getVar('saveAddress', false, 'post', 'string'));
			// Save the address for future use if requested
			if ($saveAddress)
			{
				// Update DB prefix
				$sqlUpdateValues = str_replace('tiShipping', 'sa', $sqlUpdateValues);

				// Get user
				$uId = User::get('id');

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
			throw new \Exception(Lang::txt('No transaction info.'));
		}

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

		$sql = "UPDATE `#__cart_transaction_info` SET
				`tiShipping` = " . $this->_db->quote($shippingCost) . ",
				`tiShippingDiscount` = " . $this->_db->quote($shippingDiscountAmount) . "
				WHERE `tId` = " . $this->_db->quote($this->cart->tId);

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Set the meta information for a given transaction item
	 *
	 * @param int 		sId		SKU id of the item that needs to e updated
	 * @param mixed		meta	Meta info
	 * @return void
	 */
	public function setTransactionItemMeta($sId, $meta)
	{
		$sql = "UPDATE `#__cart_transaction_items` SET
				`tiMeta` = " . $this->_db->quote($meta) . "
				WHERE `tId` = " . $this->_db->quote($this->cart->tId) . "
				AND `sId` = " . $this->_db->quote($sId);

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Gets all transaction related info about current cart transaction
	 *
	 * @param void
	 * @return object, false on no results
	 */
	public function getTransactionData()
	{
		// If session expired tId will not be saved
		if (empty($this->cart->tId))
		{
			// Try to find if there is a pending transaction for this cart in DB and use it
			$sql = "SELECT `tId` FROM `#__cart_transactions` WHERE `crtId` = {$this->cart->crtId} AND `tStatus` = 'pending'";
			$this->_db->setQuery($sql);
			$tId = $this->_db->loadResult();
		}
		else
		{
			$tId = $this->cart->tId;
		}

		if (!$tId)
		{
		  return false;
		}

		// Get info
		$transactionInfo = parent::getTransactionInfo($tId);

		// Set transaction id session value (needed for expired session)
		$this->cart->tId = $transactionInfo->tId;

		// Get steps
		$steps = $this->getCheckoutSteps();
		$transactionInfo->steps = $steps;

		return $transactionInfo;
	}

	/**
	 * Set customer status
	 *
	 * @param	string 	$status new status
	 * @param	int 	$tId transaction ID
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

		if (!$affectedRows)
		{
			return false;
		}

		return true;
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
		if (!CartHelper::isNonNegativeInt($saId))
		{
			throw new \Exception(Lang::txt('COM_CART_INCORRECT_SAVED_SHIPPING_ADDRESS'));
		}

		$sql = "SELECT * FROM `#__cart_saved_addresses` WHERE `saId` = " . $this->_db->quote($saId);
		$this->_db->setQuery($sql);
		$this->_db->query();

		if ($this->_db->getNumRows() < 1)
		{
			throw new \Exception(Lang::txt('COM_CART_INCORRECT_SAVED_SHIPPING_ADDRESS'));
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
		$this->_db->query();

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
			throw new \Exception(Lang::txt(COM_CART_NO_TRANSACTION_FOUND));
		}

		return md5(parent::$securitySalt . $this->tInfo->tId);
	}

	/**
	 * Verify security token
	 *
	 * @param 		string	token
	 * @return		bool
	 */
	public function verifyToken($token, $tId = false)
	{
		if (empty($tId))
		{
			if (empty($this->tInfo))
			{
				throw new \Exception(Lang::txt(COM_CART_NO_TRANSACTION_FOUND));
			}
			$tId = $this->tInfo->tId;
		}

		return parent::verifySecurityToken($token, $tId);
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
			throw new \Exception(Lang::txt(COM_CART_NO_TRANSACTION_FOUND));
		}

		$tiTotal = $this->tInfo->tiSubtotal + $this->tInfo->tiShipping - $this->tInfo->tiShippingDiscount - $this->tInfo->tiDiscounts;

		$sql = "UPDATE `#__cart_transaction_info` SET
				`titotal` = " . $this->_db->quote($tiTotal) . "
				WHERE `tId` = " . $this->_db->quote($this->cart->tId);

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Mark step for current transaction as completed (default) or not-completed
	 *
	 * @param 	string Step
	 * @param 	mixed Meta key to match the step among multiple of the same type (eg transaction can have multiple EULA steps for each SKU)
	 * @param 	bool Completed (true) ar not completed (false)
	 * @return 	bool
	 */
	public function setStepStatus($step, $meta = '', $status = true)
	{
		$sql = "UPDATE `#__cart_transaction_steps`
				SET `tsStatus` = " .  $this->_db->quote($status) . "
				WHERE `tId` = {$this->cart->tId} AND `tsStep` = '{$step}'";
		if (!empty($meta))
		{
			$sql .= "AND `tsMeta` = '{$meta}'";
		}
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}


	/********************************************* Coupon functions **********************************************/

	/**
	 * Add coupon to cart
	 * @param 	string		$couponCode coupon code
	 * @return	bool		true on sucess
	 */
	public function addCoupon($couponCode)
	{
		// Check if coupon is valid and active (throws exception if invalid)
		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php';
		$coupons = new \Components\Storefront\Models\Coupons();

		// Get coupons
		$this->cartCoupons = $this->getCoupons();

		// Check if coupon has already been applied
		if ($this->isCouponApplied($couponCode))
		{
			throw new \Exception(Lang::txt('COM_CART_COUPON_ALREADY_APPLIED'));
		}

		$cnId = $coupons->isValid($couponCode);

		// Apply coupon, add item to cart if needed/possible (throws exception if not applicable)
		$this->applyCoupon($cnId);

		// If user is logged in subtract coupon use count. If not logged in subtraction will happen when user logs in
		if (User::get('id'))
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
	public function applyCoupon($cnId)
	{
		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php';
		$storefrontCoupons = new \Components\Storefront\Models\Coupons();
		$coupon = $storefrontCoupons->getCouponInfo($cnId, true, true, true, true);

		if (!$coupon->info->itemCoupon)
		{
			// All non-item coupons apply
			return true;
		}

		$cartInfo = $this->getCartInfo();
		$cartItems = $cartInfo->items;

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

		// Only one object may be defined to map to a single SKU
		if (sizeof($coupon->objects) == 1)
		{
			$couponObject = $coupon->objects[0];

			if ($coupon->info->cnObject == 'sku')
			{
				// Add SKU to cart
				$this->add($couponObject->cnoObjectId);
				return true;
			}
			elseif ($coupon->info->cnObject == 'product')
			{
				// Check product SKUs
				$warehouse = $this->warehouse;
				$productOptions = $warehouse->getProductOptions($couponObject->cnoObjectId);

				// See if the product has only one SKU, then add this SKU to cart (There is no way do decide what SKU to add if there are several of them)
				if (sizeof($productOptions->skus) == 1)
				{
					// Get product's SKU
					$sId = array_shift($productOptions->skus);
					$sId = $sId['info']->sId;

					// Add SKU to cart
					$this->add($sId);
					return true;
				}
			}
		}

		// Coupon is not applicable
		throw new \Exception(Lang::txt('COM_CART_CANNOT_APPLY_COUPON'));
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
		$cnIds = $this->_db->loadColumn();

		// Get coupon types
		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php';
		$storefrontCoupons = new \Components\Storefront\Models\Coupons();
		$coupons = $storefrontCoupons->getCouponsInfo($cnIds);

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

		// Iterate through coupons and return true if coupon already applied
		foreach ($this->cartCoupons as $coupon)
		{
			if ($coupon->cnCode == $cnCode)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Do the maintenance of coupons -- make sure all coupons are valid and in order, remove unnecessary coupons...
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

		// Since coupons in $cartCoupons are expected to be ordered with item coupons coming first,
		// item coupons will be processed first in bulk
		// set the flag if currently processing item coupons
		$itemCoupon = true;

		// Initialize $itemsDiscountsTotal -- the total sum of all item discount amounts
		$itemsDiscountsTotal = 0;
		// Initialize $genericDiscountsTotal -- the total sum of all non-item discounts
		$genericDiscountsTotal = 0;

		/*
			Initialize a global SKU/Other object to coupon mapping.
			The coupons are ordered by time applied, so if there is more than one coupon for one SKU,
			we need to apply the most recently applied only. As we iterate through coupons we only want to record the recent one.
			Same for non item coupons -- there is only one coupon per object type is allowed (shipping, total order discount, etc...)
		*/
		$couponMappings = array();

		/*
			Initialize a global SKU to discount mapping.
			Just like coupons we need to apply only the most recently applied discount and discard old ones
		*/
		$itemCouponDiscountMappings = array();

		// go through each coupon in the cart
		foreach ($cartCoupons as $cn)
		{
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

			$storefrontCoupons = new \Components\Storefront\Models\Coupons();
			$coupon = $storefrontCoupons->getCouponInfo($cn->cnId, $itemCoupon); // Load objects for itemCoupons only

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
					throw new \Exception(Lang::txt('Invalid coupon. Invalid object type.'));
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
					throw new \Exception(Lang::txt('Invalid coupon. Invalid discount amount ' . $couponDiscount . '.'));
				}

			}
			else {
				throw new \Exception(Lang::txt('Invalid coupon. Invalid action type.'));
			}

			// Check if we need to match against the object type to make sure the coupon is applicable
			if ($itemCoupon)
			{
				// check if there are options available
				if (empty($coupon->objects))
				{
					throw new \Exception(Lang::txt('Invalid coupon. No object found.'));
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
							$perk = new \stdClass();
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
								TODO Also see a note about figuring out the most expensive items if there is a limit.
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
								throw new \Exception(Lang::txt('Invalid coupon. Only discounts are available for SKUs and products'));
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
			elseif ($couponObjectType != 'shipping')
			{
				// Initialize the perk
				unset($perk);
				$perk = new \stdClass();
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
					Also see a note about figuring out the most expensive items if there is a limit.
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
			elseif ($couponObjectType == 'shipping')
			{
				// Initialize the perk
				unset($perk);
				$perk = new \stdClass();
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
			At this point $couponMappings has all the coupons that need to be applied to cart -- all other unused coupons have to be released
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
				$this->removeCoupon($cn->cnId);
			}
		}

		$perksInfo = new \stdClass();
		$perksInfo->itemsDiscountsTotal = $itemsDiscountsTotal;
		$perksInfo->genericDiscountsTotal = $genericDiscountsTotal;
		$perksInfo->discountsTotal = $itemsDiscountsTotal + $genericDiscountsTotal;
		$perks['info'] = $perksInfo;

		return $perks;
	}

	/**
	 * Remove coupon from cart
	 * @param 	int		$cnId coupon ID
	 * @return	bool		true on sucess
	 */
	public function removeCoupon($cnId)
	{
		$coupons = new Coupons;

		// If user is logged in return coupon back to the coupons pool.
		if (User::get('id'))
		{
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

		$cartItems = $cartInfo->items;

		// init membership info
		$memberships = array();

		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php';
		$ms = new \Components\Storefront\Models\Memberships();

		// Get membership types
		$membershipTypes = $ms->getMembershipTypes();

		// Go through each product and see if the type is membership
		foreach ($cartItems as $sId => $item)
		{
			if (in_array($item['info']->ptId, $membershipTypes) && !empty($item['meta']['ttl']))
			{
				$itemInfo = $item['info'];

				// Get product type
				$warehouse = $this->warehouse;
				$pType = $warehouse->getProductTypeInfo($itemInfo->ptId);
				$type = $pType['ptName'];

				// Get the correct membership Object
				$subscription = \Components\Storefront\Models\Memberships::getSubscriptionObject($type, $itemInfo->pId, User::get('id'));
				// Get the expiration for the current subscription (if any)
				$currentExpiration = $subscription->getExpiration();

				// Calculate new expiration
				$newExpires = \Components\Storefront\Models\Memberships::calculateNewExpiration($currentExpiration, $item);

				$membershipSIdInfo = new \stdClass();
				$membershipSIdInfo->newExpires = strtotime($newExpires);

				if ($currentExpiration && $currentExpiration['crtmActive'])
				{
					$membershipSIdInfo->existingExpires = strtotime($currentExpiration['crtmExpires']);
				}

				$memberships[$sId] = $membershipSIdInfo;

				unset($membershipSIdInfo);
			}

		}
		return $memberships;
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
		if (!$this->exists())
		{
			$this->clearSessionCart();
			$this->createCart();
			// call itself
			return $this->getItems();
		}
		return $this->getUpdatedItems();
	}

	/**
	 * Update cart based on current availability (and pricing) and return all items in the cart, update cart's 'lastUpdated' field
	 *
	 * @param void
	 * @return array of items in the cart
	 */
	private function getUpdatedItems()
	{
		// Get cart items from the database
		$items = $this->getCartItems();
		$allSkuInfo = $items->allSkuInfo;
		$skus = $items->skus;

		$warehouse = $this->warehouse;

		$skuInfo = $warehouse->getSkusInfo($skus);

		$cartItems = array();

		// Note that some items become unavailable/out of stock or prices may change, need to account for this
		foreach ($allSkuInfo as $sId => $sku)
		{
			// See if the SKU is available at all
			if (empty($skuInfo[$sId]))
			{
				// SKU became unavailable, set it as such (there is a difference between unavailable and out of stock)
				$this->markItemUnavailable($sId);
				$this->cart->hasChanges = true;

				continue;
			}

			$inf = $skuInfo[$sId]['info'];
			$cartItems[$sId] = $skuInfo[$sId];

			$updated = false;

			$cartInfo = new \stdClass();

			// Check if this item has been already processed by the doItem (i.e. through cart update quantities call)
			if (!empty($sku->crtiOldPrice) && $sku->crtiPrice != $sku->crtiOldPrice)
			{
				// Set card changed flag
				$this->cart->hasChanges = true;
				// Mark as updated to prevent double updating
				$updated = true;
			}

			// Check if there is enough inventory
			if ($inf->sTrackInventory && ($inf->sInventory < $sku->crtiQty) && !$updated)
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

			// Check if allowMultiple policy changed
			if (!$inf->sAllowMultiple && $sku->crtiQty > 1 && !$updated)
			{
				$this->doItem($sId, 'set', 1, true);
				$cartInfo->qty = 1;

				// Set card changed flag
				$this->cart->hasChanges = true;

				// Mark as updated to prevent double updating
				$updated = true;
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

		$session = \App::get('session'); //Factory::getSession();
		$cart = $session->get('cart');

		if ($cart && !empty($cart->crtId))
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
	 * Update session with instance cart info (no product syncing)
	 *
	 * @param void
	 * @return void
	 */
	private function updateSession()
	{
		$session = \App::get('session');
		$session->set('cart', $this->cart);
	}

	/**
	 * Clear session cart
	 *
	 * @param void
	 * @return void
	 */
	private function clearSessionCart()
	{
		$session = \App::get('session');
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
		$crtId = Request::getVar('cartId', '', 'COOKIE');

		if (!empty($crtId) && !$this->cartIsLinked($crtId))
		{
			// Load cart
			return $this->liftCart($crtId);
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
			if ($this->liftCart($crtId))
			{
				$this->cart->linked = 1;
				return true;
			}
		}
		return false;
	}

	/**
	 * Lift requested cart
	 * For logged in users use liftUserCart function instead.
	 *
	 * @param int $crtId Cart ID
	 * @return bool
	 */
	private function liftCart($crtId)
	{
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

		$uId = 'NULL';
		$cart = new \stdClass();
		if (!User::isGuest())
		{
			$uId = User::get('id');
			$cart->linked = 1;
		}
		else {
			$cart->linked = 0;
		}

		$sql = "INSERT INTO `#__cart_carts` SET `crtCreated` = NOW(), `crtLastUpdated` = NOW(), `uidNumber` = {$uId}";
		$this->_db->setQuery($sql);
		$this->_db->query();
		$crtId = $this->_db->insertid();

		$session = \App::get('session');
		$cart->crtId = $crtId;
		$this->crtId = $cart->crtId;
		$session->set('cart', $cart);

		// Set cookie for non logged-in users to recover the cart
		if (User::isGuest())
		{
			if ($this->debug)
			{
				echo "<br>Setting a cookie";
			}
			setcookie("cartId", $crtId, time() + $this->cookieTTL); // Set cookie life time
		}

		// Update session cart
		$this->syncSessionCart();
	}

	/**
	 * Link a session cart with user's cart. If these carts are different -- merge them.
	 * If there is only a session cart -- make it the user's cart
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

		//print_r($this); die;

		// Kill the old cookie
		setcookie("cartId", '', time() - $this->cookieTTL); // Set the cookie lifetime in the past

		// Get user
		$user = User::getInstance();

		// Check if session cart is not someone else's. Otherwise load user's cart and done
		if ($this->cartIsLinked($this->crtId))
		{
			if (!$this->liftUserCart($user->get('id')))
			{
				return false;
			}
			return true;
		}

		// Get user's cart
		$userCartId = $this->getUserCartId($user->get('id'));

		// Get coupons
		$coupons = $this->getCoupons();

		// If no user cart -- make the session cart a user's cart. Easy.
		if (!$userCartId)
		{
			$sql = "UPDATE `#__cart_carts` SET `uidNumber` = {$user->id} WHERE `crtId` = {$this->crtId}";
			$this->_db->setQuery($sql);
			$this->_db->query();
			$existingCnIds = array();
		}
		// Merge session and user carts. Not so easy.
		else
		{
			// Get a static instance of the users' cart
			require_once(__DIR__ . DS . 'UserCart.php');
			$userCart = new UserCart($userCartId);
			// Get items from the user's cart to see if it is empty or nor
			$userCartItems = $userCart->getCartItems();

			// If both session and user carts are not empty notify the user that items are being combined
			if (!$this->isEmpty() && !empty($userCartItems))
			{
				$this->cart->messages[] = array(Lang::txt('COM_CART_ITEMS_COMBINED'), 'info');
				$this->cart->hasMessages = true;
			}

			if (!$this->isEmpty())
			{
				$sessionCartItems = $this->getUpdatedItems();

				// Go through all session cart items (if any) and add them to the user's cart
				foreach ($sessionCartItems as $item)
				{
					try
					{
						$userCart->add($item['info']->sId, $item['cartInfo']->qty);
					}
					catch (\Exception $e)
					{
						$this->cart->messages[] = array($e->getMessage(), 'warning');
						$this->cart->hasMessages = true;
					}
				}
			}

			// need to apply each coupon (mark as used) before merging
			$cnSql = '0';
			$allCouponsIds = array();
			foreach ($coupons as $coupon)
			{
				$cnSql .= ',' . $coupon->cnId;
				$allCouponsIds[] = $coupon->cnId;
			}

			// Find all coupons in the user's cart that are already applied and don't need to be reapplied
			$sql = "SELECT `cnId` FROM `#__cart_coupons`
					WHERE `crtId` = " . $this->_db->quote($userCartId) . "
					AND `cnId` IN (" . $cnSql . ") AND `crtCnStatus` = 'active'";
			$this->_db->setQuery($sql);
			$this->_db->query();
			$existingCnIds = $this->_db->loadColumn();

			// merge coupons
			$couponsIdsToMerge = array_diff($allCouponsIds, $existingCnIds);
			$mergeSql = '0';
			foreach ($couponsIdsToMerge as $cnId)
			{
				$mergeSql .= ',' . $cnId;
			}
			$sql = "INSERT INTO `#__cart_coupons` (`crtId`, `cnId`, `crtCnAdded`, `crtCnStatus`)
					SELECT {$userCartId}, `cnId`, `crtCnAdded`, 'active' FROM `#__cart_coupons` cc
					WHERE cc.crtId = {$this->crtId} AND `cnId` IN (" . $mergeSql . ")";
			$this->_db->setQuery($sql);
			$this->_db->query();

			// kill old cart
			$this->kill($this->crtId);

			// Update current cart ID
			$this->crtId = $userCartId;
		}

		require_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupons.php');
		$storefrontCoupons = new \Components\Storefront\Models\Coupons();

		// Go through each coupon and apply all that are not applied
		// (if merging, or all if making the session cart a user's cart)
		foreach ($coupons as $coupon)
		{
			if (!in_array($coupon->cnId, $existingCnIds))
			{
				$storefrontCoupons->apply($coupon->cnId);
			}
		}

		// Mark cart as linked
		$this->cart->linked = 1;
		// Update session
		$this->syncSessionCart();

		return true;
	}

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

		$this->populateTransaction();
		return true;
	}

	/**
	 * Populate transaction items and required steps -- cart must be synced.
	 *
	 * @param void
	 * @return void
	 */
	private function populateTransaction()
	{
		// Get all cart items
		$cartItems = $this->cart->items;

		// Add cart items to transaction
		$sqlValues = '';

		// Initialize required steps, split it into logical parts
		$preSteps = array();
		$postSteps = array();

		$warehouse = $this->warehouse;

		$transactionSubtotalAmount = 0;

		//print_r($cartItems); die;

		foreach ($cartItems as $sId => $skuInfo)
		{
			if ($sqlValues)
			{
				$sqlValues .= ', ';
			}
			$sqlValues .= "({$this->cart->tId}, {$sId}, {$skuInfo['cartInfo']->qty}, {$skuInfo['info']->sPrice})";

			$transactionSubtotalAmount += ($skuInfo['info']->sPrice * $skuInfo['cartInfo']->qty);

			/* Steps */

			// EULA: for software, license agreement may be needed, if so, add the step for each product

			// get product type
			$productInfo = $warehouse->getProductInfo($skuInfo['info']->pId);
			// if soft, check if EULA is needed
			if ($productInfo->ptModel == 'software')
			{
				$productMeta = $warehouse->getProductMeta($skuInfo['info']->pId);
				// If EULA is needed, add step, note the EULA required for each SKU, so for multiple SKUs of the same product, multiple EULA will be required
				if (!empty($productMeta['eulaRequired']) && $productMeta['eulaRequired']->pmValue)
				{
					$step = new \stdClass();
					$step->name = 'eula';
					$step->meta = $sId;
					$preSteps[] = $step;
				}
			}

			// shipping: if any of the products require shipping, add the shipping step
			if ($skuInfo['info']->ptId == 1 && !in_array('shipping', $postSteps))
			{
				$step = new \stdClass();
				$step->name = 'shipping';
				$postSteps[] = $step;
			}

			// Reserve/lock items
			require_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php');
			$sku = \Components\Storefront\Models\Sku::getInstance($sId);
			//print_r($sku); die;
			$sku->reserveInventory($skuInfo['cartInfo']->qty);
			$sku->save();
			//$warehouse->updateInventory($sId, $skuInfo['cartInfo']->qty, 'reserve');
		}

		// populate items
		$sql = "INSERT INTO `#__cart_transaction_items` (`tId`, `sId`, `tiQty`, `tiPrice`) VALUES {$sqlValues}";
		$this->_db->setQuery($sql);
		$this->_db->query();

		// merge pre- and post- steps to ensure the correct order
		$steps = array_merge($preSteps, $postSteps);
		// populate steps
		foreach ($steps as $step)
		{
			if (empty($step->meta))
			{
				$step->meta = '';
			}
			$sql = "INSERT INTO `#__cart_transaction_steps` (`tId`, `tsStep`, `tsMeta`)
					VALUES ({$this->cart->tId}, '{$step->name}', '{$step->meta}')";
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

		$this->_db->setQuery($sql);
		$this->_db->query();
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
		$warehouse = $this->warehouse;

		require_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Sku.php');

		foreach ($tItems as $sId => $item)
		{
			$sku = \Components\Storefront\Models\Sku::getInstance($sId);
			$sku->reserveInventory($item['transactionInfo']->qty);
			$sku->save();
			//$warehouse->updateInventory($sId, $item['transactionInfo']->qty, 'subtract');
		}

		parent::updateTransactionStatus('pending', $this->cart->tId);

		return true;
	}

	/**
	 * Kill all cart pending transactions
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
		$tIds = $this->_db->loadColumn();

		if (is_array($tIds))
		{
			foreach ($tIds as $tId)
			{
				$this->releaseTransaction($tId);
				parent::killTransaction($tId);
			}
		}
		$this->cart->tId = NULL;
	}
}