<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model with no cascade (explicit for test clarity)
 */
class CascadePostNoCascade extends Relational
{
    protected $table = 'cascade_posts';
    protected $namespace = '';

    /**
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function comments()
    {
        return $this->oneToMany(CascadeComment::class, 'post_id');
    }
}
