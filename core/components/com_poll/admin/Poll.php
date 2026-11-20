<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

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
        if (!User::authorise('core.manage', 'com_poll')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Create the controller
        $controller = new Controllers\Polls();
        $controller->execute();
    }
}
