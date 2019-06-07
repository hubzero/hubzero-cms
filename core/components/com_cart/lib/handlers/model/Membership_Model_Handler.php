<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class Membership_Model_Handler extends Model_Handler
{
	/**
	 * Constructor
	 *
	 * @param 	void
	 * @return 	void
	 */
	public function __construct($item, $crtId, $tId)
	{
		parent::__construct($item, $crtId, $tId);
	}

	public function handle()
	{
		$itemInfo = $this->item['info'];

		// Get user
		require_once dirname(dirname(dirname(__DIR__))) . DS . 'models' . DS . 'Cart.php';
		$uId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

		// Get product type
		require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Warehouse.php';
		$warehouse = new \Components\Storefront\Models\Warehouse();
		$pType = $warehouse->getProductTypeInfo($itemInfo->ptId);
		$type = $pType['ptName'];

		require_once \Component::path('com_storefront') . DS . 'models' . DS . 'Memberships.php';
		$subscription = \Components\Storefront\Models\Memberships::getSubscriptionObject($type, $itemInfo->pId, $uId);
		// Get the expiration for the current subscription (if any)
		$currentExpiration = $subscription->getExpiration();

		// Calculate new expiration
		$newExpires = Components\Storefront\Models\Memberships::calculateNewExpiration($currentExpiration, $this->item);

		// Update/Create membership expiration date with new value
		$subscription->setExpiration($newExpires);
	}
}
