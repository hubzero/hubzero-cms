<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model for testing onCondition
 */
class OnCondPost extends Relational
{
    protected $table = 'oncond_posts';

    /**
     * All comments - no onCondition (BC test)
     */
    public function allComments()
    {
        return $this->oneToMany(OnCondComment::class, 'post_id');
    }

    /**
     * Active comments only - simple onCondition
     */
    public function activeComments()
    {
        return $this->oneToMany(OnCondComment::class, 'post_id')
            ->onCondition('status', 1);
    }

    /**
     * Active featured comments - multiple onConditions
     */
    public function activeFeatured()
    {
        return $this->oneToMany(OnCondComment::class, 'post_id')
            ->onCondition('status', 1)
            ->onCondition('type', 'featured');
    }

    /**
     * Complex conditions with closure
     */
    public function complexComments()
    {
        return $this->oneToMany(OnCondComment::class, 'post_id')
            ->onCondition(function ($query) {
                $query->whereEquals('status', 1)
                      ->whereEquals('type', 'general');
            });
    }
}
