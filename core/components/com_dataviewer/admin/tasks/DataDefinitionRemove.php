<?php

/**
 * Remove a data definition for the admin Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;
use Components\Dataviewer\Admin\Helpers\GitHelper;

class DataDefinitionRemove
{
    public static function execute()
    {
        \Components\Dataviewer\Admin\Libs\Security::checkRid();
        $base = DvConfig::$conf['dir_base'];

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $ddName = \Hubzero\Facades\Request::getString('dd_name', false);

        $author = GitHelper::getAuthor();

        // Remove PHP data definition
        $ddFilePhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/' . $ddName . '.php';
        if (file_exists($ddFilePhp)) {
            unlink($ddFilePhp);
        }

        $phpDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/';
        GitHelper::commit(
            $phpDir,
            $ddName . '.php',
            "[DELETE] $ddName.php.",
            $author
        );

        // Remove JSON data definition
        $ddFileJson = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/' . $ddName . '.json';
        if (file_exists($ddFileJson)) {
            unlink($ddFileJson);
        }

        $jsonDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/';
        GitHelper::commit(
            $jsonDir,
            $ddName . '.json',
            "[DELETE] $ddName.json.",
            $author
        );

        \Components\Dataviewer\Admin\Libs\Messages::dbMsg(
            'Dataview successfully removed',
            'message'
        );

        $url = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$com_name)
            . '&task=dataview_list&db=' . urlencode($dbId);
        \Hubzero\Facades\App::redirect($url);
    }
}
