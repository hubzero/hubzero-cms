<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\User\Geo;

use Hubzero\Plugin\Plugin;

// No direct access

/**
 * Plugin for automatically adding users to a specified group based on geolocation
 */
class Geo extends Plugin
{
    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param array $user    holds the user data
     * @param array $options holding options (remember, autoregister, group)
     * @return bool
     */
    public function onUserLogin($user, $options = array())
    {
        // Get params
        $groupAlias = $this->params->get('group', false);
        $location   = $this->params->get('location', false);

        // Make sure params were set
        if (!$groupAlias || !$location) {
            return;
        }

        // Check user location
        if (\Hubzero\Geocode\Geocode::is_iplocation(Request::ip(), $location)) {
            // Opens and saves the group only if the user is not in it yet
            \Hubzero\User\Group::setMember($groupAlias, \User::getInstance($user['username'])->get('id'), true);
        }
    }

    /**
     * Method is called after user data is deleted from the database
     *
     * @param array  $user   holds the user data
     * @param bool   $succes true if user was succesfully stored in the database
     * @param string $msg    message
     */
    public function onUserAfterDelete($user, $succes, $msg)
    {
        // Check params for group name
        $groupAlias = $this->params->get('group', false);

        if ($groupAlias) {
            // Get the group
            $group = \Hubzero\User\Group::getInstance($groupAlias);

            if (is_object($group)) {
                // Remove the user from the group
                $group->remove('members', array($user['id']));

                // Update the groups
                $group->update();
            }
        }
    }
}
