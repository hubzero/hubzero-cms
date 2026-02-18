<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;

class ConfigUpdate
{
    public static function execute()
    {
        \Components\Dataviewer\Admin\Libs\Security::checkRid();
        $base = DvConfig::$conf['dir_base'];

        $db_id = \Request::getString('db', false);
        $dv_conf_text = \Request::getString('conf_text', false);

        $dv_conf_file = $base . DS . $db_id . DS . 'applications/dataviewer/config.json';
        file_put_contents($dv_conf_file, $dv_conf_text);

        $_SESSION['dataviewer']['conf_file_updated'] = true;

        $url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
        $url .= "/administrator/index.php?option=com_" . DvConfig::$conf['com_name'] . "&task=config&db=$db_id";
        header("Location: $url");
        exit;
    }
}
