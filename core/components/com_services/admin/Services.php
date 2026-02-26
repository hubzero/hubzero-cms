<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Submenu;
use Hubzero\Facades\User;

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
        if (!User::authorise('core.manage', 'com_services')) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        // Include scripts

        $controllerName = Request::getCmd('controller', 'services');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'services';
        }

        Submenu::addEntry(
            Lang::txt('COM_SERVICES_SERVICES'),
            Route::url('index.php?option=com_services&controller=services'),
            $controllerName == 'services'
        );
        Submenu::addEntry(
            Lang::txt('COM_SERVICES_SUBSCRIPTIONS'),
            Route::url('index.php?option=com_services&controller=subscriptions'),
            $controllerName == 'subscriptions'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
