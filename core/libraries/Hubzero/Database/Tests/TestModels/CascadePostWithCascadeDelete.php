<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model with cascade delete enabled
 */
class CascadePostWithCascadeDelete extends Relational
{
    protected $table = 'cascade_posts';
    protected $namespace = '';
    protected $cascadeRelationships = true;

    /**
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function comments()
    {
        return $this
            ->oneToMany(CascadeComment::class, 'post_id')
            ->cascadeOnDelete();
    }
}
