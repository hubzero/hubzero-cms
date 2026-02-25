<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps
class Pro_Membership_Custom_Handler extends Custom_Handler
{
    /**
     * Constructor
     *
     * @param   void
     * @return  void
     */
    public function __construct($item, $crtId)
    {
        parent::__construct($item, $crtId);
    }

    public function handle()
    {
        // Get user ID for the cart

        $userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

        // Get number of points to add
        if (!empty($this->item['meta']['addPoints']) && is_numeric($this->item['meta']['addPoints'])) {
            // Update points account
            $BTL = new \Hubzero\Bank\Teller($userId);
            $BTL->deposit($this->item['meta']['addPoints'], 'PRO Membership Bonus', 'PRO', $this->item['info']->sId);
        }
    }
}
