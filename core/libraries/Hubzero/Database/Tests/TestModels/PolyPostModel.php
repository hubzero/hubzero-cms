<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
