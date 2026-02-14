<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * Test Mechanic model (uses hasOneThrough alias)
 */
class ThroughMechanic extends Relational
{
    protected $table = 'through_mechanics';
    protected $namespace = '';

    /**
     * Get the car's owner through the car
     * Using the hasOneThrough alias for Laravel compatibility
     *
     * @return \Hubzero\Database\Relationship\OneToOneThrough
     */
    public function carOwner()
    {
        return $this->hasOneThrough(
            ThroughOwner::class,
            ThroughCar::class,
            'owner_id',
            'mechanic_id'
        );
    }
}
