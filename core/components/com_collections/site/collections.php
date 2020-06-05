<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Site;

require_once dirname(__DIR__) . DS . 'models' . DS . 'archive.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'collections'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'collections';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
