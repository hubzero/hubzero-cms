<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Wishlist extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Authorization check
        if (!\User::authorise('core.manage', 'com_wishlist')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Request::getCmd('controller', 'lists');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'lists';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_WISHLIST_LISTS'),
            \Route::url('index.php?option=com_wishlist&controller=lists'),
            ($controllerName == 'lists')
        );
        \Submenu::addEntry(
            \Lang::txt('COM_WISHLIST_WISHES'),
            \Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=0'),
            ($controllerName == 'wishes')
        );
        \Submenu::addEntry(
            \Lang::txt('COM_WISHLIST_COMMENTS'),
            \Route::url('index.php?option=com_wishlist&controller=comments&wish=0'),
            ($controllerName == 'comments')
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
