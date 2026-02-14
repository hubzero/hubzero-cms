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
 * Test model with custom column name
 */
class CustomHittableArticle extends Relational
{
    use Hittable;

    protected $table = 'hittable_custom_articles';
    protected $hitsColumn = 'view_count';
}
