<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers\Custom;

use Components\Cart\Lib\Handlers\CustomHandler;

class ProMembershipCustomHandler extends CustomHandler
{
    /**
     * Constructor
     *
     * @param   array<string, mixed>   $item
     * @param   integer  $crtId
     * @return  void
     */
    public function __construct($item, $crtId)
    {
        parent::__construct($item, $crtId);
    }

    /** @return void */
    public function handle()
    {
        // Get user ID for the cart
        $userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

        // Get number of points to add
        if (!empty($this->item['meta']['addPoints']) && is_numeric($this->item['meta']['addPoints'])) {
            // Update points account
            $BTL = new \Hubzero\Bank\Teller((int) $userId);
            $points = (int) $this->item['meta']['addPoints'];
            $BTL->deposit($points, 'PRO Membership Bonus', 'PRO', $this->item['info']->sId);
        }
    }
}
