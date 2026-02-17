<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;

class DataDefinitionRemove
{
    public static function execute()
    {
        \Components\Dataviewer\Admin\Libs\Security::checkRid();
        $base = DvConfig::$conf['dir_base'];

        $db_id = \Request::getString('db', false);
        $dd_name = \Request::getString('dd_name', false);

        $author = \User::get('name') . ' <' . \User::get('email') . '>';


        $dd_file_php = $base . '/' . $db_id . '/applications/'
            . DvConfig::$com_name . "/datadefinitions-php/$dd_name.php";
        system("rm $dd_file_php");
        $phpDir = $base . '/' . $db_id . '/applications/' . DvConfig::$com_name . '/datadefinitions-php/';
        $cmd = "cd $phpDir; git commit $dd_name.php --author=\"$author\" "
            . "-m\"[DELETE] $dd_name.php.\"  > /dev/null";
        system($cmd);

        $dd_file_json = $base . '/' . $db_id . '/applications/'
            . DvConfig::$com_name . "/datadefinitions/$dd_name.json";
        system("rm $dd_file_json");
        $jsonDir = $base . '/' . $db_id . '/applications/' . DvConfig::$com_name . '/datadefinitions/';
        $cmd = "cd $jsonDir; git commit $dd_name.json --author=\"$author\" "
            . "-m\"[DELETE] $dd_name.json.\"  > /dev/null";
        system($cmd);

        \Components\Dataviewer\Admin\Libs\Messages::dbMsg('Dataview successfully removed', 'message');
        $url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI']);
        $url .= "/administrator/index.php?option=com_" . DvConfig::$com_name . "&task=dataview_list&db=$db_id";
        header("Location: $url");
        exit();
    }
}
