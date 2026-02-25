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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_support')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'tickets');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'tickets';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_TICKETS'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=tickets'),
            $controllerName == 'tickets'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=categories'),
            $controllerName == 'categories'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_QUERIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=queries'),
            $controllerName == 'queries'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_MESSAGES'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=messages'),
            $controllerName == 'messages'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_STATUSES'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=statuses'),
            $controllerName == 'statuses'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_ABUSE'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=abusereports'),
            $controllerName == 'abusereports'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_STATS'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=stats'),
            $controllerName == 'stats'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_SUPPORT_ACL'),
            \Hubzero\Facades\Route::url('index.php?option=com_support&controller=acl'),
            $controllerName == 'acl'
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
