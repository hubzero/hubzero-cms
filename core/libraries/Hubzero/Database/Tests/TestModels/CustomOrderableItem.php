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
 * Test model with custom column name
 */
class CustomOrderableItem extends Relational
{
    use Orderable;

    protected $table = 'orderable_custom_items';
    protected $orderingColumn = 'sort_order';
}
