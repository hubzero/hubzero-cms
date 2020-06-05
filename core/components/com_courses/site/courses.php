<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site;

// Require needed files
require_once dirname(__DIR__) . DS . 'tables' . DS . 'log.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'courses.php';

// Build controller path and name
$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'courses'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'courses';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller and execute
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
