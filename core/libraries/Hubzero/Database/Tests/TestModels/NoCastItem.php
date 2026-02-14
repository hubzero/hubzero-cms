<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model without casts (for comparison)
 */
class NoCastItem extends Relational
{
    protected $table = 'cast_test_items';
    protected $namespace = '';
}
