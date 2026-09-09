<?php

/**
 * Dataview list task for the admin Dataviewer component.
 *
 * Lists all data definitions for a database with links to
 * view, edit, and remove.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Tasks;

use Components\Dataviewer\Admin\DvConfig;
use Components\Dataviewer\Admin\Helpers\GitHelper;

class DataviewList
{
    public static function execute()
    {
        $base = DvConfig::$conf['dir_base'];

        $document = \Hubzero\Facades\App::get('document');
        $document->addScript(DB_PATH . DS . 'html' . DS . 'ace/ace.js');

        $dbId = \Hubzero\Facades\Request::getString('db', false);
        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        $jdb = \Hubzero\Database\Driver::getInstance($dbConf['database_ro']);

        \Hubzero\Facades\Toolbar::title(
            $dbConf['name'] . ' >> <small> The list of Dataviews</small>',
            'databases'
        );

        if (!$jdb->getErrorMsg()) {
            \Hubzero\Facades\Toolbar::custom(
                'new', 'new', 'new', 'New Dataview', false
            );
        }

        \Hubzero\Facades\Toolbar::custom(
            'back', 'back', 'back', 'Go back', false
        );

        $path = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/';
        $pathPhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/';

        // Create directories using PHP instead of shell mkdir
        if (!file_exists($path)) {
            GitHelper::initRepo($path);
        }
        if (!file_exists($pathPhp)) {
            GitHelper::initRepo($pathPhp);
        }

        $files = [];
        if (is_dir($pathPhp)) {
            $files = scandir($pathPhp);
        }

        $backLink = "/administrator/index.php?option=com_databases";

        \Components\Dataviewer\Admin\Libs\Messages::dbShowMsg();
        ?>

        <script>
            var com_name = '<?php echo htmlspecialchars(DvConfig::$com_name); ?>';
            var db_back_link = '<?php echo htmlspecialchars($backLink); ?>';
        </script>
        <style type="text/css"> .toolbar-box .header:before {content: " ";}</style>

        <table class="adminlist" summary="">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="55%">Title</th>
                    <th>Remove</th>
                    <th>Last Updated</th>
                    <th>Data View</th>
                    <th>Data Definition</th>
                </tr>
            </thead>

            <tbody>
        <?php

        if (count($files) < 1) {
            print "<h2>No Dataviews available</h2>";
        } else {
            asort($files);
            $c = 0;
            foreach ($files as $file) {
                if (substr($file, -4) === '.php') {
                    $ddName = substr($file, 0, -4);

                    $jsonFile = $path . DS . $ddName . '.json';
                    $phpFile = $pathPhp . DS . $ddName . '.php';

                    // Create JSON data definition if unavailable
                    if (!file_exists($jsonFile)) {
                        self::convertPhpToJson($phpFile, $jsonFile);

                        $author = GitHelper::getAuthor();
                        GitHelper::addAndCommit(
                            $path,
                            $ddName . '.json',
                            "[ADD] $ddName.json Initial commit.",
                            $author
                        );
                    }

                    $dd = json_decode(file_get_contents($jsonFile), true);
                    $lastMod = date("Y-m-d H:i:s", filemtime($phpFile));

                    $viewLink = '/' . htmlspecialchars(DvConfig::$com_name)
                        . "/view/" . htmlspecialchars($dbId) . ":db/"
                        . htmlspecialchars($ddName) . '/';
                    $editLink = '/administrator/index.php?option=com_dataviewer'
                        . '&task=data_definition&db='
                        . urlencode($dbId) . '&dd=' . urlencode($ddName);
                    $fsLink = '/administrator/index.php?option=com_dataviewer'
                        . '&tmpl=component&task=data_definition&db='
                        . urlencode($dbId) . '&dd=' . urlencode($ddName);

                    print '<tr>';
                    print '<td>' . ++$c . '</td>';
                    print '<td>' . htmlspecialchars($dd['title'])
                        . ' &nbsp;<small>[' . htmlspecialchars($ddName)
                        . ']</small></td>';
                    print '<td><a class="db-dd-remove-link" style="color: red;" '
                        . 'data-dd="' . htmlspecialchars($ddName)
                        . '" href="#">Remove</a></td>';
                    print '<td>' . $lastMod . '</td>';
                    print '<td align="center"><a target="_blank" href="'
                        . $viewLink . '">View</a></td>';
                    print '<td><a href="' . $editLink . '">Edit &nbsp; </a>'
                        . '&nbsp;[<a target="_blank" href="' . $fsLink
                        . '">Full Screen</a>]</td>';
                    print '</tr>';
                }
            }
        }

        ?>
            </tbody>
        </table>

        <?php

        if (get_class($jdb) === 'JException' || $jdb->getErrorMsg()) {
            print "<h3>Invalid Database connection information</h3>";
            return;
        } else {
            $sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '
                . $jdb->quote($dbConf['database_ro']['database'])
                . ' GROUP BY TABLE_NAME ORDER BY TABLE_NAME';
            $jdb->setQuery($sql);
            $list = $jdb->loadAssocList();
        }

        ?>

        <!-- Remove Table form -->
        <?php
        $removeAction = '/administrator/index.php?option=com_'
            . htmlspecialchars(DvConfig::$com_name)
            . '&task=data_definition_remove';
        ?>
        <form id="db-dd-remove-frm" method="post"
            action="<?php echo $removeAction; ?>" style="display: none;">
                <input name="<?php echo DB_RID; ?>" type="hidden"
                    value="<?php echo DB_RID; ?>" />
                <input name="db" type="hidden"
                    value="<?php echo htmlspecialchars($dbId); ?>" />
                <input name="dd_name" type="hidden">
        </form>

        <?php
        $newAction = '/administrator/index.php?option=com_'
            . htmlspecialchars(DvConfig::$com_name)
            . '&task=data_definition_new';
        $dialogTitle = htmlspecialchars($dbConf['name'])
            . ' Database : Add new Dataview';
        ?>
        <div id="db-dd-new" style="display: none;"
            title="<?php echo $dialogTitle; ?>">
            <form method="post" action="<?php echo $newAction; ?>">
                <input name="<?php echo DB_RID; ?>" type="hidden"
                    value="<?php echo DB_RID; ?>" />
                <input name="db" type="hidden"
                    value="<?php echo htmlspecialchars($dbId); ?>" />
                <label for="table">Select Table:</label>
                <br />
                <select name="table" id="table">
                <?php
                foreach ($list as $table) {
                    $tName = htmlspecialchars($table['TABLE_NAME']);
                    print "<option value=\"$tName\">$tName</option>";
                }
                ?>
                </select>

                <br />
                <label for="name">Name:</label>
                <br />
                <input type="text" id="name" name="name" />

                <br />
                <label for="title">Title:</label>
                <br />
                <input type="text" id="title" name="title" />

                <input type="submit" value="Create" />
            </form>
        </div>
        <?php
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
