<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Libs;

class Messages
{
    public static function init()
    {
        if (!isset($_SESSION['databases']['notifications'])) {
            $_SESSION['databases']['notifications'] = array();
        }
    }

    public static function dbMsg($msg, $type = 'error')
    {
        self::init();
        $_SESSION['databases']['notifications'][] = array('message' => $msg, 'type' => $type);
    }

    public static function dbShowMsg()
    {
        self::init();
        foreach ($_SESSION['databases']['notifications'] as $notification) {
            print "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
        }

        $_SESSION['databases']['notifications'] = array();
    }
}
