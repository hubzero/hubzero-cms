<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;
use Components\Dataviewer\Site\DvConfig as SiteDvConfig;

class ConfigCurrent
{
    public static function execute()
    {
        $base = DvConfig::$conf['dir_base'];
        $db_id = \Hubzero\Facades\Request::getString('db', false);

        \Components\Dataviewer\Site\DvConfig::init();

        $dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';

        $db_dv_conf = array();
        if (file_exists($dv_conf_file)) {
            $db_dv_conf = json_decode(file_get_contents($dv_conf_file), true);
            if (!is_array($db_dv_conf)) {
                $db_dv_conf = array();
            } if (isset($db_dv_conf['settings'])) {
                $db_dv_conf['settings'] = array_merge(SiteDvConfig::$dv_conf['settings'], $db_dv_conf['settings']);
            }
        }

        SiteDvConfig::$dv_conf = array_merge(SiteDvConfig::$dv_conf, $db_dv_conf);

        print \Components\Dataviewer\Admin\Libs\JsonFormat::jsonFormat(json_encode(SiteDvConfig::$dv_conf));
        exit;
    }
}
