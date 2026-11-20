<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Components\Saml\Models\ServiceProvider;

/**
 * Service Provider with the group lookup stubbed, so the access policy can
 * be exercised without a group table behind it.
 */
class StubbedServiceProvider extends ServiceProvider
{
    /**
     * cn => list of member user IDs
     *
     * @var  array<mixed>
     */
    public $groups = [];

    /**
     * @param   string   $cn
     * @param   integer  $userId
     * @return  boolean
     */
    protected function userIsInGroup($cn, $userId)
    {
        $this->lookups++;

        return isset($this->groups[$cn]) && in_array($userId, $this->groups[$cn]);
    }

    /**
     * Membership lookups made, to see they are not repeated
     *
     * @var  integer
     */
    public $lookups = 0;

    /**
     * Entry => the group's own cn; entries not listed are their own cn
     *
     * @var  array<mixed>
     */
    public $canonical = [];

    /**
     * @param   string  $cn
     * @return  string
     */
    protected function groupCn($cn)
    {
        return isset($this->canonical[$cn]) ? $this->canonical[$cn] : $cn;
    }

    /**
     * cn's this stub reports as confirmed membership
     *
     * @var  array<mixed>
     */
    public $confirmed = [];

    /**
     * @param   integer  $userId
     * @return  array<mixed>
     */
    protected function confirmedGroups($userId)
    {
        return $this->confirmed;
    }
}
