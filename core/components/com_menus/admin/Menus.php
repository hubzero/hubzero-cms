<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Request;

/**
 * Component entry point
 */
class Menus extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Access check.
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_menus')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Determine task
        $task = Request::getCmd('task');
        if (strpos($task, '.') !== false) {
            $splitTask = explode('.', $task);
            Request::setVar('task', $splitTask[1]);
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'menus'));
        if (!file_exists(__DIR__ . '/controllers/' . $controllerName . '.php')) {
            $controllerName = 'menus';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_MENUS_SUBMENU_MENUS'),
            \Hubzero\Facades\Route::url('index.php?option=com_menus&controller=menus', false),
            $controllerName == 'menus'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_MENUS_SUBMENU_ITEMS'),
            \Hubzero\Facades\Route::url('index.php?option=com_menus&controller=items', false),
            $controllerName == 'items'
        );

        require_once __DIR__ . '/controllers/' . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
