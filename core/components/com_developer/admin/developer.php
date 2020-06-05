<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Admin;

// permissions check
if (!\User::authorise('core.manage', 'com_developer'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'application.php';

// Make extra sure that controller exists
$controllerName = \Request::getCmd('controller', 'applications');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'applications';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

// Add some submenu items
\Submenu::addEntry(
	\Lang::txt('COM_DEVELOPER_APPLICATIONS'),
	\Route::url('index.php?option=com_developer&controller=applications'),
	($controllerName == 'applications')
);

// Build the class name
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$component = new $controllerName();
$component->execute();
