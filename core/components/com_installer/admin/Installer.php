<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Installer extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_installer')) {
            \Hubzero\Facades\App::abort(403, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        if ($task = \Hubzero\Facades\Request::getCmd('task')) {
            if (strstr($task, '.')) {
                @list($c, $t) = explode('.', $task);
                $t = \Hubzero\Facades\Request::setVar('task', trim($t));
                $c = \Hubzero\Facades\Request::setVar('controller', trim($c));
            }
        }
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'manage');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        \Components\Installer\Admin\Helpers\Installer::addSubmenu($controllerName);

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
