<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     */
    protected $deletedAtColumn = 'removed_at';
}
