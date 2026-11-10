<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Tests;

use Components\Storefront\Models\Memberships;
use Components\Storefront\Models\ProductTypes\Subscriptions\BaseSubscription;
use PHPUnit\Framework\TestCase;

/**
 * A membership product's type, as its product type records it, picks the
 * class that tracks the subscription. Access group memberships have a class
 * of their own; every other type falls back to the base subscription.
 */
class SubscriptionObjectTest extends TestCase
{
    /**
     * @return  void
     */
    public function testAccessGroupMembershipHasItsOwnClass(): void
    {
        foreach (['Access Group Membership', 'access group membership'] as $type) {
            $subscription = Memberships::getSubscriptionObject($type, 1, 2);

            $this->assertInstanceOf(BaseSubscription::class, $subscription, $type);
            $this->assertNotSame(BaseSubscription::class, get_class($subscription), $type);
        }
    }

    /**
     * @return  void
     */
    public function testOtherTypesUseTheBaseSubscription(): void
    {
        foreach (['Membership', 'Course'] as $type) {
            $this->assertSame(BaseSubscription::class, get_class(Memberships::getSubscriptionObject($type, 1, 2)), $type);
        }
    }
}
