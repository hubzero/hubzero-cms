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
 * @package   Hubzero
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

class Membership_Model_Handler extends Model_Handler
{
	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($item, $crtId)
	{
		parent::__construct($item, $crtId);
	}

	public function handle()
	{
		$itemInfo = $this->item['info'];

		// Get user
		require_once(dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php');
		$uId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

		// Get product type
		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php';
		$warehouse = new \Components\Storefront\Models\Warehouse();
		$pType = $warehouse->getProductTypeInfo($itemInfo->ptId);
		$type = $pType['ptName'];

		require_once PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php';
		$subscription = \Components\Storefront\Models\Memberships::getSubscriptionObject($type, $itemInfo->pId, $uId);
		// Get the expiration for the current subscription (if any)
		$currentExpiration = $subscription->getExpiration();

		// Calculate new expiration
		$newExpires = Components\Storefront\Models\Memberships::calculateNewExpiration($currentExpiration, $this->item);

		// Update/Create membership expiration date with new value
		$subscription->setExpiration($newExpires);
	}
}