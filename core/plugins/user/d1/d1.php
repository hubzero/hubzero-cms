<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\D1;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\Log;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

/**
 * Plugin for automatically adding users to the d1_nation group based on IP country group lookup
 */
class D1 extends Plugin
{
    public function onLoginUser($user, $options = [])
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
    public function onUserLogin($user, $options = [])
    {
        if (!($uid = $this->userId($user))) {
            return true;
        }
        $username = isset($user['username']) ? $user['username'] : $uid;

        if (\Hubzero\User\Group::hasMember('d1_override', $uid)) {
            Log::debug('plgUserD1: [' . $username . '] is in d1_override, not in d1_nation, skipping geo check.');

            \Hubzero\User\Group::setMember('d1_nation', $uid, false);
            return true;
        }

        $ip = Request::ip();

        // IPv4 or IPv6, through the table for the address's family. A country
        // can be in several groups (KP is D1 and E1), so ask about D1 itself
        // rather than taking whichever group comes first.
        $country = \Hubzero\Geocode\Geocode::ipcountry($ip);

        // d1_nation records that a user has signed in from a D1 nation; it is
        // not taken away for signing in from elsewhere, nor from an address
        // with no known country
        if ($country === '') {
            Log::debug('plgUserD1: no country for ' . $ip . ', leaving [' . $username . '] membership to group [d1_nation] unchanged.');
            return true;
        }

        if (\Hubzero\Geocode\Geocode::is_d1nation($country)) {
            Log::debug($ip . ' is in a D1 nation, [' . $username . '] in group [d1_nation].');

            \Hubzero\User\Group::setMember('d1_nation', $uid, true);
        } else {
            Log::debug($ip . ' is in [' . $country . '], not a D1 nation, leaving [' . $username . '] membership to group [d1_nation] unchanged.');
        }

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
        \Hubzero\User\Group::setMember('d1_nation', (int) $user['id'], false);
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
