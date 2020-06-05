<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Installer\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_installer'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

if ($task = \Request::getCmd('task'))
{
	if (strstr($task, '.'))
	{
		@list($c, $t) = explode('.', $task);
		$t = \Request::setVar('task', trim($t));
		$c = \Request::setVar('controller', trim($c));
	}
}
$controllerName = \Request::getCmd('controller', 'manage');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once __DIR__ . DS . 'helpers' . DS . 'installer.php';
\Components\Installer\Admin\Helpers\Installer::addSubmenu($controllerName);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
