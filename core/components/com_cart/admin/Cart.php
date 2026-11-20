<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin;

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

        if (!User::authorise('core.manage', $option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $scope = Request::getCmd('scope', 'site');
        $controllerName = Request::getCmd('controller', 'downloads');

        Submenu::addEntry(
            Lang::txt('COM_CART_SOFTWARE_DOWNLOADS'),
            Route::url('index.php?option=com_cart&controller=downloads'),
            $controllerName == 'downloads',
        );

        Submenu::addEntry(
            Lang::txt('COM_CART_ORDERS'),
            Route::url('index.php?option=com_cart&controller=orders'),
            $controllerName == 'orders',
        );

        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'downloads';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        /** @var \Hubzero\Component\AdminController $controller Every controller here extends one */
        $controller = new $controllerName();

        $controller->execute();
        $controller->redirect();
    }
}
