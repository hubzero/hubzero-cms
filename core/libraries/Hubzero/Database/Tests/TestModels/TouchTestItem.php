<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Simple test model for touch method tests
 */
class TouchTestItem extends Relational
{
    protected $table = 'touch_test_items';
    protected $namespace = '';

    /**
     * Public column cache for testing
     * @var array<mixed>
     */
    public static $columns = [];
}
