<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin;

if (!\User::authorise('core.manage', 'com_system'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'info'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'info';
}

\Submenu::addEntry(
	\Lang::txt('COM_SYSTEM_LDAP'),
	\Route::url('index.php?option=com_system&controller=ldap'),
	$controllerName == 'ldap'
);
\Submenu::addEntry(
	\Lang::txt('COM_SYSTEM_GEO'),
	\Route::url('index.php?option=com_system&controller=geodb'),
	$controllerName == 'geodb'
);
\Submenu::addEntry(
	\Lang::txt('COM_SYSTEM_APC'),
	\Route::url('index.php?option=com_system&controller=apc'),
	$controllerName == 'apc'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
