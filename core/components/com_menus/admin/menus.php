<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_menus'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Determine task
$task = Request::getCmd('task');
if (strpos($task, '.') !== false)
{
	$splitTask = explode('.', $task);
	Request::setVar('task', $splitTask[1]);
}

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'menus'));
if (!file_exists(__DIR__ . '/controllers/' . $controllerName . '.php'))
{
	$controllerName = 'menus';
}

\Submenu::addEntry(
	\Lang::txt('COM_MENUS_SUBMENU_MENUS'),
	\Route::url('index.php?option=com_menus&controller=menus', false),
	$controllerName == 'menus'
);
\Submenu::addEntry(
	\Lang::txt('COM_MENUS_SUBMENU_ITEMS'),
	\Route::url('index.php?option=com_menus&controller=items', false),
	$controllerName == 'items'
);

require_once dirname(__DIR__) . '/helpers/menus.php';
require_once dirname(__DIR__) . '/models/menu.php';

require_once __DIR__ . '/controllers/' . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
