<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

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
        if (!User::authorise('core.manage', 'com_installer')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        if ($task = Request::getCmd('task')) {
            if (strstr($task, '.')) {
                @list($c, $t) = explode('.', $task);
                $t = Request::setVar('task', trim($t));
                $c = Request::setVar('controller', trim($c));
            }
        }
        $controllerName = Request::getCmd('controller', 'manage');
        if (!is_subclass_of(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName), \Hubzero\Component\ControllerInterface::class)) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        \Components\Installer\Admin\Helpers\Installer::addSubmenu($controllerName);

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
