<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Admin;

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
class Projects extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!User::authorise('core.manage', 'com_projects')) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = Request::getCmd('controller', 'projects');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'projects';
        }

        Submenu::addEntry(
            Lang::txt('COM_PROJECTS'),
            Route::url('index.php?option=com_projects'),
            ($controllerName == 'projects' || $controllerName == 'team')
        );
        Submenu::addEntry(
            Lang::txt('COM_PROJECTS_ACTIVITY'),
            Route::url('index.php?option=com_projects&controller=activity&project=0'),
            $controllerName == 'activity'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
