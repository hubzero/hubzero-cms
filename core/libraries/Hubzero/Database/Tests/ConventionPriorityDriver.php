<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Drivers\Mock\MockDriver;

class ConventionPriorityDriver extends MockDriver
{
    public static function test()
    {
        return true;
    }
}
