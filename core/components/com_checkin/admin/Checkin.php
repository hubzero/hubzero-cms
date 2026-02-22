<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Checkin\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Checkin extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\User::authorise('core.manage', 'com_checkin')) {
            \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        require_once __DIR__ . DS . 'controllers' . DS . 'checkin.php';

        // Instantiate controller
        $controller = new Controllers\Checkin();
        $controller->execute();
    }
}
