<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Site;

use App;
use Lang;
use Request;
use Route;
use User;

require_once dirname(__DIR__) . DS . 'models' . DS . 'manager.php';

$controllerName = \Request::getCmd('controller', \Request::getCmd('view', 'sections'));
if (!file_exists(__DIR__ . DS . 'controllers' . DS . $controllerName . '.php')) {
    $controllerName = 'sections';
}
require_once __DIR__ . DS . 'controllers' . DS . $controllerName . '.php';
$controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

if (!User::authorise('core.access', 'com_forum')) {
    // Signing in is the answer only for a guest; sent to the login page, a
    // signed-in user without access is sent straight back, round and round
    if (!User::isGuest()) {
        App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
    }
    $return = base64_encode(Request::getString('REQUEST_URI', '', 'server'));
    App::redirect(
        Route::url('index.php?option=com_users&view=login&return=' . $return, false),
        Lang::txt('COM_FORUM_ALERTLOGIN_REQUIRED'),
        'warning'
    );
}

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
