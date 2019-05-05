<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Admin;

if (!\User::authorise('core.manage', 'com_collections'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'orm' . DS . 'collection.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

$controllerName = \Request::getCmd('controller', 'collections');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'collections';
}

\Submenu::addEntry(
	\Lang::txt('COM_COLLECTIONS_COLLECTIONS'),
	\Route::url('index.php?option=com_collections'),
	$controllerName == 'collections'
);
\Submenu::addEntry(
	\Lang::txt('COM_COLLECTIONS_POSTS'),
	\Route::url('index.php?option=com_collections&controller=posts&collection_id=0&item_id=0'),
	$controllerName == 'posts'
);
\Submenu::addEntry(
	\Lang::txt('COM_COLLECTIONS_ITEMS'),
	\Route::url('index.php?option=com_collections&controller=items'),
	$controllerName == 'items'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
