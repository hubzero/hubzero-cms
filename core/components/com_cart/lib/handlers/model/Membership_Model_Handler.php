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
 * @package   Hubzero
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		require_once(JPATH_BASE . DS . 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php');
		$uId = CartModelCart::getCartUser($this->crtId);

		// Get product type
		require_once(PATH_CORE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');
		$warehouse = new StorefrontModelWarehouse();
		$pType = $warehouse->getProductTypeInfo($itemInfo->ptId);
		$type = $pType['ptName'];

		require_once(JPATH_BASE . DS . 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Memberships.php');
		$subscription = StorefrontModelMemberships::getSubscriptionObject($type, $itemInfo->pId, $uId);
		// Get the expiration for the current subscription (if any)
		$currentExpiration = $subscription->getExpiration();

		// Calculate new expiration
		$newExpires = StorefrontModelMemberships::calculateNewExpiration($currentExpiration, $this->item);

		// Update/Create membership expiration date with new value
		$subscription->setExpiration($newExpires);
	}
}