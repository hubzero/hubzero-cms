<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
