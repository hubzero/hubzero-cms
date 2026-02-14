<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

    public function comments()
    {
        return $this->oneToMany(EagerComment::class);
    }
}
