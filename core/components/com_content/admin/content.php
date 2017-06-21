<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Components\Content\Admin;
require_once Component::path('com_content') . '/models/article.php';
require_once Component::path('com_content') . '/admin/helpers/permissions.php';
// Access check.
if (!User::authorise('core.manage', 'com_content')) 
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

$task = Request::getCmd('task');
if (strpos($task, '.') !== false)
{
	$splitTask = explode('.', $task);
	Request::setVar('task', $splitTask[1]);
}
$defaultController = 'articles';
$controllerName = Request::getCmd('controller', $defaultController);
\Submenu::addEntry(
    \Lang::txt('Articles'),
    \Route::url('index.php?option=com_content&controller=' . $defaultController),
    ($controllerName == $defaultController)
);

\Submenu::addEntry(
    \Lang::txt('Categories'),
    \Route::url('index.php?option=com_categories&extension=com_content')
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
