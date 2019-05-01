<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Categories\Admin;

use Request;

// Access check.
if (!\User::authorise('core.manage', Request::getCmd('extension')))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Load needed files
require_once dirname(__DIR__) . '/models/category.php';
require_once __DIR__ . '/helpers/categories.php';

// Determine task
$task = Request::getCmd('task');
if (strpos($task, '.') !== false)
{
	$splitTask = explode('.', $task);
	Request::setVar('task', $splitTask[1]);
}

// Get the controller
$defaultController = 'categories';
$controllerName = Request::getCmd('controller', $defaultController);
if (!file_exists(__DIR__ . '/controllers/' . $controllerName . '.php'))
{
	$controllerName = $defaultController;
}
require_once __DIR__ . '/controllers/' . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Execute
$controller = new $controllerName();
$controller->execute();
