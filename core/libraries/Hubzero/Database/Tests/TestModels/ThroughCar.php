<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
