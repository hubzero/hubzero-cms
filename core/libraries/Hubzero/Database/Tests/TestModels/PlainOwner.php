<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for belongsToOne() key defaults: primary key id
 */
class PlainOwner extends Relational
{
    protected $table = 'plain_owners';
    protected $namespace = '';
}
