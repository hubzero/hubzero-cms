<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
