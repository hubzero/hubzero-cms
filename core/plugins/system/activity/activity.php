<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin for activity
 */

namespace Plugins\System\Activity;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;

class Activity extends Plugin
{
    /**
     * Log an activity
     *
     * @param   array<mixed>  $activity
     * @param   array<mixed>  $recipients
     * @return  void
     */
    public function logActivity($activity, $recipients = [])
    {
        if (!App::isSite() && !App::isApi()) {
            return;
        }

        if (!$activity) {
            return;
        }

        \Hubzero\Activity\Log::log($activity, $recipients);
    }
}
