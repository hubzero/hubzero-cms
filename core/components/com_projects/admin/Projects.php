<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        }

        $controllerName = Request::getCmd('controller', 'projects');
        if (
            !is_subclass_of(
                __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName),
                \Hubzero\Component\ControllerInterface::class,
            )
        ) {
            $controllerName = 'projects';
        }

        Submenu::addEntry(
            Lang::txt('COM_PROJECTS'),
            Route::url('index.php?option=com_projects'),
            ($controllerName == 'projects' || $controllerName == 'team'),
        );
        Submenu::addEntry(
            Lang::txt('COM_PROJECTS_ACTIVITY'),
            Route::url('index.php?option=com_projects&controller=activity&project=0'),
            $controllerName == 'activity',
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // initiate controller
        /** @var \Hubzero\Component\ControllerInterface $controller Every controller extends one that is */
        $controller = new $controllerName();
        $controller->execute();
    }
}
