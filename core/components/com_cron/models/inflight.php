<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Models;

/**
 * The job a cron runner has in flight, for Job::releaseOnShutdown() to
 * release if the process ends before the runner can
 */
class InFlight
{
    /**
     * Set by the runner while a job runs
     *
     * @var  Job|null
     */
    public $job = null;
}
