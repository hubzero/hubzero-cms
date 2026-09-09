<?php

/**
 * Admin dataviews controller for the Dataviewer component.
 *
 * Handles listing, creating, editing, updating, and removing
 * data definitions for a database.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Controllers;

use Components\Dataviewer\Admin\DvConfig;
use Components\Dataviewer\Admin\Helpers\GitHelper;
use Hubzero\Component\AdminController;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

class Dataviews extends AdminController
{
    /**
     * Execute the controller.
     *
     * @return  void
     */
    public function execute()
    {
        $this->registerTask('data_definition', 'edit');
        $this->registerTask('data_definition_new', 'create');
        $this->registerTask('data_definition_update', 'save');
        $this->registerTask('data_definition_remove', 'remove');
        $this->registerTask('dataview_list', 'display');

        parent::execute();
    }

    /**
     * List all data definitions for a database (default task).
     *
     * @return  void
     */
    public function displayTask()
    {
        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');

        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        $jdb = \Hubzero\Database\Driver::getInstance(
            $dbConf['database_ro']
        );

        Toolbar::title(
            htmlspecialchars($dbConf['name']) . ' >> <small>'
                . Lang::txt('COM_DATAVIEWER_DATAVIEW_LIST')
                . '</small>',
            'databases'
        );

        $dbError = false;
        if (
            is_object($jdb) && method_exists($jdb, 'getErrorMsg')
            && $jdb->getErrorMsg()
        ) {
            $dbError = true;
        }

        if (!$dbError) {
            Toolbar::custom(
                'new',
                'new',
                'new',
                Lang::txt('COM_DATAVIEWER_NEW_DATAVIEW'),
                false
            );
        }

        Toolbar::custom(
            'back',
            'back',
            'back',
            Lang::txt('COM_DATAVIEWER_GO_BACK'),
            false
        );

        // Ensure directories exist
        $path = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/';
        $pathPhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/';

        if (!file_exists($path)) {
            GitHelper::initRepo($path);
        }
        if (!file_exists($pathPhp)) {
            GitHelper::initRepo($pathPhp);
        }

        // Scan for PHP definition files
        $files = [];
        if (is_dir($pathPhp)) {
            $files = scandir($pathPhp);
        }

        $definitions = [];
        if (count($files) > 0) {
            asort($files);
            foreach ($files as $file) {
                if (substr($file, -4) !== '.php') {
                    continue;
                }

                $ddName = substr($file, 0, -4);
                $jsonFile = $path . DS . $ddName . '.json';
                $phpFile = $pathPhp . DS . $ddName . '.php';

                // Auto-convert PHP to JSON if needed
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

                $dd = json_decode(
                    file_get_contents($jsonFile),
                    true
                );
                $definitions[] = [
                    'name'     => $ddName,
                    'title'    => $dd['title'] ?? $ddName,
                    'lastMod'  => date(
                        'Y-m-d H:i:s',
                        filemtime($phpFile)
                    ),
                ];
            }
        }

        // Get table list for "new" dialog
        $tableList = [];
        if (!$dbError) {
            $sql = 'SELECT * FROM INFORMATION_SCHEMA.TABLES'
                . ' WHERE TABLE_SCHEMA = '
                . $jdb->quote($dbConf['database_ro']['database'])
                . ' GROUP BY TABLE_NAME ORDER BY TABLE_NAME';
            $jdb->setQuery($sql);
            $tableList = $jdb->loadAssocList();
        }

        // Add ACE editor and dataview_list task JS
        $document = App::get('document');
        $document->addScript(
            DvConfig::$conf['com_path'] . DS . 'html' . DS . 'ace/ace.js'
        );
        $document->addScript(
            DvConfig::$conf['com_path'] . DS . 'tasks' . DS . 'html'
            . DS . 'dataview_list.js?v=2'
        );

        $this->view
            ->set('dbId', $dbId)
            ->set('dbConf', $dbConf)
            ->set('definitions', $definitions)
            ->set('tableList', $tableList)
            ->set('dbError', $dbError)
            ->set('comName', DvConfig::$com_name)
            ->set('csrfToken', Session::getFormToken())
            ->display();
    }

    /**
     * Edit a data definition.
     *
     * @return  void
     */
    public function editTask()
    {
        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');
        $ddName = Request::getString('dd', '');
        $fullScreen = Request::getString('tmpl', false);

        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        $ddFile = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/'
            . $ddName . '.json';
        $ddJson = file_get_contents($ddFile);
        $dd = json_decode($ddJson, true);

        Toolbar::title(
            htmlspecialchars($dbConf['name']) . ' >> <small>'
                . htmlspecialchars($dd['title']) . '</small>',
            'databases'
        );
        Toolbar::custom(
            'back',
            'back',
            'back',
            Lang::txt('COM_DATAVIEWER_GO_BACK'),
            false
        );

        $ddFilePhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/'
            . $ddName . '.php';
        $ddPhp = file_get_contents($ddFilePhp);

        // Add ACE editor and data_definition task JS
        $document = App::get('document');
        $document->addScript(
            DvConfig::$conf['com_path'] . DS . 'html' . DS . 'ace/ace.js'
        );
        $document->addScript(
            DvConfig::$conf['com_path'] . DS . 'tasks' . DS . 'html'
            . DS . 'data_definition.js?v=2'
        );

        $this->view
            ->set('dbId', $dbId)
            ->set('dbConf', $dbConf)
            ->set('ddName', $ddName)
            ->set('dd', $dd)
            ->set('ddJson', $ddJson)
            ->set('ddPhp', $ddPhp)
            ->set('fullScreen', $fullScreen)
            ->set('comName', DvConfig::$com_name)
            ->set('csrfToken', Session::getFormToken())
            ->setLayout('edit')
            ->display();
    }

    /**
     * Create a new data definition (POST).
     *
     * @return  void
     */
    public function createTask()
    {
        Session::checkToken();

        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');
        $table = Request::getString('table', '');
        $name = Request::getString('name', '');
        $title = Request::getString('title', '');

        $name = strtolower(preg_replace('/\W/', '_', $name));

        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        $jdb = \Hubzero\Database\Driver::getInstance(
            $dbConf['database_ro']
        );

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

        // Ensure directories exist
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

        $author = GitHelper::getAuthor();

        // Write PHP data definition
        $ddFilePhp = $phpDir . $name . '.php';
        file_put_contents($ddFilePhp, $ddText);

        GitHelper::addAndCommit(
            $phpDir,
            $name . '.php',
            "[ADD] $name.php Initial commit.",
            $author
        );

        // Convert PHP to JSON
        $ddFileJson = $jsonDir . $name . '.json';
        self::convertPhpToJson($ddFilePhp, $ddFileJson);

        GitHelper::addAndCommit(
            $jsonDir,
            $name . '.json',
            "[ADD] $name.json Initial commit.",
            $author
        );

        Notify::success(
            Lang::txt('COM_DATAVIEWER_DATAVIEW_ADDED')
        );

        App::redirect(
            '/administrator/index.php?option=com_dataviewer'
            . '&controller=dataviews&task=edit&db='
            . urlencode($dbId)
            . '&dd=' . urlencode($name)
        );
    }

    /**
     * Update a data definition (POST).
     *
     * @return  void
     */
    public function saveTask()
    {
        Session::checkToken();

        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');
        $ddName = Request::getString('dd', '');
        $ddText = Request::getString('dd_text', '');

        $author = GitHelper::getAuthor();

        // Write PHP data definition
        $ddFilePhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/'
            . $ddName . '.php';
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
            . DvConfig::$com_name . '/datadefinitions/'
            . $ddName . '.json';
        self::convertPhpToJson($ddFilePhp, $ddFileJson);

        $jsonDir = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/';
        GitHelper::commit(
            $jsonDir,
            $ddName . '.json',
            "[UPDATE] $ddName.json.",
            $author
        );

        App::redirect(
            '/administrator/index.php?option=com_dataviewer'
            . '&controller=dataviews&task=edit&db='
            . urlencode($dbId)
            . '&dd=' . urlencode($ddName)
        );
    }

    /**
     * Remove a data definition (POST).
     *
     * @return  void
     */
    public function removeTask()
    {
        Session::checkToken();

        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');
        $ddName = Request::getString('dd_name', '');

        $author = GitHelper::getAuthor();

        // Remove PHP file
        $ddFilePhp = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions-php/'
            . $ddName . '.php';
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

        // Remove JSON file
        $ddFileJson = $base . '/' . $dbId . '/applications/'
            . DvConfig::$com_name . '/datadefinitions/'
            . $ddName . '.json';
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

        Notify::success(
            Lang::txt('COM_DATAVIEWER_DATAVIEW_REMOVED')
        );

        App::redirect(
            '/administrator/index.php?option=com_dataviewer'
            . '&controller=dataviews&task=display&db='
            . urlencode($dbId)
        );
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

    /**
     * Format a var_export string for compact output.
     *
     * @param   string  $var  var_export output
     * @return  string
     */
    private static function formatVar($var)
    {
        $var = str_replace("\n", '', $var);
        $var = str_replace(' (  ', '(', $var);
        $var = str_replace(',)', ');', $var);
        return str_replace(' => ', '=>', $var);
    }
}
