<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Us;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Log;
use Hubzero\Facades\User;
use Request;

/**
 * Plugin for automatically adding users to the location_us group based on IP country lookup
 */
class Us extends Plugin
{
    public function onLoginUser($user, $options = array())
    {
        return $this->onUserLogin($user, $options);
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param   array  $user     holds the user data
     * @param   array  $options  holding options (remember, autoregister, group)
     * @return  bool
     */
    public function onUserLogin($user, $options = array())
    {
        if (!($uid = $this->userId($user))) {
            return;
        }
        $username = isset($user['username']) ? $user['username'] : $uid;

        if (\Hubzero\User\Group::hasMember('d1_nation', $uid)) {
            Log::debug('[' . $username . '] is a member of [d1_nation], not a member of [location_us].');

            \Hubzero\User\Group::setMember('location_us', $uid, false);
            return true;
        }

        $ip = Request::ip();

        // IPv4 or IPv6, through the table for the address's family
        $country = \Hubzero\Geocode\Geocode::ipcountry($ip);

        // location_us follows where the user signs in from now, for access
        // control: an address not known to be in the US (no range, or no
        // geo database) is not a US address
        Log::debug($ip . ' is ' . ($country == 'us' ? 'a' : 'not a') . ' US address' . ($country === '' ? ' (no country found)' : '') . ', [' . $username . '] ' . ($country == 'us' ? 'in' : 'not in') . ' group [location_us].');

        \Hubzero\User\Group::setMember('location_us', $uid, $country == 'us');

        return true;
    }

    public function onAfterDeleteUser($user, $success, $msg)
    {
        return $this->onUserAfterDelete($user, $success, $msg);
    }

    /**
     * Method is called after user data is deleted from the database
     *
     * @param   array   $user     holds the user data
     * @param   bool    $success  true if user was successfully stored in the database
     * @param   string  $msg      message
     */
    public function onUserAfterDelete($user, $success, $msg)
    {
        \Hubzero\User\Group::setMember('location_us', (int) $user['id'], false);
    }


    /**
     * The ID of the user signing in
     *
     * From the session, as user/hubzero has put the user there by now. If it
     * has not (the session still holds the guest, 0), this plugin is ordered
     * before user/hubzero: look the user up by name instead, and say so.
     *
     * @param   array    $user  The user data passed to onUserLogin
     * @return  integer  0 when the user cannot be found
     */
    private function userId($user)
    {
        $uid = (int) User::get('id');

        if ($uid) {
            return $uid;
        }

        $username = isset($user['username']) ? (string) $user['username'] : '';

        Log::warning(get_class($this) . ': no user in the session at sign-in of [' . $username . ']; is this plugin ordered before user/hubzero? Looking the user up by name.');

        return $username === '' ? 0 : (int) User::getInstance($username)->get('id');
    }
}
