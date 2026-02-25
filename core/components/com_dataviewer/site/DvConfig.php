<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

class DvConfig
{
    public static $dv_conf = [];
    public static $com_name = '';
    public static $html_path = '';

    public static function init()
    {
        static::$html_path = str_replace(PATH_ROOT, '', __DIR__) . '/html';
        static::$com_name = basename(dirname(__DIR__));
        static::$com_name = str_replace('com_', '', static::$com_name);
        static::$com_name = trim(static::$com_name, DS);
        $com_path = str_replace(PATH_ROOT, '', __DIR__);

        static::$dv_conf['settings']['com_name'] = static::$com_name;

        define('DV_COM', static::$com_name);
        define('DV_COM_PATH', $com_path);
        define('DV_COM_HTML', DV_COM_PATH . DS . 'html');
        define('DV_PATH_HTML', __DIR__ . DS . 'html');

        $params = \Hubzero\Facades\Component::params('com_dataviewer');

        $rowOptions = array(5, 10, 25, 50, 100);
        static::$dv_conf['settings']['num_rows'] = array(
            'labels' => $rowOptions,
            'values' => $rowOptions,
        );
        $displayLimit = $params->get('record_display_limit');
        static::$dv_conf['settings']['limit'] = $displayLimit == '' ? 10 : $displayLimit;
        static::$dv_conf['settings']['serverside'] = false;

        /* Processing mode switching*/
        static::$dv_conf['proc_mode_switch'] = ($params->get('processing_mode_switch') == '1') ? true : false;
        static::$dv_conf['proc_switch_threshold'] = intval($params->get('proc_switch_threshold'));


        static::$dv_conf['null_desc'] = $params->get('null_desc');
        static::$dv_conf['help_file_base_path'] = '';
        static::$dv_conf['db'] = array();


        /* Access Control */
        $acl_users = $params->get('acl_users');
        if ($acl_users == 'registered') {
            static::$dv_conf['acl']['allowed_users'] = 'registered';
        } elseif ($acl_users != 'registered' && $acl_users != '') {
            static::$dv_conf['acl']['allowed_users'] = array_map('trim', explode(',', $acl_users));
        } else {
            static::$dv_conf['acl']['allowed_users'] = false;
        }

        $acl_groups = $params->get('acl_groups');
        if ($acl_groups != '') {
            static::$dv_conf['acl']['allowed_groups'] = array_map('trim', explode(',', $acl_groups));
        } else {
            static::$dv_conf['acl']['allowed_groups'] = false;
        }
    }
}
