<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
