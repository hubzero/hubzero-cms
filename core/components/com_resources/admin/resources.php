<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Admin;

$option = \Request::getCmd('option', 'com_resources');
$task = \Request::getWord('task', '');

if (!\User::authorise('core.manage', $option))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include models
require_once dirname(__DIR__) . DS . 'models' . DS . 'entry.php';

// Include helpers
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'tags.php';

// Include importer
require_once dirname(__DIR__) . DS . 'import' . DS . 'importer.php';

// Get controller name
$controllerName = \Request::getCmd('controller', 'items');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES'),
	\Route::url('index.php?option=' . $option),
	($controllerName == 'items' && $task != 'orphans')
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_ORPHANS'),
	\Route::url('index.php?option=' . $option . '&controller=items&task=orphans'),
	$task == 'orphans'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_TYPES'),
	\Route::url('index.php?option=' . $option . '&controller=types'),
	$controllerName == 'types'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_LICENSES'),
	\Route::url('index.php?option=' . $option . '&controller=licenses'),
	$controllerName == 'licenses'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_AUTHORS'),
	\Route::url('index.php?option=' . $option . '&controller=authors'),
	$controllerName == 'authors'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_ROLES'),
	\Route::url('index.php?option=' . $option . '&controller=roles'),
	$controllerName == 'roles'
);
require_once \Component::path('com_plugins') . DS . 'helpers' . DS . 'plugins.php';
if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_RESOURCES_PLUGINS'),
		\Route::url('index.php?option=' . $option . '&controller=plugins'),
		$controllerName == 'plugins'
	);
}
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_IMPORT'),
	\Route::url('index.php?option=' . $option . '&controller=imports'),
	$controllerName == 'imports'
);
\Submenu::addEntry(
	\Lang::txt('COM_RESOURCES_IMPORTHOOK'),
	\Route::url('index.php?option=' . $option . '&controller=importhooks'),
	$controllerName == 'importhooks'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
