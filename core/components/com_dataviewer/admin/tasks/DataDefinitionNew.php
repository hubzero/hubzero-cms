<?php

/**
 * Create a new data definition for the admin Dataviewer component.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;
use Components\Dataviewer\Admin\Helpers\GitHelper;

class DataDefinitionNew
{
    public static function execute()
    {
        $base = DvConfig::$conf['dir_base'];

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $table = \Hubzero\Facades\Request::getString('table', false);
        $name = \Hubzero\Facades\Request::getString('name', false);
        $title = \Hubzero\Facades\Request::getString('title', false);

        $name = strtolower(preg_replace('/\W/', '_', $name));

        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        $jdb = \Hubzero\Database\Driver::getInstance($dbConf['database_ro']);

        $dd = [];
        $dd['table'] = $table;
        $dd['title'] = $title;

        $sql = 'SHOW COLUMNS FROM ' . $jdb->quoteName($table);
        $jdb->setQuery($sql);
        $cols = $jdb->loadAssocList();

        $pk = '';

        foreach ($cols as $col) {
            if ($col['Key'] == 'PRI') {
                $pk = $dd['table'] . '.' . $col['Field'];
            }

            $colKey = $dd['table'] . '.' . $col['Field'];
            $label = ucwords(str_replace('_', ' ', $col['Field']));
            $dd['cols'][$colKey] = ['label' => $label];
        }

        $ddText = "<?php\ndefined('_HZEXEC_') or die();\n\n";
        $ddText .= "function get_$name()\n{\n";
        $ddText .= "\t" . '$dd[\'title\'] = \''
            . addcslashes($title, "'\\") . '\';' . "\n";
        $ddText .= "\t" . '$dd[\'table\'] = \''
            . addcslashes($dd['table'], "'\\") . '\';' . "\n";
        $ddText .= "\t" . '$dd[\'pk\'] = \''
            . addcslashes($pk, "'\\") . '\';' . "\n\n";

        foreach ($dd['cols'] as $col => $val) {
            $ddText .= "\t" . '$dd[\'cols\'][\'' . $col . '\'] = '
                . self::formatVar(var_export($val, true)) . "\n";
        }

        $ddText .= "\n\t" . 'return $dd;' . "\n\n}\n?>";

        // Create directories using GitHelper instead of shell mkdir/git
        $phpDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/';
        if (!file_exists($phpDir)) {
            GitHelper::initRepo($phpDir);
        }

        $jsonDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/';
        if (!file_exists($jsonDir)) {
            GitHelper::initRepo($jsonDir);
        }

        $ddName = $name;
        $author = GitHelper::getAuthor();

        // Write PHP data definition
        $ddFilePhp = $phpDir . $ddName . '.php';
        file_put_contents($ddFilePhp, $ddText);

        GitHelper::addAndCommit(
            $phpDir,
            $ddName . '.php',
            "[ADD] $ddName.php Initial commit.",
            $author
        );

        // Convert PHP to JSON
        $ddFileJson = $jsonDir . $ddName . '.json';
        self::convertPhpToJson($ddFilePhp, $ddFileJson);

        GitHelper::addAndCommit(
            $jsonDir,
            $ddName . '.json',
            "[ADD] $ddName.json Initial commit.",
            $author
        );

        \Components\Dataviewer\Admin\Libs\Messages::dbMsg(
            'New Dataview Added',
            'message'
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

    public static function formatVar($var)
    {
        $var = str_replace("\n", '', $var);
        $var = str_replace(' (  ', '(', $var);
        $var = str_replace(',)', ');', $var);
        return str_replace(' => ', '=>', $var);
    }
}
