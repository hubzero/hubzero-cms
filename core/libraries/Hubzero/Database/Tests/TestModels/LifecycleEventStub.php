<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Event stub for SoftDeletes trait (requires Event facade)
 */
class LifecycleEventStub
{
    public static function trigger($event, $args = [])
    {
        return [];
    }
}
