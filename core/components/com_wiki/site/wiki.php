<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site;

include_once dirname(__DIR__) . DS . 'models' . DS . 'book.php';
include_once dirname(__DIR__) . DS . 'helpers' . DS . 'editor.php';
include_once dirname(__DIR__) . DS . 'helpers' . DS . 'parser.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'pages'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'pages';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName(array('name' => 'wiki'));
$controller->execute();
