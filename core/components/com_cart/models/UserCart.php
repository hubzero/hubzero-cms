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