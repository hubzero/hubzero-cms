<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Referrer Policy Header Plugin
 */

namespace Plugins\System\Referrerpolicy;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;

class Referrerpolicy extends Plugin
{
    /**
     * Hook for after app initialization
     *
     * @return  void
     */
    public function onAfterInitialise()
    {
        // Get the policy
        $policy = $this->params->get('policy', 'same-origin');
        $policy = trim($policy);

        // Bail out on empty policy
        if (empty($policy)) {
            return;
        }

        // Add to the headers
        App::get('response')->headers->set('Referrer-Policy', $policy, true);
    }
}
