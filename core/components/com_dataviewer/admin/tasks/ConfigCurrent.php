<?php

/**
 * Return the current merged Dataviewer configuration as JSON.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        $dbId = \Hubzero\Facades\Request::getString('db', false);

        SiteDvConfig::init();

        $dvConfFile = $base . DS . $dbId
            . DS . 'applications/dataviewer/config.json';

        $dbDvConf = [];
        if (file_exists($dvConfFile)) {
            $dbDvConf = json_decode(file_get_contents($dvConfFile), true);
            if (!is_array($dbDvConf)) {
                $dbDvConf = [];
            }
            if (isset($dbDvConf['settings'])) {
                $dbDvConf['settings'] = array_merge(
                    SiteDvConfig::$dv_conf['settings'],
                    $dbDvConf['settings']
                );
            }
        }

        SiteDvConfig::$dv_conf = array_merge(
            SiteDvConfig::$dv_conf,
            $dbDvConf
        );

        header('Content-Type: application/json; charset=utf-8');
        print \Components\Dataviewer\Admin\Libs\JsonFormat::jsonFormat(
            json_encode(SiteDvConfig::$dv_conf)
        );
        \Hubzero\Facades\App::close();
    }
}
