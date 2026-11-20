<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers\Model;

use Components\Cart\Lib\Handlers\ModelHandler;

class MembershipModelHandler extends ModelHandler
{
    /**
     * Constructor
     *
     * @param   array<string, mixed>   $item
     * @param   integer  $crtId
     * @param   integer  $tId
     * @return  void
     */
    public function __construct($item, $crtId, $tId)
    {
        parent::__construct($item, $crtId, $tId);
    }

    /** @return void */
    public function handle()
    {
        $itemInfo = $this->item['info'];

        // Get user
        $uId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

        // Get product type
        $warehouse = new \Components\Storefront\Models\Warehouse();
        $pType = $warehouse->getProductTypeInfo($itemInfo->ptId);
        $type = $pType['ptName'];

        $subscription = \Components\Storefront\Models\Memberships::getSubscriptionObject(
            $type,
            $itemInfo->pId,
            (int) $uId,
        );
        // Get the expiration for the current subscription (if any)
        $currentExpiration = $subscription->getExpiration();

        // Calculate new expiration
        $newExpires = \Components\Storefront\Models\Memberships::calculateNewExpiration(
            $currentExpiration,
            $this->item,
        );

        // Update/Create membership expiration date with new value
        $subscription->setExpiration($newExpires);
    }
}
