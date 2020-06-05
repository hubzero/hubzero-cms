<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Models;

require_once 'Cart.php';

/**
 * User shopping cart
 */
class UserCart extends Cart
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
			throw new \Exception('Bad cart initialization');
		}

		parent::__construct();

		$this->crtId = $crtId;
		if (!$this->exists())
		{
			throw new \Exception(COM_CART_NO_CART_FOUND);
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
