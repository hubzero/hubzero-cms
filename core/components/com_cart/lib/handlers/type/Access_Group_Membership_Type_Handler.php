<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps
class Access_Group_Membership_Type_Handler extends Type_Handler
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

        /* NEW
        $subscription = StorefrontModelMemberships::getSubscriptionObject($this->type, $this->pId, $this->uId);
        // Get the expiration for the current subscription (if any)
        $currentExpiration = $subscription->getExpiration();
        */

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
                mail(Config::get('mailfrom'), 'Error adding to the group', $errorMsg);
            }

            $table = User::getInstance($userId);

            // Trigger the onAftereStoreUser event
            Event::trigger('user.onUserAfterSave', array($table->toArray(), false, true, null));
        } catch (Exception $e) {
            // Error
            return false;
        }
    }
}
