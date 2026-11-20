<?php

/**
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test model for belongsToOne() key defaults: primary key owner_key
 */
class KeyedOwner extends Relational
{
    protected $table = 'key_owners';
    protected $pk = 'owner_key';
    protected $namespace = '';
}
