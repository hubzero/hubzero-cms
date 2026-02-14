<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Comment model for lazy eager loading tests
 */
class LazyComment extends Relational
{
    protected $table = 'lazy_comments';
    protected $namespace = '';

    public function post()
    {
        return $this->belongsToOne(LazyPost::class, 'post_id');
    }

    public function author()
    {
        return $this->belongsToOne(LazyUser::class, 'user_id');
    }
}
