<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Admin;

// Access check.
if (!\User::authorise('core.manage', 'com_content'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . '/models/article.php';
require_once __DIR__ . '/helpers/permissions.php';

$task = Request::getCmd('task');
if (strpos($task, '.') !== false)
{
	$splitTask = explode('.', $task);
	Request::setVar('task', $splitTask[1]);
}
$defaultController = 'articles';
$controllerName = Request::getCmd('controller', $defaultController);

\Submenu::addEntry(
	\Lang::txt('COM_CONTENT_ARTICLES'),
	\Route::url('index.php?option=com_content&controller=' . $defaultController),
	($controllerName == $defaultController)
);
\Submenu::addEntry(
	\Lang::txt('COM_CONTENT_SUBMENU_CATEGORIES'),
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
