<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * Whether to say there is nothing to prune
     *
     * @var  bool
     */
    public static $nothingToPrune = false;

    /**
     * Get the prunable query - expired sessions
     *
     * @return static|null
     */
    protected function prunable()
    {
        if (static::$nothingToPrune) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        return static::all()->where('expires_at', '<', $now);
    }
}
