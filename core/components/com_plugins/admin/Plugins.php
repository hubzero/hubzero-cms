<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Plugins\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Plugins extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_plugins')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $task = \Hubzero\Facades\Request::getCmd('task');
        if (strstr($task, '.')) {
            \Hubzero\Facades\Request::setVar('controller', strstr($task, '.', true));
            \Hubzero\Facades\Request::setVar('task', strstr($task, '.'));
        }
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'plugins'));
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }


        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
