<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Lib;

use Components\Dataviewer\Site\DvConfig;
use Hubzero\Facades\User;

class Auth
{
    /**
     * @return  bool|null
     */
    public static function dvAuth()
    {
        $dd = DvConfig::$dv_conf['dd'] ?? [];
        $allowedUsers = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : null;
        $allowedGroups = isset($dd['acl']['allowed_groups']) ? $dd['acl']['allowed_groups'] : null;

        $isValid = is_array($allowedUsers) || $allowedUsers === false || $allowedUsers == 'registered';
        if (isset($allowedUsers) && $isValid) {
            DvConfig::$dv_conf['acl']['allowed_users'] = $allowedUsers;
        }

        if (isset($allowedGroups) && (is_array($allowedGroups) || $allowedGroups === false)) {
            DvConfig::$dv_conf['acl']['allowed_groups'] = $allowedGroups;
        }

        $usersAllowed = DvConfig::$dv_conf['acl']['allowed_users'];
        $groupsAllowed = DvConfig::$dv_conf['acl']['allowed_groups'];

        if ($usersAllowed === false && $groupsAllowed === false) {
            return true;
        } elseif (User::isGuest()) {
            $redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
            $login_url = '/login';
            $url = $login_url . $redir_url;
            header('Location: ' . $url);
            return null;
        }

        // Guests were sent to log in above
        if ($usersAllowed !== false && $usersAllowed == 'registered') {
            return true;
        } elseif (isset($usersAllowed) && is_array($usersAllowed)) {
            if (in_array(User::get('username'), $usersAllowed)) {
                return true;
            }
        }

        if ($groupsAllowed !== false && is_array($groupsAllowed)) {
            $groups = \Hubzero\User\Helper::getGroups(User::get('id'));
            if ($groups) {
                foreach ($groups as $g) {
                    if (in_array($g->cn, DvConfig::$dv_conf['acl']['allowed_groups'])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
