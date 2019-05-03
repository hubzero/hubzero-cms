<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Admin;

if (!\User::authorise('core.manage', 'com_services'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'subscription.php';

$controllerName = \Request::getCmd('controller', 'services');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'services';
}

\Submenu::addEntry(
	\Lang::txt('COM_SERVICES_SERVICES'),
	\Route::url('index.php?option=com_services&controller=services'),
	$controllerName == 'services'
);
\Submenu::addEntry(
	\Lang::txt('COM_SERVICES_SUBSCRIPTIONS'),
	\Route::url('index.php?option=com_services&controller=subscriptions'),
	$controllerName == 'subscriptions'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
