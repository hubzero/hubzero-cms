<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Tag model - demonstrates morphedByMany (inverse of morphToMany)
 *
 * @property-read  mixed  $posts  What posts() gives
 * @property-read  mixed  $videos  What videos() gives
 */
class PolyTagModel extends Relational
{
    protected $table = 'poly_tags';

    /**
     * @return  \Hubzero\Database\Relationship\MorphToMany
     */
    public function posts()
    {
        return $this->morphedByMany(
            __NAMESPACE__ . '\PolyPostModel',
            'taggable',
            'poly_taggables',
            null,
            null,
            'tag_id',
        );
    }

    /**
     * @return  \Hubzero\Database\Relationship\MorphToMany
     */
    public function videos()
    {
        return $this->morphedByMany(
            __NAMESPACE__ . '\PolyVideo',
            'taggable',
            'poly_taggables',
            null,
            null,
            'tag_id',
        );
    }
}
