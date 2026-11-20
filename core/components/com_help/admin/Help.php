<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Help\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Help extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {

        // Instantiate controller and execute
        $controller = new Controllers\Help();
        $controller->execute();
        $controller->redirect();
    }
}
