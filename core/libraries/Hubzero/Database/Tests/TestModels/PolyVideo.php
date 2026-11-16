<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Video model - demonstrates morphOne, morphMany, and morphToMany from parent side
 *
 * @property-read  mixed  $activities  What activities() gives
 * @property-read  mixed  $image  What image() gives
 * @property-read  mixed  $tags  What tags() gives
 */
class PolyVideo extends Relational
{
    protected $table = 'poly_videos';

    /**
     * @return  \Hubzero\Database\Relationship\MorphOne
     */
    public function image()
    {
        return $this->morphOne(__NAMESPACE__ . '\PolyImage', 'imageable');
    }

    /**
     * @return  \Hubzero\Database\Relationship\MorphMany
     */
    public function activities()
    {
        return $this->morphMany(__NAMESPACE__ . '\PolyActivityLog', 'loggable');
    }

    /**
     * @return  \Hubzero\Database\Relationship\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(
            __NAMESPACE__ . '\PolyTagModel',
            'taggable',
            'poly_taggables',
            null,
            null,
            'tag_id',
        );
    }
}
