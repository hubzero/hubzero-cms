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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
		$this->_task  = JRequest::getVar('task', '');

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
		require_once(JPATH_COMPONENT . DS . 'models' . DS . 'cart.php');
		$cart = new CartModelCart();

		// update cart if needed for non-ajax transactions
		$updateCartRequest = JRequest::getVar('updateCart', false, 'post');

		$pIds = JRequest::getVar('pId', false, 'post');

		//print_r($pIds); die;

		// If pIds are posted, convert them to SKUs
		if (!empty($pIds))
		{
			$skus = array();
			include_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
			$warehouse = new StorefrontModelWarehouse();

			foreach ($pIds as $pId => $qty)
			{
				$product_skus = $warehouse->getProductSkus($pId);

				// must be only one sku to work
				if (sizeof($product_skus) != 1)
				{
					continue;
				}

				$skus[$product_skus[0]] = $qty;

				// each pId must map to one SKU, otherwise ignored
			}
		}
		else
		{
			$skus = JRequest::getVar('skus', false, 'post');
		}
		//print_r($skus); die;

		// Initialize errors array
		$errors = array();

		if ($updateCartRequest && $skus)
		{
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
					$updateErrors[] = $e->getMessage();
				}
			}

			if (!empty($errors))
			{
				$redirect = false;
			}
			else
			{
				// set flag to redirect
				$redirect = true;
			}
		}

		// add coupon if needed
		$addCouponRequest = JRequest::getVar('addCouponCode', false, 'post');
		$couponCode = JRequest::getVar('couponCode', false, 'post');

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
				$errors[] = $e->getMessage();
			}

			if (!empty($errors))
			{
				$redirect = false;
			}
			else
			{
				// set flag to redirect
				$redirect = true;
			}
		}

		if (!empty($redirect) && $redirect)
		{
			// prevent resubmitting form by refresh
			// If not an ajax call, redirect to cart
			$redirect_url = JRoute::_('index.php?option=' . 'com_cart');
			$app = JFactory::getApplication();
			$app->redirect($redirect_url);
		}

		// Set errors
		$this->view->setError($errors);

		// Get the latest synced cart info, it will also enable cart syncing that was turned off before
		$cartInfo = $cart->getCartInfo(true);
		//print_r($cartInfo); die;
		$this->view->cartInfo = $cartInfo;

		// Handle coupons
		$couponPerks = $cart->getCouponPerks();
		//print_r($couponPerks); die;
		$this->view->couponPerks = $couponPerks;

		// Handle memberships
		$membershipInfo = $cart->getMembershipInfo();
		//print_r($membershipInfo); die;
		$this->view->membershipInfo = $membershipInfo;

		// Check if there are changes to display
		if ($cart->cartChanged())
		{
			$cartChanges = $cart->getCartChanges();
			$this->view->setError($cartChanges);
		}

		$this->view->display();
	}
}

