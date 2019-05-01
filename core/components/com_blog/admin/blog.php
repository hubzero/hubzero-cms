<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Admin;

if (!\User::authorise('core.manage', 'com_blog'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'archive.php';
require_once __DIR__ . DS . 'helpers' . DS . 'permissions.php';
require_once __DIR__ . DS . 'helpers' . DS . 'html.php';

$scope = \Request::getCmd('scope', 'site');
$controllerName = \Request::getCmd('controller', 'entries');

\Submenu::addEntry(
	\Lang::txt('COM_BLOG_MENU_ENTRIES'),
	\Route::url('index.php?option=com_blog&controller=entries'),
	($controllerName == 'entries')
);
\Submenu::addEntry(
	\Lang::txt('COM_BLOG_MENU_COMMENTS'),
	\Route::url('index.php?option=com_blog&controller=comments'),
	($controllerName == 'comments')
);

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'entries';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
