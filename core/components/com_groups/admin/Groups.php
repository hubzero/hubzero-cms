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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_groups')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // build controller path
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'manage');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'manage';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_GROUPS_MENU_GROUPS'),
            \Hubzero\Facades\Route::url('index.php?option=com_groups'),
            ($controllerName != 'imports' && $controllerName != 'importhooks' && $controllerName != 'customfields')
        );
        if (\Hubzero\Facades\User::authorise('core.admin', 'com_groups')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_GROUPS_MENU_IMPORT'),
                \Hubzero\Facades\Route::url('index.php?option=com_groups&controller=imports'),
                ($controllerName == 'imports' || $controllerName == 'importhooks')
            );
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_GROUPS_MENU_CUSTOMFIELDS'),
                \Hubzero\Facades\Route::url('index.php?option=com_groups&controller=customfields'),
                ($controllerName == 'customfields')
            );
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
