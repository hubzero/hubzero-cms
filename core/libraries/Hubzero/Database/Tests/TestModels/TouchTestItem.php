<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     * @var array
     */
    public static $columns = [];
}
