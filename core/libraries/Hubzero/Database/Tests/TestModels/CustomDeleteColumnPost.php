<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\SoftDeletes;

/**
 * Model with SoftDeletes trait using custom column name
 */
class CustomDeleteColumnPost extends Relational
{
    use SoftDeletes;

    protected $table = 'soft_delete_custom_posts';
    protected $namespace = '';

    /**
     * Custom column name for soft deletes
     *
     * @var  string
     */
    protected $deletedAtColumn = 'removed_at';
}
