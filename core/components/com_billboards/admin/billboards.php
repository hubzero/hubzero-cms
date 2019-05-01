<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Billboards\Admin;

if (!\User::authorise('core.manage', 'com_billboards'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include needed models and controller
require_once dirname(__DIR__) . DS . 'models' . DS . 'billboard.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'collection.php';

$controllerName = \Request::getCmd('controller', 'billboards');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'billboards';
}

\Submenu::addEntry(
	\Lang::txt('COM_BILLBOARDS'),
	\Route::url('index.php?option=com_billboards&controller=billboards'),
	$controllerName == 'billboards'
);
\Submenu::addEntry(
	\Lang::txt('COM_BILLBOARDS_COLLECTIONS'),
	\Route::url('index.php?option=com_billboards&controller=collections'),
	$controllerName == 'collections'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
