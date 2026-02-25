<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Poll extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Authorization check
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_poll')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Create the controller
        $controller = new Controllers\Polls();
        $controller->execute();
    }
}
