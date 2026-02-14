<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Profile model (intermediate)
 */
class ThroughProfile extends Relational
{
    protected $table = 'through_profiles';
    protected $namespace = '';

    public function user()
    {
        return $this->belongsToOne(ThroughUser::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsToOne(ThroughCountry::class, 'country_id');
    }
}
