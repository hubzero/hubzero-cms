<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
