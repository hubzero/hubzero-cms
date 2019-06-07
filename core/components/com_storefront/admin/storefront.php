<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin;

$option = 'com_storefront';

if (!\User::authorise('core.manage', $option))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'Archive.php';
require_once __DIR__ . DS . 'helpers' . DS . 'permissions.php';

$scope = \Request::getCmd('scope', 'site');
$controllerName = \Request::getCmd('controller', 'products');

\Submenu::addEntry(
		Lang::txt('COM_STOREFRONT_PRODUCTS'),
		\Route::url('index.php?option=com_storefront&id=0'),
		$controllerName == 'products'
);
\Submenu::addEntry(
		Lang::txt('COM_STOREFRONT_COLLECTIONS'),
		\Route::url('index.php?option=com_storefront&controller=collections&id=0'),
		$controllerName == 'collections'
);
\Submenu::addEntry(
		Lang::txt('COM_STOREFRONT_OPTION_GROUPS'),
		\Route::url('index.php?option=com_storefront&controller=optiongroups&id=0'),
		$controllerName == 'optiongroups'
);

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'products';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
