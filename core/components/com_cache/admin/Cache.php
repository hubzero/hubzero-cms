<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

/**
 * Component entry point
 */
class Cache extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!User::authorise('core.manage', 'com_cache')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Instantiate controller
        $controller = new Controllers\Cleanser();
        $controller->execute();
    }
}
