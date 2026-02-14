<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\Orderable;

/**
 * Test model with Orderable trait
 */
class OrderableItem extends Relational
{
    use Orderable;

    protected $table = 'orderable_items';
}
