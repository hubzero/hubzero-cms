<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Simple test model for bulk insert tests
 */
class BulkTestUser extends Relational
{
    protected $table = 'bulk_test_users';
    protected $namespace = '';
}
