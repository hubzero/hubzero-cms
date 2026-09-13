<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Karma\Site;

use App;
use Lang;
use Request;
use Route;
use User;

if (User::isGuest())
{
	$return = base64_encode(Request::getString('REQUEST_URI', '', 'server'));

	App::redirect(
		Route::url('index.php?option=com_users&view=login&return=' . $return, false),
		Lang::txt('COM_KARMA_LOGIN_REQUIRED'),
		'warning'
	);
}

$controllerName = Request::getCmd('controller', Request::getCmd('view', 'karma'));

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'karma';
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();
$controller->execute();
