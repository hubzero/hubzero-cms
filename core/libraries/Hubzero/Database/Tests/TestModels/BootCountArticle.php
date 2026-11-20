<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

    /**
     * @var  int
     */
    public static $bootCount = 0;

    protected static function boot()
    {
        parent::boot();
        self::$bootCount++;
    }
}
