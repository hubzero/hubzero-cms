<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model without SoftDeletes trait (for comparison)
 */
class NonSoftDeletePost extends Relational
{
    protected $table = 'soft_delete_posts';
    protected $namespace = '';
}
