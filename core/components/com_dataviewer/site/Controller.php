<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

class Controller
{
    public static function dispatch()
    {
        $db_id = array();

        $db_id['id'] = \Request::getString('db', '');
        $db_info = explode(':', $db_id['id']);
        $db_id['name'] = $db_info[0];
        $db_id['mode'] = isset($db_info[1]) ? $db_info[1] : 'db';
        $db_id['extra'] = isset($db_info[2]) ? $db_info[2] : false;

        DvConfig::$dv_conf['settings']['db_id'] = $db_id;

        /* Include database mode specific functionality */
        $modeClass = __NAMESPACE__ . '\\Modes\\Mode' . ucfirst($db_id['mode']);

        /* Update config with DB specific values */
        $modeClass::getConf($db_id);

        /* Store mode class for use in task methods */
        DvConfig::$dv_conf['settings']['mode_class'] = $modeClass;

        $task = strtolower(\Request::getCmd('task'));
        $taskMethod = 'task' . ucfirst($task);

        if (method_exists(self::class, $taskMethod)) {
            self::$taskMethod($db_id);
        } else {
            \App::abort(404, 'Invalid or Missing Dataview', 'Invalid or Missing Dataview');
        }
    }

    public static function taskView($db_id)
    {
        $view = 'spreadsheet';

        $modeClass = DvConfig::$dv_conf['settings']['mode_class'];
        $dd = $modeClass::getDd($db_id);

        if (!$dd) {
            throw new \Exception('Invalid DataView', 404);
            return;
        }

        if (!self::authorize($dd)) {
            print ('<br /><p class="warning">Sorry, you are not authorized to view this page.</p>');
            return;
        }

        $filter = strtolower(\Request::getString('format', 'json'));
        $filterClass = __NAMESPACE__ . '\\Filter\\' . ucfirst($filter);

        $modeClass::pathway($dd);

        $viewClass = __NAMESPACE__ . '\\View\\' . ucfirst($view);
        $viewClass::render($dd);
    }

    public static function taskData($db_id)
    {
        $modeClass = DvConfig::$dv_conf['settings']['mode_class'];
        $dd = $modeClass::getDd($db_id);


        if (!self::authorize($dd)) {
            print ('<br /><p class="error">Sorry, you are not authorized to view this page.</p>');
            return;
        }

        $filter = strtolower(\Request::getString('type', 'csv'));
        $filterClass = __NAMESPACE__ . '\\Filter\\' . ucfirst($filter);

        if ($dd) {
            $link = Lib\Db::getDb();

            $sql = Lib\Db::queryGen($dd);

            $res = Lib\Db::getResults($sql, $dd);

            $filteredResults = $filterClass::filter($res, $dd);

            print $filteredResults;
            exit(0);
        } else {
            print "<p class=\"error\">Invalid Request</p>";
            exit(1);
        }
    }

    public static function authorize($dd)
    {
        $allowedUsers = isset($dd['acl']['allowed_users']) ? $dd['acl']['allowed_users'] : null;
        $isValid = is_array($allowedUsers) || $allowedUsers === false || $allowedUsers == 'registered';
        if (isset($allowedUsers) && $isValid) {
            DvConfig::$dv_conf['acl']['allowed_users'] = $dd['acl']['allowed_users'];
        }

        $allowedGroups = isset($dd['acl']['allowed_groups']) ? $dd['acl']['allowed_groups'] : null;
        if (isset($allowedGroups) && (is_array($allowedGroups) || $allowedGroups === false)) {
            DvConfig::$dv_conf['acl']['allowed_groups'] = $dd['acl']['allowed_groups'];
        }

        $usersAllowed = DvConfig::$dv_conf['acl']['allowed_users'];
        $groupsAllowed = DvConfig::$dv_conf['acl']['allowed_groups'];
        if ($usersAllowed === false && $groupsAllowed === false || isset($dd['acl']['public'])) {
            return true;
        } elseif (\User::isGuest()) {
            $redir_url = '?return=' . base64_encode($_SERVER['REQUEST_URI']);
            $login_url = '/login';
            $url = $login_url . $redir_url;
            header('Location: ' . $url);
            return;
        }

        if (!\User::isGuest() && isset($dd['acl']['registered'])) {
            return true;
        }

        if ($usersAllowed !== false && $usersAllowed == 'registered' && !\User::isGuest()) {
            return true;
        } elseif (isset($usersAllowed) && is_array($usersAllowed) && !\User::isGuest()) {
            if (in_array(\User::get('username'), DvConfig::$dv_conf['acl']['allowed_users'])) {
                return true;
            }
        }

        if ($groupsAllowed !== false && is_array($groupsAllowed) && !\User::isGuest()) {
            $groups = \Hubzero\User\Helper::getGroups(\User::get('id'));
            if ($groups && count($groups)) {
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
