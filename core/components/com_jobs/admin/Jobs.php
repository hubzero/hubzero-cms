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
		if (!\User::authorise('core.manage', 'com_jobs')) {
			\App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
		}

        $controllerName = \Request::getCmd('controller', 'jobs');
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName))) {
            $controllerName = 'jobs';
        }

        \Submenu::addEntry(
            \Lang::txt('COM_JOBS_JOBS'),
            \Route::url('index.php?option=com_jobs&controller=jobs'),
            $controllerName == 'jobs'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_JOBS_CATEGORIES'),
            \Route::url('index.php?option=com_jobs&controller=categories'),
            $controllerName == 'categories'
        );
        \Submenu::addEntry(
            \Lang::txt('COM_JOBS_TYPES'),
            \Route::url('index.php?option=com_jobs&controller=types'),
            $controllerName == 'types'
        );

        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

        // Initiate controller
        $controller = new $controllerName();
        $controller->execute();
        $controller->redirect();
    }
}
