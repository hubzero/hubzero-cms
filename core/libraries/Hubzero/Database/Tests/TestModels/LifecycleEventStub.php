<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Event stub for SoftDeletes trait (requires Event facade)
 */
class LifecycleEventStub
{
    /**
     * @param   mixed  $event
     * @param   array<mixed>  $args
     * @return  array<mixed>
     */
    public static function trigger($event, $args = [])
    {
        return [];
    }
}
