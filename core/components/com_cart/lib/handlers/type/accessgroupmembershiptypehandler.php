<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers\Type;

use Components\Cart\Lib\Handlers\TypeHandler;

class AccessGroupMembershipTypeHandler extends TypeHandler
{
    /**
     * Constructor
     *
     * @param   object   $item
     * @param   integer  $crtId
     * @return  void
     */
    public function __construct($item, $crtId)
    {
        parent::__construct($item, $crtId);
    }

    /**
     * Handle
     *
     * @return  void
     */
    public function handle()
    {
        $ms = new \Components\Storefront\Models\Memberships();

        // Get current registration
        $membership = $ms->getMembershipInfo($this->crtId, $this->item['info']->pId);
        $expiration = $membership['crtmExpires'];

        /* Add the user to the corresponding user access group (pull access group ID from the meta) */
        try {
            // Get user ID for the cart
            $userId = \Components\Cart\Models\Cart::getCartUser($this->crtId);

            // Get the user group ID to set the user to (from meta)
            $userGId = \Components\Storefront\Models\Product::getMetaValue($this->item['info']->pId, 'userGroupId');

            if (!\Hubzero\Access\Map::addUserToGroup($userId, $userGId)) {
                $errorMsg = 'Failed to add user to group. Cart #' . $this->crtId;
                mail(\Hubzero\Facades\Config::get('mailfrom'), 'Error adding to the group', $errorMsg);
            }

            $table = \Hubzero\Facades\User::getInstance($userId);

            // Trigger the onAfterStoreUser event
            \Hubzero\Facades\Event::trigger('user.onUserAfterSave', array($table->toArray(), false, true, null));
        } catch (\Exception $e) {
            // Error
            return false;
        }
    }
}
