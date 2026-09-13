<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site;

use App;
use Component;
use Lang;
use Request;
use Route;
use User;

// Stories are public reading by default. A hub that would rather run a walled
// garden turns on 'require_login' and the ACL's core.access then decides who
// gets in.
if (Component::params('com_story')->get('require_login', 0)
 && !User::authorise('core.access', 'com_story'))
{
	$return = base64_encode(Request::getString('REQUEST_URI', '', 'server'));

	App::redirect(
		Route::url('index.php?option=com_users&view=login&return=' . $return, false),
		Lang::txt('COM_STORY_LOGIN_REQUIRED'),
		'warning'
	);
}

require_once dirname(__DIR__) . DS . 'models' . DS . 'manager.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'thread.php';
require_once dirname(__DIR__) . DS . 'helpers' . DS . 'context.php';

$controllerName = Request::getCmd('controller', Request::getCmd('view', 'stories'));

if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'stories';
}

require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

$controller = new $controllerName();
$controller->execute();
