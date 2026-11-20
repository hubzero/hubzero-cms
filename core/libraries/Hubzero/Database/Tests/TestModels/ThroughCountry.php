<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Country model
 *
 * @property mixed $id    Column of the test table
 * @property mixed $name  Column of the test table
 * @property mixed $code  Column of the test table
 */
class ThroughCountry extends Relational
{
    protected $table = 'through_countries';
    protected $namespace = '';
}
