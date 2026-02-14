<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

use Hubzero\Database\Relational;
use Hubzero\Database\Traits\MassPrunable;

/**
 * Model with MassPrunable trait for testing
 */
class MassPrunableSession extends Relational
{
    use MassPrunable;

    protected $table = 'prunable_sessions';
    protected $namespace = '';
    protected $dispatchesModelEvents = true;

    /**
     * Get the prunable query - expired sessions
     *
     * @return static
     */
    protected function prunable()
    {
        $now = date('Y-m-d H:i:s');
        return static::all()->where('expires_at', '<', $now);
    }
}
