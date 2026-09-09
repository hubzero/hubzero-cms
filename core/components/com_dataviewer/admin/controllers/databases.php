<?php

/**
 * Admin databases controller for the Dataviewer component.
 *
 * Handles listing databases, editing config, viewing merged
 * config, and updating config files.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Controllers;

use Components\Dataviewer\Admin\DvConfig;
use Hubzero\Component\AdminController;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

class Databases extends AdminController
{
    /**
     * Execute the controller.
     *
     * @return  void
     */
    public function execute()
    {
        $this->registerTask('config_current', 'configCurrent');
        $this->registerTask('config_update', 'configUpdate');

        parent::execute();
    }

    /**
     * List all databases (default task).
     *
     * @return  void
     */
    public function displayTask()
    {
        Toolbar::title(
            Lang::txt('COM_DATAVIEWER_DATABASE_LIST'),
            'databases'
        );
        Toolbar::preferences(
            Request::getCmd('option'),
            '500'
        );

        $base = DvConfig::$conf['dir_base'];

        $configFiles = glob($base . '/*/database.json');
        if (!is_array($configFiles)) {
            $configFiles = [];
        }

        $databases = [];
        foreach ($configFiles as $configFile) {
            $id = basename(dirname($configFile));
            $db = json_decode(file_get_contents($configFile), true);
            if (!$db) {
                continue;
            }
            $databases[] = [
                'id'   => $id,
                'name' => $db['name'],
            ];
        }

        $this->view
            ->set('databases', $databases)
            ->display();
    }

    /**
     * Edit configuration for a database.
     *
     * @return  void
     */
    public function configTask()
    {
        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');

        $dbConfFile = $base . DS . $dbId . DS . 'database.json';
        $dbConf = json_decode(file_get_contents($dbConfFile), true);

        Toolbar::title(
            Lang::txt(
                'COM_DATAVIEWER_CONFIG_EDITOR_TITLE',
                htmlspecialchars($dbConf['name'])
            ),
            'databases'
        );
        Toolbar::custom(
            'back',
            'back',
            'back',
            Lang::txt('COM_DATAVIEWER_GO_BACK'),
            false
        );

        $dvConfText = '';
        $dvConfFile = $base . DS . $dbId
            . DS . 'applications/dataviewer/config.json';
        if (file_exists($dvConfFile)) {
            $dvConfText = file_get_contents($dvConfFile);
        }

        // Add ACE editor and config task JS
        $document = App::get('document');
        $document->addScript(
            DvConfig::$conf['com_path'] . DS . 'html' . DS . 'ace/ace.js'
        );
        $document->addScript(
            DvConfig::$conf['com_path'] . DS . 'tasks' . DS . 'html'
            . DS . 'config.js?v=2'
        );

        $this->view
            ->set('dbId', $dbId)
            ->set('dbConf', $dbConf)
            ->set('dvConfText', $dvConfText)
            ->set('dvConfFile', $dvConfFile)
            ->set('comName', DvConfig::$conf['com_name'])
            ->set('csrfToken', Session::getFormToken())
            ->setLayout('config')
            ->display();
    }

    /**
     * Return the current merged config as JSON (AJAX).
     *
     * @return  void
     */
    public function configCurrentTask()
    {
        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');

        \Components\Dataviewer\Site\DvConfig::init();

        $dvConfFile = $base . DS . $dbId
            . DS . 'applications/dataviewer/config.json';

        $dbDvConf = [];
        if (file_exists($dvConfFile)) {
            $dbDvConf = json_decode(
                file_get_contents($dvConfFile),
                true
            );
            if (!is_array($dbDvConf)) {
                $dbDvConf = [];
            }
            if (isset($dbDvConf['settings'])) {
                $dbDvConf['settings'] = array_merge(
                    \Components\Dataviewer\Site\DvConfig::$dv_conf['settings'],
                    $dbDvConf['settings']
                );
            }
        }

        \Components\Dataviewer\Site\DvConfig::$dv_conf = array_merge(
            \Components\Dataviewer\Site\DvConfig::$dv_conf,
            $dbDvConf
        );

        header('Content-Type: application/json; charset=utf-8');
        print \Components\Dataviewer\Admin\Libs\JsonFormat::jsonFormat(
            json_encode(
                \Components\Dataviewer\Site\DvConfig::$dv_conf
            )
        );
        App::close();
    }

    /**
     * Update the config file for a database (POST).
     *
     * @return  void
     */
    public function configUpdateTask()
    {
        Session::checkToken();

        $base = DvConfig::$conf['dir_base'];
        $dbId = Request::getString('db', '');
        $dvConfText = Request::getString('conf_text', '');

        $dvConfFile = $base . DS . $dbId
            . DS . 'applications/dataviewer/config.json';
        file_put_contents($dvConfFile, $dvConfText);

        Notify::success(
            Lang::txt('COM_DATAVIEWER_CONFIG_UPDATED')
        );

        App::redirect(
            '/administrator/index.php?option=com_dataviewer'
            . '&controller=databases&task=config&db='
            . urlencode($dbId)
        );
    }
}
