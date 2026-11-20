<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cpanel\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Cpanel extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // No access check.

        // Instantiate controller
        $controller = new Controllers\Cpanel();
        $controller->execute();
    }
}
