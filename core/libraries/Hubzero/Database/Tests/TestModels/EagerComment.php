<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Comment model for constrained eager loading tests
 */
class EagerComment extends Relational
{
    protected $table = 'eager_comments';

    public function post()
    {
        return $this->belongsToOne(EagerPost::class);
    }

    /**
     * Scope: only approved comments
     */
    public function scopeApproved($query)
    {
        $query->whereEquals('approved', 1);
    }

    /**
     * Scope: comments for a specific post
     */
    public function scopeByPost($query, $postId)
    {
        $query->whereEquals('post_id', $postId);
    }
}
