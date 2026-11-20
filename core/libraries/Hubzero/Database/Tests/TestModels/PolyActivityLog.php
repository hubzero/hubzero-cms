<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

    /**
     * @return  \Hubzero\Database\Relationship\MorphTo
     */
    public function loggable()
    {
        return $this->morphTo('loggable');
    }
}
