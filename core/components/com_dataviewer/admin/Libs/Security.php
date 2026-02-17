<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Libs;

class Security
{
    public static function checkRid()
    {
        if (isset($_POST[DB_RID]) && $_POST[DB_RID] == DB_RID) {
            return true;
        }

        exit;
    }
}
