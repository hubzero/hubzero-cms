<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin;

use Hubzero\Component\AbstractComponent;

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
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_courses')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'courses');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'courses';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_COURSES_COURSES'),
            \Hubzero\Facades\Route::url('index.php?option=com_courses&controller=courses'),
            (!in_array($controllerName, array('students', 'roles', 'pages')))
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_COURSES_PAGES'),
            \Hubzero\Facades\Route::url('index.php?option=com_courses&controller=pages&course=0'),
            $controllerName == 'pages'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_COURSES_STUDENTS'),
            \Hubzero\Facades\Route::url('index.php?option=com_courses&controller=students&offering=0&section=0'),
            $controllerName == 'students'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_COURSES_ROLES'),
            \Hubzero\Facades\Route::url('index.php?option=com_courses&controller=roles'),
            $controllerName == 'roles'
        );

        $canDo = \Components\Plugins\Helpers\Plugins::getActions();
        if ($canDo->get('core.manage')) {
            \Hubzero\Facades\Submenu::addEntry(
                \Hubzero\Facades\Lang::txt('COM_COURSES_PLUGINS'),
                \Hubzero\Facades\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=courses&filter_type=courses')
            );
        }

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
