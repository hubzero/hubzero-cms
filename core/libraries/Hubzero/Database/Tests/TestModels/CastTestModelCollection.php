<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
