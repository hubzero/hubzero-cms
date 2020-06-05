<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site;

include_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';

$controllerName = Request::getCmd('controller', Request::getCmd('view', 'index'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'index';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
