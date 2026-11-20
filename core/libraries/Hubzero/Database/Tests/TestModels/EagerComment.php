<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

    /**
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function post()
    {
        return $this->belongsToOne(EagerPost::class);
    }

    /**
     * Scope: only approved comments
     *
     * @param   mixed  $query
     * @return  void
     */
    public function scopeApproved($query)
    {
        $query->whereEquals('approved', 1);
    }

    /**
     * Scope: comments for a specific post
     *
     * @param   mixed  $query
     * @param   mixed  $postId
     * @return  void
     */
    public function scopeByPost($query, $postId)
    {
        $query->whereEquals('post_id', $postId);
    }
}
