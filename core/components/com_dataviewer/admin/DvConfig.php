<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin;

use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Request;

class DvConfig
{
    public static $conf;
    /**
     * @var  string
     */
    public static $com_name = '';

    /** @var string  Session-based request ID sent with each form, also the constant DB_RID */
    public static $rid = '';

    /** @var string  Component path relative to the document root, also the constant DB_PATH */
    public static $com_path = '';

    public static function init()
    {
        // Request ID/CSRF prevention
        if (!isset($_SESSION['db'])) {
            $_SESSION['db'] = [];
        }

        if (!isset($_SESSION['db']['__rid'])) {
            $_SESSION['db']['__rid'] = sha1(uniqid('__rid', true));
        }
        static::$rid = $_SESSION['db']['__rid'];
        define('DB_RID', static::$rid);

        $document = App::get('document');
        static::$com_name = Request::get('option');
        static::$com_name = str_replace('com_', '', static::$com_name);

        $com_path = str_replace(PATH_ROOT, '', __DIR__);


        /* Paths */
        define('DB_COM', static::$com_name);
        static::$com_path = $com_path;
        define('DB_PATH', $com_path);


        static::$conf['com_name'] = static::$com_name;
        static::$conf['com_path'] = $com_path;
        static::$conf['app_title'] = 'Dataviewer';

        // Base directory
        $db_params = Component::params('com_databases');
        static::$conf['dir_base'] = $db_params->get('base_dir');
        if (static::$conf['dir_base'] == null || static::$conf['dir_base'] == '') {
            static::$conf['dir_base'] = '/db/databases';
        }

        $mode_db_enabled = Component::params('com_dataviewer')->get('mode_db') == '1' ? true : false;
        static::$conf['modes']['db'] = ['enabled' => $mode_db_enabled];

        // ACL
        static::$conf['access_limit_to_group'] = false;
        if (static::$conf['modes']['db']['enabled']) {
            if ($db_params->get('access_limit_to_group') != '') {
                static::$conf['access_limit_to_group'] = $db_params->get('access_limit_to_group');
            }
        }


        // Makesure the files are not accessible by other
        static::$conf['sys_umask'] = umask(0007);
    }
}
