<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Car model (intermediate)
 */
class ThroughCar extends Relational
{
    protected $table = 'through_cars';
    protected $namespace = '';
}
