<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Models\ProductTypes\Subscriptions;

class AccessGroupMembershipSubscription extends BaseSubscription
{
    /**
     * Constructor
     *
     * @param   integer  $pId
     * @param   integer  $uId
     * @return  void
     */
    public function __construct($pId, $uId)
    {
        parent::__construct($pId, $uId);
    }

    /**
     * Get expiration info.
     *
     * @return  void
     * @throws  \Exception
     */
    public function getAccessGroupExpiration()
    {
        // This will get expiration from the correct place
        throw new \Exception('not implemented');
    }
}
