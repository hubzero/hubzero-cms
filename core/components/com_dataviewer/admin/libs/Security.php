<?php

/**
 * CSRF security helper for the admin Dataviewer.
 *
 * Legacy task classes still call checkRid(). New controllers
 * use Session::checkToken() directly instead.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Libs;

class Security
{
    /**
     * Check the legacy CSRF token (DB_RID).
     *
     * Falls back to checking framework session token if DB_RID
     * is not present in POST.
     *
     * @return  bool
     */
    public static function checkRid()
    {
        // Legacy check: DB_RID token in POST
        if (
            defined('DB_RID')
            && isset($_POST[DB_RID])
            && $_POST[DB_RID] == DB_RID
        ) {
            return true;
        }

        // Framework token check
        if (\Hubzero\Facades\Session::checkToken('post', false)) {
            return true;
        }

        \Hubzero\Facades\App::abort(403, 'Invalid CSRF token');
    }
}
