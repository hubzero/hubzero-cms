<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Events extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_events')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'events');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'events';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_EVENTS'),
            \Hubzero\Facades\Route::url('index.php?option=com_events&controller=events'),
            $controllerName == 'events'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_EVENTS_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_categories&extension=com_events'),
            $controllerName == 'categories'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_EVENTS_CONFIGURATION'),
            \Hubzero\Facades\Route::url('index.php?option=com_events&controller=configure'),
            $controllerName == 'configure'
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
