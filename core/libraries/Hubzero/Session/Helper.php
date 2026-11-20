<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Session;

use Hubzero\Facades\App;
use Hubzero\Facades\Config;

/**
 * Session helper
 */
class Helper
{
    /**
     * Get Session storage class
     *
     * @return  object
     */
    public static function storage()
    {
        // get storage handler (from config)
        $storageHandler = Config::get('session_handler');

        // create storage class
        $storageClass = __NAMESPACE__ . '\\Storage\\' . ucfirst($storageHandler);

        // return new instance of storage class
        return new $storageClass();
    }

    /**
     * Get Session by id
     *
     * @param   int|string  $id  Session ID
     * @return  object|null  Null when there is no such session
     */
    public static function getSession($id)
    {
        return App::get('session')->getStore()->session($id);
    }

    /**
     * Get Session by User Id
     *
     * @param   integer  $userid  User ID
     * @return  mixed
     */
    public static function getSessionWithUserId($userid)
    {
        // get list of all sessions
        $sessions = App::get('session')->getStore()->all([
            'guest'    => 0,
            'distinct' => 1,
        ]);

        // see if any session matches our userid
        foreach ($sessions as $session) {
            if ($session->userid == $userid) {
                return $session;
            }
        }

        // nothing found
        return null;
    }

    /**
     * Get list of all sessions
     *
     * @param   array<mixed>  $filters  Filters to apply
     * @return  array<mixed>
     */
    public static function getAllSessions($filters = [])
    {
        return App::get('session')->getStore()->all($filters);
    }
}
