<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Post model for polymorphic tests
 */
class PolyPostModel extends Relational
{
    protected $table = 'poly_posts';

    public function tags()
    {
        return $this->morphToMany(
            __NAMESPACE__ . '\PolyTagModel',
            'taggable',
            'poly_taggables',
            null,
            null,
            'tag_id'
        );
    }
}
