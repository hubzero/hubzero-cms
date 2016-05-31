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

use Request;
use Components\Cart\Models\CurrentCart;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'CurrentCart.php';

/**
 * Cart controller class
 */
class Cart extends ComponentController
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
		$cart = new CurrentCart();

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

				$warehouse = new Warehouse();

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
				catch (\Exception $e)
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

					if ($toDelete[0] == 'delete')
					{
						$sId = $toDelete[1];
						// Delete the requested item by setting its QTY to zero
						$redirect = true;
						try
						{
							$cart->update($sId, 0);
						}
						catch (\Exception $e)
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
			catch (\Exception $e)
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
				App::redirect(
					Route::url('index.php?option=' . $this->_option) . DS . 'checkout'
				);
			}

			// prevent resubmitting form by refresh
			// redirect to cart
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
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

		if (Pathway::count() <= 0)
		{
			Pathway::append(
					Lang::txt(strtoupper($this->_option)),
					'index.php?option=' . $this->_option
			);
		}

		$this->view->display();
	}
}

