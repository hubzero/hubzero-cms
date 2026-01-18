<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


function dv_auth()
{
    global $dv_conf;

    $allowedUsers = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : null;
    $allowedGroups = isset($dd['acl']['allowed_groups']) ? $dd['acl']['allowed_groups'] : null;

    if (isset($allowedUsers) && (is_array($allowedUsers) || $allowedUsers === false || $allowedUsers == 'registered')) {
        $dv_conf['acl']['allowed_users'] = $allowedUsers;
    }

    if (isset($allowedGroups) && (is_array($allowedGroups) || $allowedGroups === false)) {
        $dv_conf['acl']['allowed_groups'] = $allowedGroups;
    }

    $usersAllowed = $dv_conf['acl']['allowed_users'];
    $groupsAllowed = $dv_conf['acl']['allowed_groups'];

    if ($usersAllowed === false && $groupsAllowed === false) {
        return true;
    } elseif (User::isGuest()) {
        $redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
        $login_url = '/login';
        $url = $login_url . $redir_url;
        header('Location: ' . $url);
        return;
    }

    if ($usersAllowed !== false && $usersAllowed == 'registered' && !User::isGuest()) {
        return true;
    } elseif (isset($usersAllowed) && is_array($usersAllowed) && !User::isGuest()) {
        if (in_array(User::get('username'), $usersAllowed)) {
            return true;
        }
    }

    if ($groupsAllowed !== false && is_array($groupsAllowed) && !User::isGuest()) {
        $groups = \Hubzero\User\Helper::getGroups(User::get('id'));
        if ($groups && count($groups)) {
            foreach ($groups as $g) {
                if (in_array($g->cn, $dv_conf['acl']['allowed_groups'])) {
                    return true;
                }
            }
        }
    }

    return false;
}
