<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for the current user in boost tests
 *
 * The User facade reaches get() through __callStatic, which PHPUnit cannot
 * stub, so the tests mock these declared methods instead.
 */
class UserDouble
{
    /**
     * Stubbed by the tests to return user attributes
     *
     * @param   mixed  $key
     * @return  mixed
     */
    public function get($key)
    {
        return null;
    }

    /**
     * Stubbed by the tests that use this double as a factory
     *
     * @param   mixed  $args
     * @return  mixed
     */
    public function one($args)
    {
        return null;
    }
}
