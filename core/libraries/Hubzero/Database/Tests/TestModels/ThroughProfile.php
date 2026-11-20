<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

    /**
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function user()
    {
        return $this->belongsToOne(ThroughUser::class, 'user_id');
    }

    /**
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function country()
    {
        return $this->belongsToOne(ThroughCountry::class, 'country_id');
    }
}
