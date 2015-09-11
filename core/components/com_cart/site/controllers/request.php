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
 * @author    Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 *	Cart AJAX requests
 */
class CartControllerRequest extends ComponentController
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

		parent::execute();
	}

	public function addTask()
	{
		$response = new stdClass();
		$response->status = 'ok';

		include_once(JPATH_COMPONENT . DS . 'models' . DS . 'cart.php');
		$cart = new CartModelCurrentCart();

		// update cart
		$updateCartRequest = Request::getVar('updateCart', false, 'post');
		$pIds = Request::getVar('pId', false, 'post');

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
				catch (Exception $e)
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
			catch (Exception $e)
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

