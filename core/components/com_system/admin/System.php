<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class System extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_system')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'info'));
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'info';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SYSTEM_LDAP'),
            \Hubzero\Facades\Route::url('index.php?option=com_system&controller=ldap'),
            $controllerName == 'ldap'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SYSTEM_GEO'),
            \Hubzero\Facades\Route::url('index.php?option=com_system&controller=geodb'),
            $controllerName == 'geodb'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SYSTEM_CACHE'),
            \Hubzero\Facades\Route::url('index.php?option=com_system&controller=cache'),
            $controllerName == 'cache'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
