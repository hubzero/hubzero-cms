<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Htmx\HtmxService;

/**
 * Contract for components that opt into HTMX support at component boot time.
 */
interface HtmxComponentInterface
{
    /**
     * @return  bool
     */
    public function htmxEnabled(): bool;

    /**
     * @param   HtmxService  $htmx
     * @return  void
     */
    public function registerHtmx(HtmxService $htmx): void;
}
