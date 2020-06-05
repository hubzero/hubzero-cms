<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site;

require_once dirname(__DIR__) . DS . 'models' . DS . 'newsletter.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailinglist.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'mailing.php';

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'helper.php';

//build controller path and name
$controllerName = \Request::getCmd('controller', 'newsletters');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'newsletters';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller and execute
$controller = new $controllerName();
$controller->execute();
