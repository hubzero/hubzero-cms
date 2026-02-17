<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin checking for unapproved users
 */
namespace Plugins\System\Unapproved;

use Hubzero\Plugin\Plugin;

class Unapproved extends Plugin
{
    /**
     * Hook for after parsing route
     *
     * @return void
     */
    public function onAfterRoute()
    {
        if (App::isSite() && !User::isGuest()) {
            $exceptions = [
                'com_login.logout',
                'com_login.logout.login',
                'com_users.logout',
                'com_users.userlogout',
                'com_users.logout.login',
                'com_support.tickets.save.index',
                'com_support.tickets.save',
                'com_support.tickets.new.index',
                'com_support.tickets.new',
                'com_members.media.download.profiles'
            ];

            $current  = Request::getWord('option', '');
            $current .= ($controller = Request::getWord('controller', false)) ? '.' . $controller : '';
            $current .= ($task       = Request::getWord('task', false)) ? '.' . $task : '';
            $current .= ($view       = Request::getWord('view', false)) ? '.' . $view : '';

            // Pull current user data from DB rather than cached session value.
            // Proper fix should reload the session value
            $user = \Hubzero\User\User::oneByUsername(User::get('username'));

            // If guest, proceed as normal and they'll land on the login page
            if (!in_array($current, $exceptions) && !$user->get('approved')) {
                Request::setVar('option', 'com_members');
                Request::setVar('task', 'unapproved');

                $this->event->stop();
            }
        }
    }
}
