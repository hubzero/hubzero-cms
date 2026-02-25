<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Admin;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Jobs extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        if (!\Hubzero\Facades\User::authorise('core.manage', 'com_jobs')) {
            \Hubzero\Facades\App::abort(404, \Hubzero\Facades\Lang::txt('JERROR_ALERTNOAUTHOR'));
            return;
        }

        $controllerName = \Hubzero\Facades\Request::getCmd('controller', 'jobs');
        if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
            $controllerName = 'jobs';
        }

        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_JOBS_JOBS'),
            \Hubzero\Facades\Route::url('index.php?option=com_jobs&controller=jobs'),
            $controllerName == 'jobs'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_JOBS_CATEGORIES'),
            \Hubzero\Facades\Route::url('index.php?option=com_jobs&controller=categories'),
            $controllerName == 'categories'
        );
        \Hubzero\Facades\Submenu::addEntry(
            \Hubzero\Facades\Lang::txt('COM_JOBS_TYPES'),
            \Hubzero\Facades\Route::url('index.php?option=com_jobs&controller=types'),
            $controllerName == 'types'
        );

        require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
