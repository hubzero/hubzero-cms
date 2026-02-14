<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\Hittable;

/**
 * Test model with Hittable trait
 */
class HittableArticle extends Relational
{
    use Hittable;

    protected $table = 'hittable_articles';
}
