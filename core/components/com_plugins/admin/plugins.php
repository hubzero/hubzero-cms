<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Plugins\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_plugins'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$task = \Request::getCmd('task');
if (strstr($task, '.'))
{
	\Request::setVar('controller', strstr($task, '.', true));
	\Request::setVar('task', strstr($task, '.'));
}
$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'plugins'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'plugins.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'plugin.php';
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
