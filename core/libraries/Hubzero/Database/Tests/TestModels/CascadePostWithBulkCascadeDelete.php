<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model with BULK cascade delete enabled (fast, no events)
 */
class CascadePostWithBulkCascadeDelete extends Relational
{
    protected $table = 'cascade_posts';
    protected $namespace = '';

    public function comments()
    {
        return $this->oneToMany(CascadeComment::class, 'post_id')
            ->cascadeOnDelete(true, true);  // bulk: true
    }
}
