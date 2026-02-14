<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test User model
 */
class ThroughUser extends Relational
{
    protected $table = 'through_users';
    protected $namespace = '';

    /**
     * Get the user's country through their profile
     *
     * @return \Hubzero\Database\Relationship\OneToOneThrough
     */
    public function country()
    {
        return $this->oneToOneThrough(
            ThroughCountry::class,
            ThroughProfile::class,
            'country_id',
            'user_id'
        );
    }
}
