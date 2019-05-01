<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin;

if (!\User::authorise('core.manage', 'com_citations'))
{
	return \App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}

require_once dirname(__DIR__) . DS . 'helpers' . DS . 'permissions.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'format.php';
require_once dirname(__DIR__) . DS . 'models'  . DS . 'format.php';

$controllerName = \Request::getCmd('controller', 'citations');
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'citations';
}

\Submenu::addEntry(
	\Lang::txt('CITATIONS'),
	\Route::url('index.php?option=com_citations&controller=citations'),
	($controllerName == 'citations' && \Request::getCmd('task', '') != 'stats')
);
\Submenu::addEntry(
	\Lang::txt('CITATION_STATS'),
	\Route::url('index.php?option=com_citations&controller=citations&task=stats'),
	($controllerName == 'citations' && \Request::getCmd('task', '') == 'stats')
);
\Submenu::addEntry(
	\Lang::txt('CITATION_TYPES'),
	\Route::url('index.php?option=com_citations&controller=types'),
	$controllerName == 'types'
);
\Submenu::addEntry(
	\Lang::txt('CITATION_SPONSORS'),
	\Route::url('index.php?option=com_citations&controller=sponsors'),
	$controllerName == 'sponsors'
);
\Submenu::addEntry(
	\Lang::txt('CITATION_FORMAT'),
	\Route::url('index.php?option=com_citations&controller=format'),
	$controllerName == 'format'
);

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst($controllerName);

// Initiate controller
$controller = new $controllerName();
$controller->execute();
