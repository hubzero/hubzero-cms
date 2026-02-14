<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\Prunable;

/**
 * Model with Prunable trait for testing
 */
class PrunableLog extends Relational
{
    use Prunable;

    protected $table = 'prunable_logs';
    protected $namespace = '';

    /**
     * Get the prunable query - logs older than 30 days
     *
     * @return static
     */
    protected function prunable()
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        return static::all()->where('created', '<', $thirtyDaysAgo);
    }
}
