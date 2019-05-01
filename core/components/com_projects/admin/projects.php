<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Admin;

if (!\User::authorise('core.manage', 'com_projects'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'project.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'database.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'database.version.php';

$controllerName = \Request::getCmd('controller', 'projects');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
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
