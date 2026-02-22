<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Groups extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\User::authorise('core.manage', 'com_groups')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // build controller path
        $controllerName = \Request::getCmd('controller', 'manage');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'manage';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_GROUPS_MENU_GROUPS'),
            \Route::url('index.php?option=com_groups'),
            ($controllerName != 'imports' && $controllerName != 'importhooks' && $controllerName != 'customfields')
        );
        if (\User::authorise('core.admin', 'com_groups')) {
            \Submenu::addEntry(
                \Lang::txt('COM_GROUPS_MENU_IMPORT'),
                \Route::url('index.php?option=com_groups&controller=imports'),
                ($controllerName == 'imports' || $controllerName == 'importhooks')
            );
            \Submenu::addEntry(
                \Lang::txt('COM_GROUPS_MENU_CUSTOMFIELDS'),
                \Route::url('index.php?option=com_groups&controller=customfields'),
                ($controllerName == 'customfields')
            );
        }

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
