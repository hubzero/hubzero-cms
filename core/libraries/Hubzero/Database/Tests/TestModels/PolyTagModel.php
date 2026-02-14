<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Tag model - demonstrates morphedByMany (inverse of morphToMany)
 */
class PolyTagModel extends Relational
{
    protected $table = 'poly_tags';

    public function posts()
    {
        return $this->morphedByMany(
            __NAMESPACE__ . '\PolyPostModel',
            'taggable',
            'poly_taggables',
            null,
            null,
            'tag_id'
        );
    }

    public function videos()
    {
        return $this->morphedByMany(
            __NAMESPACE__ . '\PolyVideo',
            'taggable',
            'poly_taggables',
            null,
            null,
            'tag_id'
        );
    }
}
