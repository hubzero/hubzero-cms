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
 */
class PolyVideo extends Relational
{
    protected $table = 'poly_videos';

    public function image()
    {
        return $this->morphOne(__NAMESPACE__ . '\PolyImage', 'imageable');
    }

    public function activities()
    {
        return $this->morphMany(__NAMESPACE__ . '\PolyActivityLog', 'loggable');
    }

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
