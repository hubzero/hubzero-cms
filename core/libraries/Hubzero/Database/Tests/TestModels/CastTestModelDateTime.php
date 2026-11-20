<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Casts\AsDateTime;

/**
 * Test model with DateTime cast
 */
class CastTestModelDateTime extends Relational
{
    protected $table = 'cast_test';
    protected $casts = ['expires' => AsDateTime::class];
}
