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
 * Model with Prunable and complex conditions
 */
class PrunableOrder extends Relational
{
    use Prunable;

    protected $table = 'prunable_orders';
    protected $namespace = '';

    /**
     * Get the prunable query - completed orders older than 30 days
     *
     * @return static
     */
    protected function prunable()
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        return static::all()
            ->where('created', '<', $thirtyDaysAgo)
            ->whereEquals('status', 'completed');
    }

    /**
     * Cleanup before pruning
     */
    protected function pruningCleanup()
    {
        // In a real scenario, this might delete associated files, etc.
    }
}
