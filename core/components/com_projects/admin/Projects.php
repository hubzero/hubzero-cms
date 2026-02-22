<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Projects extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\User::authorise('core.manage', 'com_projects')) {
            \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Request::getCmd('controller', 'projects');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'projects';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_PROJECTS'),
            \Route::url('index.php?option=com_projects'),
            ($controllerName == 'projects' || $controllerName == 'team')
        );
        \Submenu::addEntry(
            \Lang::txt('COM_PROJECTS_ACTIVITY'),
            \Route::url('index.php?option=com_projects&controller=activity&project=0'),
            $controllerName == 'activity'
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
