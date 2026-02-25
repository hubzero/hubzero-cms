<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Tools extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_tools')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'pipeline');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'pipeline';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TOOLS_PIPELINE'),
            \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=pipeline'),
            $controllerName == 'pipeline'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TOOLS_HOSTS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=hosts'),
            $controllerName == 'hosts'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TOOLS_HOST_TYPES'),
            \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=hosttypes'),
            $controllerName == 'hosttypes'
        );
        if (\Hubzero\Facades\Component::params('com_tools')->get('zones')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_TOOLS_ZONES'),
                \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=zones'),
                $controllerName == 'zones'
            );
        }
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TOOLS_SESSIONS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=sessions'),
            $controllerName == 'sessions'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TOOLS_USER_PREFS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=preferences'),
            $controllerName == 'preferences'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_TOOLS_HANDLERS'),
            \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=handlers'),
            $controllerName == 'handlers'
        );

        if (\Hubzero\Facades\Component::params('com_tools')->get('windows_key_id')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_TOOLS_WINDOWS'),
                \Hubzero\Facades\Route::url('index.php?option=com_tools&controller=windows'),
                $controllerName == 'windows'
            );
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
