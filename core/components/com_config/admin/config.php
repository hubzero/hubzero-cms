<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Admin;

// Access checks are done internally because of different requirements for the two controllers.

// Tell the browser not to cache this page.
\App::get('response')->headers->set('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

if (strstr(\Request::getCmd('task'), '.'))
{
	@list($ctrl, $task) = explode('.', \Request::getCmd('task'));
	\Request::setVar('controller', $ctrl);
	\Request::setVar('task', $task);
}

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'application'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	\App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';

$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

// Execute the controller.
$controller = new $controllerName();
$controller->execute();
