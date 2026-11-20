<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Casts\AsCollection;

/**
 * Test model with Collection cast
 */
class CastTestModelCollection extends Relational
{
    protected $table = 'cast_test';
    protected $casts = ['tags' => AsCollection::class];
}
