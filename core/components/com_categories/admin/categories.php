<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Components\Categories\Admin;
require_once Component::path('com_categories') . '/models/category.php';
require_once Component::path('com_categories') . '/admin/helpers/categories.php';
// Access check.
if (!User::authorise('core.manage', Request::getCmd('extension')))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$task = Request::getCmd('task');
if (strpos($task, '.') !== false)
{
	$splitTask = explode('.', $task);
	Request::setVar('task', $splitTask[1]);
}
$defaultController = 'categories';
$controllerName = Request::getCmd('controller', $defaultController);
$extension = Request::getVar('extension');
Lang::load($extension, \Component::path($extension) . '/admin', null, false, true);
\Submenu::addEntry(
    \Lang::txt(strtoupper($extension) . '_ARTICLES'),
    \Route::url('index.php?option=' . $extension)
);

\Submenu::addEntry(
    \Lang::txt('Categories'),
    \Route::url('index.php?option=com_categories&extension=com_content'),
    ($controllerName == $defaultController)
);

if (!file_exists(__DIR__ . '/controllers/' . $controllerName . '.php'))
{
	$controllerName = $defaultController;
}
require_once __DIR__ . '/controllers/' . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();
$controller->execute();
$controller->redirect();
