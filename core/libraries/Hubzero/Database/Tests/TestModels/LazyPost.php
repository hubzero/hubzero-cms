<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Post model for lazy eager loading tests
 */
class LazyPost extends Relational
{
    protected $table = 'lazy_posts';
    protected $namespace = '';

    public function user()
    {
        return $this->belongsToOne(LazyUser::class, 'user_id');
    }

    public function comments()
    {
        return $this->oneToMany(LazyComment::class, 'post_id');
    }
}
