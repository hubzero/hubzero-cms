<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

    /**
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function user()
    {
        return $this->belongsToOne(LazyUser::class, 'user_id');
    }
}
