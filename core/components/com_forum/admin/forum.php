<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Admin;

if (!\User::authorise('core.manage', 'com_forum'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'manager.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

$controllerName = \Request::getCmd('controller', 'sections');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'sections';
}

\Submenu::addEntry(
	\Lang::txt('COM_FORUM_SECTIONS'),
	\Route::url('index.php?option=com_forum&controller=sections'),
	($controllerName == 'sections')
);
\Submenu::addEntry(
	\Lang::txt('COM_FORUM_CATEGORIES'),
	\Route::url('index.php?option=com_forum&controller=categories&section_id=-1'),
	($controllerName == 'categories')
);
\Submenu::addEntry(
	\Lang::txt('COM_FORUM_THREADS'),
	\Route::url('index.php?option=com_forum&controller=threads&category_id=-1'),
	($controllerName == 'threads')
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
