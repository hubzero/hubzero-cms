<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
