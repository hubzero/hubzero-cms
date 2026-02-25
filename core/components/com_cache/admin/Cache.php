<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cache\Admin;

use Hubzero\Component\AbstractComponent;

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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_cache')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        require_once __DIR__ . DS . 'controllers' . DS . 'cleanser.php';

        // Instantiate controller
        $controller = new Controllers\Cleanser();
        $controller->execute();
    }
}
