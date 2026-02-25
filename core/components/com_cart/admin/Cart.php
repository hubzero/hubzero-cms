<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Cart extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $option = 'com_cart';

        if (!\Hubzero\Facades\User::authorise('core.manage', $option)) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $scope = \Hubzero\Facades\Request::getCmd('scope', 'site');
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'downloads');

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_CART_SOFTWARE_DOWNLOADS'),
            \Hubzero\Facades\Route::url('index.php?option=com_cart&controller=downloads'),
            $controllerName == 'downloads'
        );

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_CART_ORDERS'),
            \Hubzero\Facades\Route::url('index.php?option=com_cart&controller=orders'),
            $controllerName == 'orders'
        );

        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'downloads';
        }
        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();

        $controller->execute();
        $controller->redirect();
    }
}
