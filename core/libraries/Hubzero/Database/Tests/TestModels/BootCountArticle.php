<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model that tracks boot count
 */
class BootCountArticle extends Relational
{
    protected $table = 'scoped_articles';
    protected $namespace = '';

    public static $bootCount = 0;

    protected static function boot()
    {
        parent::boot();
        self::$bootCount++;
    }
}
