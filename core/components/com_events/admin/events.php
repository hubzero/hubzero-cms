<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Admin;

if (!\User::authorise('core.manage', 'com_events'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'tags.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'date.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'category.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'event.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'config.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'page.php';
require_once dirname(__DIR__) . DS . 'tables' . DS . 'respondent.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'csv.php';

$controllerName = \Request::getCmd('controller', 'events');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'events';
}

\Submenu::addEntry(
	\Lang::txt('COM_EVENTS'),
	\Route::url('index.php?option=com_events&controller=events'),
	$controllerName == 'events'
);
\Submenu::addEntry(
	\Lang::txt('COM_EVENTS_CATEGORIES'),
	\Route::url('index.php?option=com_categories&extension=com_events'),
	$controllerName == 'categories'
);
\Submenu::addEntry(
	\Lang::txt('COM_EVENTS_CONFIGURATION'),
	\Route::url('index.php?option=com_events&controller=configure'),
	$controllerName == 'configure'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();
