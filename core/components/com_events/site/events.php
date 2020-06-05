<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Site;

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'date.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'tags.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'event.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'category.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'config.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'page.php';
include_once dirname(__DIR__) . DS . 'tables' . DS . 'respondent.php';

$controllerName = \Request::getCmd('controller', 'events');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'events';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
