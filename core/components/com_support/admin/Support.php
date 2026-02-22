<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Support extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\User::authorise('core.manage', 'com_support')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Request::getCmd('controller', 'tickets');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'tickets';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_TICKETS'),
            \Route::url('index.php?option=com_support&controller=tickets'),
            $controllerName == 'tickets'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_CATEGORIES'),
            \Route::url('index.php?option=com_support&controller=categories'),
            $controllerName == 'categories'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_QUERIES'),
            \Route::url('index.php?option=com_support&controller=queries'),
            $controllerName == 'queries'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_MESSAGES'),
            \Route::url('index.php?option=com_support&controller=messages'),
            $controllerName == 'messages'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_STATUSES'),
            \Route::url('index.php?option=com_support&controller=statuses'),
            $controllerName == 'statuses'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_ABUSE'),
            \Route::url('index.php?option=com_support&controller=abusereports'),
            $controllerName == 'abusereports'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_STATS'),
            \Route::url('index.php?option=com_support&controller=stats'),
            $controllerName == 'stats'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_SUPPORT_ACL'),
            \Route::url('index.php?option=com_support&controller=acl'),
            $controllerName == 'acl'
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
