<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Site;

require_once dirname(__DIR__) . DS . 'models' . DS . 'entry.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'usage.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';

require_once \Component::path('com_tools') . DS . 'tables' . DS . 'tool.php';
require_once \Component::path('com_tools') . DS . 'tables' . DS . 'version.php';
require_once \Component::path('com_tools') . DS . 'tables' . DS . 'author.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'resources'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'resources';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
