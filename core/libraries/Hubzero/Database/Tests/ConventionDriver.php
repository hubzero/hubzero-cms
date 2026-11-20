<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

use Hubzero\Database\Drivers\Mock\MockDriver;

class ConventionDriver extends MockDriver
{
    public static function test()
    {
        return true;
    }
}
