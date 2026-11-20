<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * A scope object that filters by published status
 */
class PublishedScope
{
    /**
     * @param   mixed  $query
     * @param   mixed  $model
     * @return  void
     */
    public function apply($query, $model)
    {
        $query->whereEquals('status', 'published');
    }
}
