<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin;

if (!\User::authorise('core.manage', 'com_courses'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'log.php';

$controllerName = \Request::getCmd('controller', 'courses');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'courses';
}

\Submenu::addEntry(
	\Lang::txt('COM_COURSES_COURSES'),
	\Route::url('index.php?option=com_courses&controller=courses'),
	(!in_array($controllerName, array('students', 'roles', 'pages')))
);
\Submenu::addEntry(
	\Lang::txt('COM_COURSES_PAGES'),
	\Route::url('index.php?option=com_courses&controller=pages&course=0'),
	$controllerName == 'pages'
);
\Submenu::addEntry(
	\Lang::txt('COM_COURSES_STUDENTS'),
	\Route::url('index.php?option=com_courses&controller=students&offering=0&section=0'),
	$controllerName == 'students'
);
\Submenu::addEntry(
	\Lang::txt('COM_COURSES_ROLES'),
	\Route::url('index.php?option=com_courses&controller=roles'),
	$controllerName == 'roles'
);

require_once \Component::path('com_plugins') . DS . 'helpers' . DS . 'plugins.php';
$canDo = \Components\Plugins\Helpers\Plugins::getActions();
if ($canDo->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_COURSES_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=courses&filter_type=courses')
	);
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
