<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Services extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_services')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Include scripts

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'services');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'services';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SERVICES_SERVICES'),
            \Hubzero\Facades\Route::url('index.php?option=com_services&controller=services'),
            $controllerName == 'services'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SERVICES_SUBSCRIPTIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_services&controller=subscriptions'),
            $controllerName == 'subscriptions'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
