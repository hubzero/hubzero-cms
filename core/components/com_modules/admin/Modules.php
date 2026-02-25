<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Modules\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Modules extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_modules')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        require_once __DIR__ . DS . 'controllers' . DS . 'modules.php';

        // initiate controller
        $controller = new Controllers\Modules();
        $controller->execute();
    }
}
