<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Admin;

use Submenu;
use Lang;
use App;

// Access check.
if (!\User::authorise('core.manage', 'com_languages'))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'utilities.php';

$controllerName = \Request::getCmd('controller', 'installed');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	App::abort(404, Lang::txt('Controller not found.'));
}

Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_INSTALLED_SITE'),
	Route::url('index.php?option=com_languages&controller=installed&client=0'),
	($controllerName == 'installed')
);
Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_INSTALLED_ADMINISTRATOR'),
	Route::url('index.php?option=com_languages&controller=installed&client=1'),
	($controllerName == 'installed' && Request::getInt('client'))
);
Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_CONTENT'),
	Route::url('index.php?option=com_languages&controller=languages'),
	($controllerName == 'languages')
);
Submenu::addEntry(
	Lang::txt('COM_LANGUAGES_SUBMENU_OVERRIDES'),
	Route::url('index.php?option=com_languages&controller=overrides'),
	($controllerName == 'overrides')
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// initiate controller
$controller = new $controllerName();
$controller->execute();
