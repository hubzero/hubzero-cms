<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Libs;

class Messages
{
    /** @return void */
    public static function init()
    {
        if (!isset($_SESSION['databases']['notifications'])) {
            $_SESSION['databases']['notifications'] = [];
        }
    }

    /**
     * @param   string  $msg
     * @param   string  $type
     * @return void
     */
    public static function dbMsg($msg, $type = 'error')
    {
        self::init();
        $_SESSION['databases']['notifications'][] = ['message' => $msg, 'type' => $type];
    }

    /** @return void */
    public static function dbShowMsg()
    {
        self::init();
        foreach ($_SESSION['databases']['notifications'] as $notification) {
            print "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
        }

        $_SESSION['databases']['notifications'] = [];
    }
}
