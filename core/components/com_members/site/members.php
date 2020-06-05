<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Site;

include_once dirname(__DIR__) . DS . 'helpers' . DS . 'imghandler.php';
include_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
include_once dirname(__DIR__) . DS . 'models' . DS . 'member.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'profiles'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'profiles';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
