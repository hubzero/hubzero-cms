<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Image model - demonstrates morphTo (belongs to multiple parent types)
 *
 * @property-read  mixed  $imageable  What imageable() gives
 */
class PolyImage extends Relational
{
    protected $table = 'poly_images';

    /**
     * @return  \Hubzero\Database\Relationship\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo('imageable');
    }
}
