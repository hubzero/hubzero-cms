<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\SoftDeletes;

/**
 * Test model with soft deletes for lifecycle hook tests
 */
class HookTestItemWithSoftDeletes extends Relational
{
    use SoftDeletes;

    protected $table = 'hook_test_items';
    protected $namespace = '';
    protected $dispatchesModelEvents = true;
}
