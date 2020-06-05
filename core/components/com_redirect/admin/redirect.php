<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_redirect'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include controller
$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'links'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	return \App::abort(404, \Lang::txt('Controller "%s" not found.', $controllerName));
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
