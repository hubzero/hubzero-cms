<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
        require_once __DIR__ . DS . 'controllers' . DS . 'cpanel.php';

        // Instantiate controller
        $controller = new Controllers\Cpanel();
        $controller->execute();
    }
}
