<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\Lang;

/**
 * Component entry point
 */
class Storefront extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $option = 'com_storefront';

        if (!\Hubzero\Facades\User::authorise('core.manage', $option)) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $scope = \Hubzero\Facades\Request::getCmd('scope', 'site');
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'products');

        \Hubzero\Facades\Submenu::addEntry(
            Lang::txt('COM_STOREFRONT_PRODUCTS'),
            \Hubzero\Facades\Route::url('index.php?option=com_storefront&id=0'),
            $controllerName == 'products'
        );
        \Hubzero\Facades\Submenu::addEntry(
            Lang::txt('COM_STOREFRONT_COLLECTIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_storefront&controller=collections&id=0'),
            $controllerName == 'collections'
        );
        \Hubzero\Facades\Submenu::addEntry(
            Lang::txt('COM_STOREFRONT_OPTION_GROUPS'),
            \Hubzero\Facades\Route::url('index.php?option=com_storefront&controller=optiongroups&id=0'),
            $controllerName == 'optiongroups'
        );

        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'products';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
