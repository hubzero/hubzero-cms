<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Admin;

// Authorization check
if (!\User::authorise('core.manage', 'com_wiki'))
{
	return \App::abort(403, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

// Include scripts
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'parser.php';
require_once dirname(__DIR__) . DS . 'models' . DS . 'book.php';

// Initiate controller
$controllerName = \Request::getCmd('controller', 'pages');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'pages';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

\Submenu::addEntry(
	\Lang::txt('COM_WIKI_PAGES'),
	\Route::url('index.php?option=com_wiki'),
	true
);

require_once dirname(dirname(__DIR__)) . DS . 'com_plugins' . DS . 'helpers' . DS . 'plugins.php';
if (\Components\Plugins\Helpers\Plugins::getActions()->get('core.manage'))
{
	\Submenu::addEntry(
		\Lang::txt('COM_WIKI_PLUGINS'),
		\Route::url('index.php?option=com_plugins&view=plugins&filter_folder=wiki&filter_type=wiki')
	);
}

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
