<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Tests\Doubles;

/**
 * Stand-in for the resource type model in TypeOptionsHelper tests
 *
 * Declares the all() the tests stub; see ControllerDouble for why a
 * declared type is needed.
 */
class TypeDouble
{
    /**
     * Stubbed by the tests to return the types
     *
     * @return  mixed
     */
    public function all()
    {
        return null;
    }
}
