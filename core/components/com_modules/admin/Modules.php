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
        if (!\User::authorise('core.manage', 'com_modules')) {
            \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        require_once dirname(__DIR__) . DS . 'helpers' . DS . 'modules.php';
        require_once dirname(__DIR__) . DS . 'models' . DS . 'module.php';
        require_once __DIR__ . DS . 'controllers' . DS . 'modules.php';

        // initiate controller
        $controller = new Controllers\Modules();
        $controller->execute();
    }
}
