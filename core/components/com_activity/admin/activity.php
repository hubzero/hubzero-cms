<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Activity\Admin;

if (!\User::authorise('core.manage', 'com_activity'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$controllerName = \Request::getCmd('controller', 'entries');

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'entries';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
