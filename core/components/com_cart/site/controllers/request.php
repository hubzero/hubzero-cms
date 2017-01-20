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
 * @author    Hubzero
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cart\Site\Controllers;

/**
 *	Cart AJAX requests
 */
class Request extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = Request::etVar('task', '');

		parent::execute();
	}

	public function addTask()
	{
		$response = new \stdClass();
		$response->status = 'ok';

		include_once (JPATH_COMPONENT . DS . 'models' . DS . 'cart.php');
		$cart = new CurrentCart();

		// update cart
		$updateCartRequest = Request::getVar('updateCart', false, 'post');
		$pIds = Request::getVar('pId', false, 'post');

		//print_r($pIds); die;

		// If pIds are posted, convert them to SKUs
		if (!empty($pIds))
		{
			$skus = array();

			$warehouse = new Warehouse();

			foreach ($pIds as $pId => $qty)
			{
				$product_skus = $warehouse->getProductSkus($pId);

				// must be only one sku to work
				if (sizeof($product_skus) != 1)
				{
					// each pId must map to one SKU, otherwise ignored
					continue;
				}

				$skus[$product_skus[0]] = $qty;
			}
		}
		else
		{
			$skus = Request::getVar('skus', false, 'post');
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
				catch (\Exception $e)
				{
					$errors[] = $e->getMessage();
				}
			}
		}

		// add coupon if needed
		$addCouponRequest = Request::getVar('addCouponCode', false, 'post');
		$couponCode = Request::getVar('couponCode', false, 'post');

		if ($addCouponRequest && $couponCode)
		{
			// Sync cart before pontial coupons applying
			$cart->getCartInfo(true);

			// Initialize errors array
			$errors = array();

			// Add coupon
			try
			{
				$cart->addCoupon($couponCode);
			}
			catch (\Exception $e)
			{
				$errors[] = $e->getMessage();
			}
		}

		if (!empty($errors))
		{
			$response->status = 'error';
			$response->errors = $errors;
		}

		echo htmlspecialchars(json_encode($response), ENT_NOQUOTES);
		die();
	}
}
