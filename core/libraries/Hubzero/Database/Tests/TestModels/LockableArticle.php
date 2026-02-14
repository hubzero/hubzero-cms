<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\Lockable;

/**
 * Test model with Lockable trait
 */
class LockableArticle extends Relational
{
    use Lockable;

    protected $table = 'lockable_articles';
}
