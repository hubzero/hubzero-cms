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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_checkin')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Instantiate controller
        $controller = new Controllers\Checkin();
        $controller->execute();
    }
}
