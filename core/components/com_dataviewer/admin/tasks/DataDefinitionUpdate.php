<?php

/**
 * Update a data definition for the admin Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;
use Components\Dataviewer\Admin\Helpers\GitHelper;

class DataDefinitionUpdate
{
    public static function execute()
    {
        \Components\Dataviewer\Admin\Libs\Security::checkRid();
        $base = DvConfig::$conf['dir_base'];

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $ddName = \Hubzero\Facades\Request::getString('dd', false);
        $ddText = \Hubzero\Facades\Request::getString('dd_text', '');

        $author = GitHelper::getAuthor();

        // Write PHP data definition
        $ddFilePhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/' . $ddName . '.php';
        file_put_contents($ddFilePhp, $ddText);

        $phpDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/';
        GitHelper::commit(
            $phpDir,
            $ddName . '.php',
            "[UPDATE] $ddName.php.",
            $author
        );

        // Convert PHP to JSON
        $ddFileJson = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/' . $ddName . '.json';
        self::convertPhpToJson($ddFilePhp, $ddFileJson);

        $jsonDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/';
        GitHelper::commit(
            $jsonDir,
            $ddName . '.json',
            "[UPDATE] $ddName.json.",
            $author
        );

        $url = '/administrator/index.php?option=com_'
            . urlencode(DvConfig::$com_name)
            . '&task=data_definition&db=' . urlencode($dbId)
            . '&dd=' . urlencode($ddName);
        \Hubzero\Facades\App::redirect($url);
    }

    /**
     * Convert a PHP data definition to JSON using ddconvert.php.
     *
     * @param   string  $phpFile   Input PHP file path
     * @param   string  $jsonFile  Output JSON file path
     * @return  bool
     */
    private static function convertPhpToJson(
        string $phpFile,
        string $jsonFile
    ): bool {
        $script = dirname(__DIR__) . '/ddconvert.php';
        $cmd = 'php '
            . escapeshellarg($script)
            . ' -i' . escapeshellarg($phpFile)
            . ' -o' . escapeshellarg($jsonFile);
        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        return $returnCode === 0;
    }
}
