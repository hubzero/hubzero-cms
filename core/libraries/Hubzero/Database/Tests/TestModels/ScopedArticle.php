<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Model with closure-based global scopes
 */
class ScopedArticle extends Relational
{
    protected $table = 'scoped_articles';
    protected $namespace = '';

    protected static function boot()
    {
        parent::boot();

        // Add active scope
        static::addGlobalScope('active', function ($query) {
            $query->whereEquals('active', 1);
        });

        // Add tenant scope (simulating multi-tenancy)
        static::addGlobalScope('tenant', function ($query) {
            $query->whereEquals('tenant_id', 1);
        });
    }
}
