<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model for constrained eager loading tests
 */
class EagerPost extends Relational
{
    protected $table = 'eager_posts';

    /**
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function comments()
    {
        return $this->oneToMany(EagerComment::class);
    }
}
