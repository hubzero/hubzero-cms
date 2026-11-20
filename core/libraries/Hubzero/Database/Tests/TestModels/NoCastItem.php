<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model without casts (for comparison)
 */
class NoCastItem extends Relational
{
    protected $table = 'cast_test_items';
    protected $namespace = '';
}
