<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin checking for getting user consent to monitor
 */
namespace Plugins\System\Userconsent;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\User;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Session;

class Userconsent extends Plugin
{
    /**
     * Hook for after parsing route
     *
     * @return void
     */
    public function onAfterRoute()
    {
        if (User::isGuest()) {
            $current  = Request::getWord('option', '');
            $current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
            $current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
            $current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

            if (App::isSite()) {
                $pages = [
                    'com_login',
                    'com_login.login',
                    'com_users.login'
                ];

                $granted = Session::get('user_consent', false);

                if (in_array($current, $pages) && !$granted) {
                    Request::setVar('option', 'com_users');
                    Request::setVar('view', 'userconsent');
                }
            } elseif (App::isAdmin()) {
                $exceptions = [
                    'com_login.grantconsent'
                ];

                $granted = Session::get('user_consent', false);

                if (!in_array($current, $exceptions) && !$granted) {
                    Request::setVar('option', 'com_login');
                    Request::setVar('task', 'consent');
                }
            }
        }
    }
}
