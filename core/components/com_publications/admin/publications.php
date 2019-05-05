<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin;

if (!\User::authorise('core.manage', 'com_publications'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'models' . DS . 'publication.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';

// get controller name
$controllerName = \Request::getCmd('controller', 'items');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'items';
}

\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'),
	\Route::url('index.php?option=com_publications&controller=items'),
	$controllerName == 'items'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_LICENSES'),
	\Route::url('index.php?option=com_publications&controller=licenses'),
	$controllerName == 'licenses'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_CATEGORIES'),
	\Route::url('index.php?option=com_publications&controller=categories'),
	$controllerName == 'categories'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'),
	\Route::url('index.php?option=com_publications&controller=types'),
	$controllerName == 'types'
);
\Submenu::addEntry(
	\Lang::txt('COM_PUBLICATIONS_BATCH_CREATE'),
	\Route::url('index.php?option=com_publications&controller=batchcreate'),
	$controllerName == 'batchcreate'
);
require_once dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php';
if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_PUBLICATIONS_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=publications&filter_type=publications')
	);
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
