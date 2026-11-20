<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Plugins\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

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
        if (!User::authorise('core.manage', 'com_plugins')) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $task = Request::getCmd('task');
        if (strstr($task, '.')) {
            Request::setVar('controller', strstr($task, '.', true));
            Request::setVar('task', strstr($task, '.'));
        }
        $controllerName = Request::getCmd('controller', Request::getCmd('view', 'plugins'));
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }


        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
