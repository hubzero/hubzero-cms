<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model with cascade save enabled against a failing related model
 */
class CascadePostWithFailingCascadeSave extends Relational
{
    protected $table = 'cascade_posts';
    protected $namespace = '';
    protected $cascadeRelationships = true;

    public function comments()
    {
        return $this->oneToMany(CascadeFailingCommentSave::class, 'post_id')
            ->cascadeOnSave();
    }
}
