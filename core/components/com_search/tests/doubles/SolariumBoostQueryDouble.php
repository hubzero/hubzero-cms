<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for SolariumBoostQuery, used both as a query and as its own factory
 *
 * one() is static on the real class and reached through MockProxy, which turns
 * the static call into an instance one. PHPUnit will not stub a static method,
 * so both methods are declared here in instance form.
 */
class SolariumBoostQueryDouble
{
    /**
     * Factory call, stubbed by the tests
     *
     * @param   array<mixed>  $args
     * @return  mixed
     */
    public function one($args = [])
    {
        return null;
    }

    /**
     * Query serialisation, stubbed by the tests
     *
     * @return  array<mixed>
     */
    public function toArray()
    {
        return [];
    }
}
