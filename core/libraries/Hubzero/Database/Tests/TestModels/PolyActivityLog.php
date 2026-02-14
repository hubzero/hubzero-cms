<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;

/**
 * ActivityLog model - demonstrates morphTo via morphMany
 */
class PolyActivityLog extends Relational
{
    protected $table = 'poly_activity_logs';

    public function loggable()
    {
        return $this->morphTo('loggable');
    }
}
