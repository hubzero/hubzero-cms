<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Admin;

// Authorization check
if (!\User::authorise('core.manage', 'com_wishlist'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
include_once dirname(__DIR__) . DS . 'models' . DS . 'wishlist.php';
include_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

$controllerName = \Request::getCmd('controller', 'lists');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'lists';
}

\Submenu::addEntry(
	\Lang::txt('COM_WISHLIST_LISTS'),
	\Route::url('index.php?option=com_wishlist&controller=lists'),
	($controllerName == 'lists')
);
\Submenu::addEntry(
	\Lang::txt('COM_WISHLIST_WISHES'),
	\Route::url('index.php?option=com_wishlist&controller=wishes&wishlist=0'),
	($controllerName == 'wishes')
);
\Submenu::addEntry(
	\Lang::txt('COM_WISHLIST_COMMENTS'),
	\Route::url('index.php?option=com_wishlist&controller=comments&wish=0'),
	($controllerName == 'comments')
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
