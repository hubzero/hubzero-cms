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
 * Test model with group-scoped ordering
 */
class OrderableMenuItem extends Relational
{
    use Orderable;

    protected $table = 'orderable_menu_items';
    /**
     * @var  array<int, mixed>
     */
    protected $orderingGroup = ['menu_id'];
}
