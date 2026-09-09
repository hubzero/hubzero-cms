<?php

/**
 * Admin-side configuration for the Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin;

class DvConfig
{
    public static $conf;
    public static $com_name = '';

    public static function init()
    {
        // CSRF token via framework Session facade
        $rid = \Hubzero\Facades\Session::get('dv.admin.rid', '');
        if ($rid === '') {
            $rid = sha1(uniqid('__rid', true));
            \Hubzero\Facades\Session::set('dv.admin.rid', $rid);
        }

        if (!defined('DB_RID')) {
            define('DB_RID', $rid);
        }

        static::$com_name = \Hubzero\Facades\Request::get('option');
        static::$com_name = str_replace('com_', '', static::$com_name);

        $comPath = str_replace(PATH_ROOT, '', __DIR__);

        if (!defined('DB_COM')) {
            define('DB_COM', static::$com_name);
        }
        if (!defined('DB_PATH')) {
            define('DB_PATH', $comPath);
        }

        static::$conf['com_name'] = static::$com_name;
        static::$conf['com_path'] = $comPath;
        static::$conf['app_title'] = 'Dataviewer';

        // Base directory
        $dbParams = \Hubzero\Facades\Component::params('com_databases');
        static::$conf['dir_base'] = $dbParams->get('base_dir');
        if (
            static::$conf['dir_base'] == null
            || static::$conf['dir_base'] == ''
        ) {
            static::$conf['dir_base'] = '/db/databases';
        }

        $modeDbEnabled = \Hubzero\Facades\Component::params('com_dataviewer')
            ->get('mode_db') == '1';
        static::$conf['modes']['db'] = ['enabled' => $modeDbEnabled];

        // ACL
        static::$conf['access_limit_to_group'] = false;
        if (static::$conf['modes']['db']['enabled']) {
            $group = $dbParams->get('access_limit_to_group');
            if ($group != '') {
                static::$conf['access_limit_to_group'] = $group;
            }
        }

        // Ensure files are not accessible by other
        static::$conf['sys_umask'] = umask(0007);
    }
}
