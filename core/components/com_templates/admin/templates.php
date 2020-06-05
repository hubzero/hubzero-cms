<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Templates\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_templates'))
{
	return \App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'template.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';

// Include controller
$controllerName = \Request::getCmd('controller', 'styles');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'styles';
}

\Components\Templates\Helpers\Utilities::addSubmenu($controllerName);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
