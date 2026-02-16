<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
