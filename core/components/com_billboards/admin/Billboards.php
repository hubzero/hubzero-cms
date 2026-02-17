<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Billboards\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Billboards extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\User::authorise('core.manage', 'com_billboards')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Request::getCmd('controller', 'billboards');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'billboards';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_BILLBOARDS'),
            \Route::url('index.php?option=com_billboards&controller=billboards'),
            $controllerName == 'billboards'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_BILLBOARDS_COLLECTIONS'),
            \Route::url('index.php?option=com_billboards&controller=collections'),
            $controllerName == 'collections'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
