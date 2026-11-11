<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for the Boost ORM where tests stub its all() call, and for a
 * boost whose field, value and strength they stub
 *
 * The real Boost inherits all() from Relational as a static method, and the
 * production code reaches it through MockProxy, which turns a static call into
 * an instance one. PHPUnit will not stub a static method, so declare the
 * instance-shaped method the tests actually need to control.
 */
class BoostDouble
{
    /**
     * Stubbed by the tests to return a set of boosts
     *
     * @return  array
     */
    public function all()
    {
        return [];
    }

    /**
     * Stubbed by the tests to return the boosted field
     *
     * @return  mixed
     */
    public function getField()
    {
        return null;
    }

    /**
     * Stubbed by the tests to return the boosted value
     *
     * @return  mixed
     */
    public function getFieldValue()
    {
        return null;
    }

    /**
     * Stubbed by the tests to return the boost strength
     *
     * @return  mixed
     */
    public function getStrength()
    {
        return null;
    }
}
