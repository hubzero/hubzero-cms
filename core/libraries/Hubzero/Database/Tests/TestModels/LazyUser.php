<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test User model for lazy eager loading tests
 */
class LazyUser extends Relational
{
    protected $table = 'lazy_users';
    protected $namespace = '';

    /**
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function posts()
    {
        return $this->oneToMany(LazyPost::class, 'user_id');
    }

    /**
     * @return  \Hubzero\Database\Relationship\OneToOne
     */
    public function profile()
    {
        return $this->oneToOne(LazyProfile::class, 'user_id');
    }
}
