<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Admin;

// Access check.
if (!\User::authorise('core.manage', \Request::getCmd('extension')))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'message.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'cfg.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';

$controllerName = \Request::getCmd('controller', 'messages');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'messages';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Execute the task.
$controller = new $controllerName();
$controller->execute();
