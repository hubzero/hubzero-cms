<?php

/**
 * Update the Dataviewer configuration file for a database.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $dvConfText = \Hubzero\Facades\Request::getString('conf_text', false);

        $dvConfFile = $base . DS . $dbId
            . DS . 'applications/dataviewer/config.json';
        file_put_contents($dvConfFile, $dvConfText);

        \Hubzero\Facades\Session::set('dv.admin.conf_updated', true);

        $url = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$conf['com_name'])
            . '&task=config&db=' . urlencode($dbId);
        \Hubzero\Facades\App::redirect($url);
    }
}
