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
 * @package   Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once(PATH_CORE . DS . 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php');

/**
 * User shopping cart
 */
class CartModelUserCart extends CartModelCart
{
	/**
	 * Cart constructor
	 *
	 * @param int    Cart ID to locate the specific cart
	 * @return void
	 */
	public function __construct($crtId)
	{
		// Cart ID is always an integer
		if (empty($crtId) || !is_numeric($crtId) || $crtId < 1) {
			throw new Exception('Bad cart initialization');
		}

		parent::__construct();

		$this->crtId = $crtId;
		if (!$this->exists())
		{
			throw new Exception(COM_CART_NO_CART_FOUND);
		}
	}

	/**
	 * Get SKUs from the database
	 *
	 * @param void
	 * @return array of SKU IDs in the cart
	 */
	public function getCartItems()
	{
		$items = parent::getCartItems();
		// Return SKUs only
		return $items->skus;
	}
}