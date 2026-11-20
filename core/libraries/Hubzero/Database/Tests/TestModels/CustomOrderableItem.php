<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
    /**
     * @var  string
     */
    protected $orderingColumn = 'sort_order';
}
