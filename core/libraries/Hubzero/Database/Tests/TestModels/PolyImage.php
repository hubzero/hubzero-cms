<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Image model - demonstrates morphTo (belongs to multiple parent types)
 */
class PolyImage extends Relational
{
    protected $table = 'poly_images';

    public function imageable()
    {
        return $this->morphTo('imageable');
    }
}
