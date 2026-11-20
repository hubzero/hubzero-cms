<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model with object-based global scope
 */
class ArticleWithObjectScope extends Relational
{
    protected $table = 'scoped_articles';
    protected $namespace = '';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new PublishedScope());
    }
}
