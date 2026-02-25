<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Site;

use Hubzero\Component\AbstractComponent;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;

/**
 * Component entry point
 */
class Forum extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $controllerName = \Hubzero\Facades\Request::getCmd('controller', \Hubzero\Facades\Request::getCmd('view', 'sections'));
        if (!class_exists(__NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName)))) {
            $controllerName = 'sections';
        }
        $controllerName = __NAMESPACE__ . '\\Controllers\\' . ucfirst(strtolower($controllerName));

        if (!User::authorise('core.access', 'com_forum')) {
            $return = base64_encode(Request::getString('REQUEST_URI', '', 'server'));
                //$return = base64_encode($_SERVER['REQUEST_URI']);
            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . $return, false),
                Lang::txt('COM_FORUM_ALERTLOGIN_REQUIRED'),
                'warning'
            );
        }

        // Instantiate controller
        $controller = new $controllerName();
        $controller->execute();
    }
}
