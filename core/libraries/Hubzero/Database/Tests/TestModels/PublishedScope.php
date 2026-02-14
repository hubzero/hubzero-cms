<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * A scope object that filters by published status
 */
class PublishedScope
{
    public function apply($query, $model)
    {
        $query->whereEquals('status', 'published');
    }
}
