<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for chunking tests
 */
class TestItem extends Relational
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'test_items';

    /**
     * Namespace
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * Primary key
     *
     * @var string
     */
    protected $pk = 'id';
}
