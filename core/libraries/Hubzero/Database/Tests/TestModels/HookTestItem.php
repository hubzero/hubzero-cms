<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Simple test model for lifecycle hook tests
 */
class HookTestItem extends Relational
{
    protected $table = 'hook_test_items';
    protected $namespace = '';
    protected $dispatchesModelEvents = true;
}
