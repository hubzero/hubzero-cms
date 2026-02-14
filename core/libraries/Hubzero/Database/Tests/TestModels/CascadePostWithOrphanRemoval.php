<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model with orphan removal enabled
 */
class CascadePostWithOrphanRemoval extends Relational
{
    protected $table = 'cascade_posts';
    protected $namespace = '';

    public function comments()
    {
        return $this->oneToMany(CascadeComment::class, 'post_id')
            ->orphanRemoval();
    }
}
