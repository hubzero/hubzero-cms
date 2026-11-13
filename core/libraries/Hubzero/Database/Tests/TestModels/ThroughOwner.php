<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Owner model
 *
 * @property mixed $name  Column of the test table
 */
class ThroughOwner extends Relational
{
    protected $table = 'through_owners';
    protected $namespace = '';
}
