<?php

/**
 * Session-based notification helper for the admin Dataviewer.
 *
 * Uses the Hubzero Session facade instead of raw $_SESSION.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Libs;

use Hubzero\Facades\Session;

class Messages
{
    public static function dbMsg($msg, $type = 'error')
    {
        $list = Session::get('dv.admin.notifications', []);
        $list[] = ['message' => $msg, 'type' => $type];
        Session::set('dv.admin.notifications', $list);
    }

    public static function dbShowMsg()
    {
        $list = Session::get('dv.admin.notifications', []);
        foreach ($list as $notification) {
            print '<p class="' . htmlspecialchars($notification['type'])
                . '">' . $notification['message'] . '</p>';
        }
        Session::set('dv.admin.notifications', []);
    }
}
