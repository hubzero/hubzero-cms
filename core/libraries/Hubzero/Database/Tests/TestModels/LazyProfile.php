<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Profile model for lazy eager loading tests
 */
class LazyProfile extends Relational
{
    protected $table = 'lazy_profiles';
    protected $namespace = '';

    public function user()
    {
        return $this->belongsToOne(LazyUser::class, 'user_id');
    }
}
