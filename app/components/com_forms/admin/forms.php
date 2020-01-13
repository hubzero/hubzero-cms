<?php
/*
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Admin;

$componentName = 'com_forms';
$componentAdminPath = Component::path($componentName) . '/admin';

require_once "$componentAdminPath/helpers/permissions.php";

use \App;
use \Lang;
use \Request;
use \Route;
use \Submenu;
use \User;

$defaultControllerName = 'forms';
$controllerName = Request::getCmd('controller');
$taskName = Request::getCmd('task');

if (!User::authorise('core.manage', $componentName))
{
	return App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
}

if (!file_exists("$componentAdminPath/controllers/$controllerName.php"))
{
	$controllerName = $defaultControllerName;
}

require_once "$componentAdminPath/controllers/$controllerName.php";

$submenuEntries = [
	[
		'text' => 'LABEL TEXT',
		'url' => Route::url(''), // e.g. Route::url('index.php?option=com_safekids&controller=events&task=display')
		'selectedTest' => (true && false) // e.g. ($controllerName === 'events' && ($task === '' || $task === $defaultTask))
	]
];

foreach ($submenuEntries as $entry)
{
	Submenu::addEntry($entry['text'], $entry['url'], $entry['selectedTest']);
}

$controllerClassNameMap = [
	'forms' => 'Forms'
];

$controllerClassName = __NAMESPACE__ . "\\Controllers\\" . $controllerClassNameMap[$controllerName];

$controller = new $controllerClassName();

$controller->execute();
