<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

    /**
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function post()
    {
        return $this->belongsToOne(LazyPost::class, 'post_id');
    }

    /**
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function author()
    {
        return $this->belongsToOne(LazyUser::class, 'user_id');
    }
}
