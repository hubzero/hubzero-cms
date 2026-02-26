<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin;

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
class Courses extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!User::authorise('core.manage', 'com_courses')) {
            App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = Request::getCmd('controller', 'courses');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'courses';
        }

        Submenu::addEntry(
            Lang::txt('COM_COURSES_COURSES'),
            Route::url('index.php?option=com_courses&controller=courses'),
            (!in_array($controllerName, array('students', 'roles', 'pages')))
        );
        Submenu::addEntry(
            Lang::txt('COM_COURSES_PAGES'),
            Route::url('index.php?option=com_courses&controller=pages&course=0'),
            $controllerName == 'pages'
        );
        Submenu::addEntry(
            Lang::txt('COM_COURSES_STUDENTS'),
            Route::url('index.php?option=com_courses&controller=students&offering=0&section=0'),
            $controllerName == 'students'
        );
        Submenu::addEntry(
            Lang::txt('COM_COURSES_ROLES'),
            Route::url('index.php?option=com_courses&controller=roles'),
            $controllerName == 'roles'
        );

        $canDo = \Components\Plugins\Helpers\Plugins::getActions();
        if ($canDo->get('core.manage')) {
            Submenu::addEntry(
                Lang::txt('COM_COURSES_PLUGINS'),
                Route::url('index.php?option=com_plugins&view=plugins&filter_folder=courses&filter_type=courses')
            );
        }

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
