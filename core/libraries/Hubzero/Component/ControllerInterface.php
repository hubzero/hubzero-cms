<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

/**
 * Controller interface for components.
 */
interface ControllerInterface
{
    /**
     * Determines task being called and attempts to execute it
     *
     * @return  void
     */
    public function execute();
}
