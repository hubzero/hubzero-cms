<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Site;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'format.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'download.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'citation.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'citations'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'citations';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
