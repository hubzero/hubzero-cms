<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oauth\Site;

$controllerName = \Request::getCmd('controller', 'authorize');

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	throw new \Exception('Specified controller does not exist.', 404);
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
