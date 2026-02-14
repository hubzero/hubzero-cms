<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for replicate tests
 */
class ReplicateTestItem extends Relational
{
    protected $table = 'replicate_test_items';
    protected $pk = 'id';
}
