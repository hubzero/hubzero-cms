<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Admin;

if (!\User::authorise('core.manage', 'com_jobs'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
include_once dirname(__DIR__) . DS . 'tables' . DS . 'admin.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'application.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'category.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'employer.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'job.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'prefs.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'resume.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'seeker.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'shortlist.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'stats.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'type.php';
include_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
include_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';

$controllerName = \Request::getCmd('controller', 'jobs');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
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

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
