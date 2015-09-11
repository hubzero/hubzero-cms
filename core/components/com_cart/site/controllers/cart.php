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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'CurrentCart.php');

/**
 * Cart controller class
 */
class CartControllerCart extends ComponentController
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
	 * Display default page
	 *
	 * @return     void
	 */
	public function homeTask()
	{
		$cart = new CartModelCurrentCart();

		// Initialize errors array
		$errors = array();

		// Update cart if needed
		$updateCartRequest = Request::getVar('updateCart', false, 'post');

		// If pIds are posted, convert them to SKUs
		$pIds = Request::getVar('pId', false, 'post');
		//print_r($pIds); die;
		$skus = Request::getVar('skus', false, 'post');

		if ($updateCartRequest && ($pIds || $skus))
		{
			if (!empty($pIds))
			{
				$skus = array();
				include_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
				$warehouse = new StorefrontModelWarehouse();

				foreach ($pIds as $pId => $qty)
				{
					$product_skus = $warehouse->getProductSkus($pId);

					// each pId must map to one SKU, otherwise ignored, since there is no way which SKU is being added
					// Must be only one sku...
					if (sizeof($product_skus) != 1)
					{
						continue;
					}

					$skus[$product_skus[0]] = $qty;
				}
			}
			else
			{
				if (!is_array($skus))
				{
					$skus = array($skus => 1);
				}
			}
			//print_r($skus); die;

			// Turn off syncing to prevent redundant session update queries
			$cart->setSync(false);
			foreach ($skus as $sId => $qty)
			{
				try
				{
					$cart->update($sId, $qty);
				}
				catch (Exception $e)
				{
					$cart->setMessage($e->getMessage(), 'error');
				}
			}

			// set flag to redirect
			$redirect = true;
			if ($cart->hasMessages())
			{
				$redirect = false;
			}
		}
		// Check if there is a delete request
		else
		{
			$allPost = Request::request();
			foreach ($allPost as $var => $val)
			{
				if ($val == 'delete')
				{
					$toDelete = explode('_', $var);
					//print_r($toDelete);	die;

					if ($toDelete[0] == 'delete')
					{
						$sId = $toDelete[1];
						// Delete the requested item by setting its QTY to zero
						$redirect = true;
						try
						{
							$cart->update($sId, 0);
						}
						catch (Exception $e)
						{
							$cart->setMessage($e->getMessage(), 'error');
							$redirect = false;
						}
					}
				}
			}
		}

		// Add coupon if needed
		$addCouponRequest = Request::getVar('addCouponCode', false, 'post');
		$couponCode = Request::getVar('couponCode', false, 'post');

		if ($addCouponRequest && $couponCode)
		{
			// Sync cart before pontial coupons applying
			$cart->getCartInfo(true);

			// Add coupon
			try
			{
				$cart->addCoupon($couponCode);
			}
			catch (Exception $e)
			{
				$cart->setMessage($e->getMessage(), 'error');
			}

			// set flag to redirect
			$redirect = true;
			if ($cart->hasMessages())
			{
				$redirect = false;
			}
		}

		// Check for express add to cart
		if (!empty($redirect) && $redirect)
		{
			// If this is an express checkout (go to the confirm page right away) there shouldn't be any items in the cart
			// Since redirect is set, there are no errors
			$expressCheckout = Request::getVar('expressCheckout', false, 'post');

			// make sure the cart is empty
			if ($expressCheckout && !empty($skus) && $cart->isEmpty())
			{
				// Get the latest synced cart info, it will also enable cart syncing that was turned off before
				$cart->getCartInfo(true);

				// Redirect directly to checkout, skip the cart page
				$redirect_url  = Route::url('index.php?option=' . 'com_cart') . DS . 'checkout';
				App::redirect($redirect_url);
			}

			// prevent resubmitting form by refresh
			// redirect to cart
			$redirect_url = Route::url('index.php?option=' . 'com_cart');
			App::redirect($redirect_url);
		}

		// Get the latest synced cart info, it will also enable cart syncing that was turned off before
		$cartInfo = $cart->getCartInfo(true);
		$this->view->cartInfo = $cartInfo;

		// Handle coupons
		$couponPerks = $cart->getCouponPerks();
		//print_r($couponPerks); die;
		$this->view->couponPerks = $couponPerks;

		// Handle memberships
		$membershipInfo = $cart->getMembershipInfo();
		//print_r($membershipInfo); die;
		$this->view->membershipInfo = $membershipInfo;

		// At this point the cart is lifted and may have some issues/errors (say, after merging), get them
		if ($cart->hasMessages())
		{
			$cartMessages = $cart->getMessages();
			$this->view->notifications = $cartMessages;
		}

		$this->view->display();
	}
}

