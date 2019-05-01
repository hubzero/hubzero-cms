<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Admin;

$option = 'com_cart';

if (!\User::authorise('core.manage', $option))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once __DIR__ . DS . 'helpers' . DS . 'permissions.php';

$scope = \Request::getCmd('scope', 'site');
$controllerName = \Request::getCmd('controller', 'downloads');

\Submenu::addEntry(
	\Lang::txt('COM_CART_SOFTWARE_DOWNLOADS'),
	\Route::url('index.php?option=com_cart&controller=downloads'),
	$controllerName == 'downloads'
);

\Submenu::addEntry(
	\Lang::txt('COM_CART_ORDERS'),
	\Route::url('index.php?option=com_cart&controller=orders'),
	$controllerName == 'orders'
);

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'downloads';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();

$controller->execute();
$controller->redirect();
