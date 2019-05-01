<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site;

// Include publication model
require_once dirname(__DIR__) . DS . 'models' . DS . 'publication.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'logs.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'usage.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'resourceMapGenerator.php';

$view = \Request::getCmd('view', 'publications');
$controllerName = \Request::getCmd('controller', $view);
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'publications';
	\Request::setVar('task', \Request::getCmd('task', $view));
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
