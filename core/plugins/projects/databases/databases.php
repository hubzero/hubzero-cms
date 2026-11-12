<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Projects\Databases;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Date;
use Hubzero\Facades\Document;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Exception;

/**
 * Projects - Databases plugin
 */
class Databases extends Plugin
{
    protected $area;
    protected $config;
    protected $configured;
    protected $database;
    protected $path;
    protected $uid;
    protected $gitpath;
    protected $model;
    protected $repo;
    protected $subdir;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Store output message
     *
     * @var  array
     */
    public $dataviewer = 'dataviewer';

    /**
     * Component name
     *
     * @var  string
     */
    protected $componentOption = 'com_projects';

    /**
     * Store internal message
     *
     * @var  array
     */
    protected $msg = null;

    /**
     * Event call after databases initialized
     *
     * @return  void
     */
    public function onAfterInitialise()
    {
        // Databases initialized
    }

    /**
     * Event call to determine if this plugin should return data
     *
     * @param   string  $alias
     * @return  array   Plugin name and title
     */
    public function &onProjectAreas($alias = null)
    {
        // default areas returned to nothing
        $area = [];

        // Check if plugin is restricted to certain projects
        // Check if plugin is restricted to certain projects
        $projects = $this->params->get('restricted')
            ? \Components\Projects\Helpers\Html::getParamArray($this->params->get('restricted'))
            : [];

        if (!empty($projects) && $alias) {
            if (!in_array($alias, $projects)) {
                return $area;
            }
        }

        // Hide section completely if not configured
        if ($this->checkConfig() == false) {
            return $area;
        }

        $area = [
            'name' => 'databases',
            'title' => 'Databases',
            'submenu' => 'Assets',
            'show' => true,
            'icon' => 'f001',
        ];

        return $area;
    }

    /**
     * Event call to return count of items
     *
     * @param   object  $model   Project
     * @return  array   integer
     */
    public function &onProjectCount($model)
    {
        $counts['databases'] = 0;

        // Get this area details
        $this->area = $this->onProjectAreas();
        if (empty($this->area) || !$model->exists()) {
            return $counts;
        }

        $database = App::get('db');

        $objPD = new \Components\Projects\Tables\Database($database);
        $total = $objPD->getItems($model->get('id'), ['count' => 1]);

        $counts['databases'] = $total;

        return $counts;
    }

    /**
     * Check if plugin is configured
     *
     * @return  boolean
     */
    protected function checkConfig()
    {
        if (isset($this->configured)) {
            return $this->configured;
        }

        try {
            // Check if the plugin parameters the two mysql accounts are properly set
            $db_opt_rw['driver'] = 'mysql';
            $db_opt_rw['host'] = $this->params->get('db_host');
            $db_opt_rw['user'] = $this->params->get('db_user');
            $db_opt_rw['password'] = $this->params->get('db_password');
            $db_opt_rw['prefix'] = '';
            $db_rw = \Hubzero\Database\Driver::getInstance($db_opt_rw);

            $db_opt_ro['driver'] = 'mysql';
            $db_opt_ro['host'] = $this->params->get('db_host');
            $db_opt_ro['user'] = $this->params->get('db_ro_user');
            $db_opt_ro['password'] = $this->params->get('db_ro_password');
            $db_opt_ro['prefix'] = '';
            $db_ro = \Hubzero\Database\Driver::getInstance($db_opt_ro);

            if ($db_rw->getErrorNum() > 0 || $db_ro->getErrorNum() > 0) {
                $this->configured = false;
            } else {
                $this->configured = true;
            }
        } catch (\Exception $e) {
            $this->configured = false;
        }

        return $this->configured;
    }

    /**
     * Event call to return data for a specific project
     *
     * @param   object  $model   Project model
     * @param   string  $action  Plugin task
     * @param   string  $areas   Plugins to return data
     * @return  array   Return array of html
     */
    public function onProject($model, $action = 'view', $areas = null, $params = [])
    {
        $arr = [
            'html' => '',
            'metadata' => '',
            'message' => '',
            'error' => '',
        ];

        // Get this area details
        $this->area = $this->onProjectAreas();

        // Check if our area is in the array of areas we want to return results for
        if (is_array($areas)) {
            if (empty($this->area) || !in_array($this->area['name'], $areas)) {
                return null;
            }
        }

        // Check authorization
        if ($model->exists() && !$model->access('member')) {
            return $arr;
        }

        // Model
        $this->model = $model;

        // Load component configs
        $this->config = $model->config();
        $this->gitpath = $this->config->get('gitpath', '/opt/local/bin/git');

        $repoName = !empty($params['repo']) ? $params['repo'] : Request::getString('repo', 'local');
        $this->repo = new \Components\Projects\Models\Repo($this->model, $repoName);

        $this->database = App::get('db');
        $this->uid = User::get('id');
        $this->subdir = trim(urldecode(Request::getString('subdir', '')), DS);

        if (!$this->repo->exists()) {
            // Default to local repo (will redirect to add repo page in the future)
            $this->repo = new \Components\Projects\Models\Repo($this->model, 'local');
        }
        $this->path = $this->repo->get('path');

        // Incoming
        $raw_op = Request::getInt('raw_op', 0);
        $action = $action ? $action : Request::getString('action', 'list');

        // Get this area details
        $this->area = $this->onProjectAreas();

        // Check if our area is in the array of areas we want to return results for
        if (is_array($areas)) {
            if (empty($this->area) || !in_array($this->area['name'], $areas)) {
                return $arr;
            }
        }

        $this->database = App::get('db');
        $this->uid = User::get('id');

        // Publishing?
        if ($action == 'browser') {
            return $this->browser();
        }
        if ($action == 'select') {
            return $this->select();
        }

        // An action such as preview_data is handled by actPreviewData()
        $act_func = 'act' . str_replace('_', '', ucwords(strtolower($action), '_'));

        if (!method_exists($this, $act_func)) {
            if ($raw_op) {
                // nothing was produced for an unknown action
                echo json_encode(['status' => 'success', 'data' => []]);
                exit();
            } else {
                $act_func = 'actList';
            }
        }

        if ($raw_op) {
            $this->$act_func();
            exit();
        } else {
            Document::addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/themes/smoothness/jquery-ui.css');

            Document::addScript('/core/plugins/projects/databases/res/main.js');
            Document::addStyleSheet('/core/plugins/projects/databases/res/main.css');

            if (file_exists(__DIR__ . '/res/ds.' . $action . '.js')) {
                Document::addScript('/core/plugins/projects/databases/res/ds.' . $action . '.js');
            }

            if (file_exists(__DIR__ . '/res/ds.' . $action . '.css')) {
                Document::addStyleSheet('/core/plugins/projects/databases/res/ds.' . $action . '.css');
            }

            return $this->$act_func();
        }
    }

    /**
     * Browser within publications NEW
     *
     * @return  string
     */
    public function select()
    {
        // Incoming
        $props = Request::getString('p', '');
        $ajax = Request::getInt('ajax', 0);
        $pid = Request::getInt('pid', 0);
        $vid = Request::getInt('vid', 0);
        $filter = urldecode(Request::getString('filter', ''));

        // Parse props for curation
        $parts = explode('-', $props);
        $block = isset($parts[0]) ? $parts[0] : 'content';
        $step = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
        $element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;

        // Provisioned project?
        $prov = $this->model->isProvisioned() ? 1 : 0;

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder' => 'projects',
            'element' => 'databases',
            'name' => 'selector',
            'layout' => 'default',
        ]);

        $view->set('publication', new \Components\Publications\Models\Publication($pid, null, $vid));

        // On error
        if (!$view->publication->exists()) {
            // Output error
            $view = new \Hubzero\Plugin\View([
                'folder' => 'projects',
                'element' => 'files',
                'name' => 'error',
            ]);

            $view->set('title', '');
            $view->set('option', $this->componentOption);
            $view->setError(Lang::txt('PLG_PROJECTS_DATABASES_SELECTOR_ERROR_NO_PUBID'));
            return $view->loadTemplate();
        }

        // pid= resolves any publication on the hub; only this project's are selected for
        if ((int) $view->publication->get('project_id') !== (int) $this->model->get('id')) {
            throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        $view->publication->attachments();

        // Get curation model
        $view->publication->setCuration();

        // Make sure block exists, else use default
        $view->publication->_curationModel->setBlock($block, $step);

        // Set pub assoc and load curation
        $view->publication->_curationModel->setPubAssoc($view->publication);

        \Hubzero\Document\Assets::addPluginStylesheet('projects', 'databases', 'selector');
        if (!$ajax) {
            \Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'selector');
        }

        $view->set('option', $this->componentOption);
        $view->set('database', $this->database);
        $view->set('model', $this->model);
        $view->set('uid', $this->uid);
        $view->set('ajax', $ajax);
        $view->set('element', $element);
        $view->set('block', $block);
        $view->set('step', $step);
        $view->set('props', $props);
        $view->set('filter', $filter);

        // Get databases to choose from
        $objPD = new \Components\Projects\Tables\Database($this->database);
        $view->set('items', $objPD->getItems($this->model->get('id'), []));

        // Get messages and errors
        if ($this->getError()) {
            $view->setError($this->getError());
        }

        $arr = [
            'html' => $view->loadTemplate(),
            'metadata' => '',
            'msg' => '',
            'referer' => '',
        ];

        return $arr;
    }

    /**
     * List project databases available for publishing
     *
     * @return  array|false
     */
    public function browser()
    {
        // Incoming
        $ajax = Request::getInt('ajax', 0);
        $primary = Request::getInt('primary', 1);
        $versionid = Request::getInt('versionid', 0);

        if (!$ajax) {
            return false;
        }

        // versionid= resolves any publication version on the hub; only this
        // project's publications list their database attachments here
        $pubVersion = \Components\Publications\Models\Orm\Version::oneOrNew($versionid);
        $pubParent  = \Components\Publications\Models\Orm\Publication::oneOrNew(
            (int) $pubVersion->get('publication_id'),
        );
        if (
            $pubVersion->isNew() || $pubParent->isNew()
            || (int) $pubParent->get('project_id') !== (int) $this->model->get('id')
        ) {
            throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder' => 'projects',
            'element' => 'databases',
            'name' => 'browser',
        ]);

        // Get current attachments
        $pContent = new \Components\Publications\Tables\Attachment($this->database);
        $role = $primary ? '1' : '0';
        $other = $primary ? '0' : '1';

        $view->set('attachments', $pContent->getAttachments(
            $versionid,
            $filters = ['role' => $role, 'type' => 'data'],
        ));

        // Output HTML
        $view->set('params', $this->model->params);
        $view->set('option', $this->componentOption);
        $view->set('database', $this->database);
        $view->set('model', $this->model);
        $view->set('uid', $this->uid);
        $view->set('config', $this->config);
        $view->set('title', $this->area['title']);
        $view->set('primary', $primary);
        $view->set('versionid', $versionid);

        // Get messages and errors
        if ($this->getError()) {
            $view->setError($this->getError());
        }

        $arr = [
            'html' => $view->loadTemplate(),
            'metadata' => '',
            'msg' => '',
            'referer' => '',
        ];

        return $arr;
    }

    /**
     * List project databases
     *
     * @return  array
     */
    public function actList()
    {
        // Get project path
        $path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));

        // Get project database object
        $objPD = new \Components\Projects\Tables\Database($this->database);

        // Get database list
        $list = $objPD->getList($this->model->get('id'));

        $list_u = [];
        foreach ($list as $l) {
            $info = [];

            if ($l['source_dir'] != '') {
                $file = $l['source_dir'] . DS . $l['source_file'];
            } else {
                $file = $l['source_file'];
            }

            $l['source_revision_curr'] = '';
            $l['source_revision_date'] = '';

            $l['source_available'] = file_exists($path . DS . $file) ? true : false;

            if ($l['created_by'] == $this->uid) {
                $l['name'] = 'me';
            }

            $list_u[] = $l;
        }

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder' => 'projects',
            'element' => 'databases',
            'name' => 'list',
        ]);
        $view->set('model', $this->model);
        $view->set('option', $this->componentOption);
        $view->set('dataviewer', $this->dataviewer);
        $view->set('list', $list_u);

        return ['html' => $view->loadTemplate()];
    }

    /**
     * Create database
     *
     * @return  array
     */
    public function actCreate()
    {
        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }
        // Set params
        $params = [
            'subdir' => $this->subdir,
            'filter' => 'csv',
            'sortby' => Request::getString('sortby', 'name'),
            'sortdir' => Request::getString('sortdir', 'ASC'),
            'showFullMetadata' => true,
            'showUntracked' => true,
            'showAll' => true,
            'getPubConnections' => false,
            'versionTracking' => $this->model->params->get('versionTracking', '0'),
            'recursive' => true,
        ];

        // Incoming
        $db_id = Request::getInt('db_id', false);

        // Get project path
        $path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));

        $list = [];
        $error = false;

        if (file_exists($path) && is_dir($path)) {
            $list = $this->repo->filelist($params);
        } else {
            $error = Lang::txt('PLG_PROJECTS_DATABASES_MISSING_REPO');
        }

        // Get project database object
        $objPD = new \Components\Projects\Tables\Database($this->database);

        // Get database list
        $used_files = $objPD->getUsedItems($this->model->get('id'));

        $files = [];
        foreach ($list as $l) {
            $files[$l->get('dirname', '/')][] = [
                'name' => $l->get('name'),
                'hash' => $l->get('date')->toUnix(),
                'date' => $l->get('date'),
            ];
        }

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder' => 'projects',
            'element' => 'databases',
            'name' => 'create',
        ]);

        $view->set('model', $this->model);
        $view->set('option', $this->componentOption);
        $view->set('files', $files);
        $view->set('msg', null);

        if ($error) {
            $view->setError($error);
        }

        // Get project database object
        $objPD = new \Components\Projects\Tables\Database($this->database);

        if ($objPD->loadRecord($db_id)) {
            $view->set('db_id', $db_id);
            $view->set('dir', $objPD->source_dir);
            $view->set('file', $objPD->source_file);
            $view->set('title', $objPD->title);
            $view->set('desc', $objPD->description);
        }

        return ['html' => $view->loadTemplate()];
    }

    /**
     * Preview data
     *
     * @return  void
     */
    public function actPreviewData()
    {
        // Incoming
        $file = Request::getString('file', '');
        $dir = Request::getString('dir', '');

        if (!$file) {
            echo json_encode(['status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')]);
            return;
        }

        // Get project path
        $path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));

        if ($dir != '') {
            $path .= DS . $dir;
        }

        // Confine the requested file to the project repository: resolve it and
        // verify it still sits under the repo root before opening it.
        $repoReal   = realpath(\Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias')));
        $targetReal = realpath($path . DS . $file);
        if (
            $repoReal === false || $targetReal === false
            || strpos($targetReal, $repoReal . DS) !== 0
        ) {
            echo json_encode(['status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')]);
            return;
        }

        if (is_file($targetReal) && ($handle = fopen($targetReal, "r")) !== false) {
            $table = [];
            $dd = [];

            $sub_dirs = [];
            $list = [];
            // The directory itself came from the request too; only list inside
            // the project repository
            $dirReal = realpath($path);
            if ($dirReal === false || strpos($dirReal . DS, $repoReal . DS) !== 0) {
                echo json_encode(['status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')]);
                return;
            }
            chdir($dirReal);
            exec('find . -type d -not \( -name ".?*" -prune \)', $list);
            foreach ($list as $d) {
                $d = ltrim($d, './');
                if ($d != '.' && $d != '') {
                    $sub_dirs[] = $d;
                }
            }

            $table['repo'] = [
                'prj_alias' => $this->model->get('alias'),
                'wd' => trim($dir, '/'),
                'base' => '/projects/' . $this->model->get('alias')
                    . '/files/?action=download&subdir=' . trim($dir, '/'),
                'sub_dirs' => $sub_dirs,
            ];

            // Check if expert mode CSV
            $expert_mode = false;
            $col_labels = fgetcsv($handle);
            $col_prop = fgetcsv($handle);
            $data_start = fgetcsv($handle);

            if (isset($data_start[0]) && $data_start[0] == 'DATASTART') {
                $expert_mode = true;
            }

            $count = 0;
            $display_count = 0;
            $limit = 100;

            // Non expert mode
            if (!$expert_mode) {
                $handle = fopen($path . '/' . $file, "r");
                $col_labels = fgetcsv($handle);

                $type_info = [];
                while ($r = fgetcsv($handle)) {
                    $col_vals = [];

                    for ($i = 0; $i < count($col_labels); $i++) {
                        $val = isset($r[$i]) ? mb_convert_encoding(trim($r[$i]), "UTF-8") : '';

                        $col_vals[] = $val;

                        $type_info[$i]['type'] = isset($type_info[$i]['type'])
                            ? $type_info[$i]['type']
                            : false;
                        if (isset($type_info[$i]['max_len'])) {
                            $type_info[$i]['max_len'] = $type_info[$i]['max_len'] > strlen($val)
                                ? $type_info[$i]['max_len']
                                : strlen($val);
                        } else {
                            $type_info[$i]['max_len'] = strlen($val);
                        }

                        $type_info[$i]['type'] = $this->guessDataType(
                            $val,
                            $type_info[$i]['type'],
                            $type_info[$i]['max_len'],
                        );
                    }

                    if ($count < $limit) {
                        $table['data'][] = $col_vals;
                        $display_count++;
                    }

                    $count++;
                }

                for ($i = 0; $i < count($col_labels); $i++) {
                    $label = trim($col_labels[$i]);
                    if ($label == '') {
                        $label = 'Column-' . $i;
                    }

                    $dd[$i]['type'] = $type_info[$i]['type'];
                    switch ($dd[$i]['type']) {
                        case 'numeric':
                        case 'float':
                        case 'int':
                        case 'date':
                            $dd[$i]['align'] = 'right';
                            break;
                        default:
                            $dd[$i]['align'] = 'left';
                            break;
                    }

                    $table['header'][] = ['sTitle' => $label, 'sClass' => $dd[$i]['align']];
                    $dd[$i]['label'] = $label;
                    $dd[$i]['idx'] = $i;
                }
            } else {
                while ($r = fgetcsv($handle)) {
                    if ($count < $limit) {
                        $col_vals = [];

                        for ($i = 0; $i < count($col_labels); $i++) {
                            $val = isset($r[$i]) ? mb_convert_encoding(trim($r[$i]), 'UTF-8') : '';

                            $col_vals[] = $val;
                        }

                        $table['data'][] = $col_vals;

                        $display_count++;
                    }

                    $count++;
                }

                for ($i = 0; $i < count($col_labels); $i++) {
                    $label = trim($col_labels[$i]);
                    if ($label == '') {
                        $label = 'Column-' . $i;
                    }

                    $prop = isset($col_prop[$i]) ? '{' . trim($col_prop[$i]) . '}' : '{}';
                    $dd[$i] = json_decode($prop, true);
                    $dd[$i]['type'] = isset($dd[$i]['type']) ? $dd[$i]['type'] : 'text_large';

                    if (!isset($dd[$i]['align'])) {
                        switch ($dd[$i]['type']) {
                            case 'numeric':
                            case 'float':
                            case 'int':
                            case 'date':
                                $dd[$i]['align'] = 'right';
                                break;
                            default:
                                $dd[$i]['align'] = 'left';
                                break;
                        }
                    }

                    $table['header'][] = ['sTitle' => $label, 'sClass' => $dd[$i]['align']];
                    $dd[$i]['label'] = $label;
                    $dd[$i]['idx'] = $i;
                }
            }

            $table['dd'] = $dd;
            $table['rec_total'] = $count;
            $table['rec_display'] = $display_count;

            echo json_encode(['status' => 'success', 'data' => $table]);
        }
    }

    /**
     * Guess data type
     *
     * @param   object   $data
     * @param   string   $type
     * @param   integer  $max_len
     * @return  string
     */
    protected function guessDataType($data, $type, $max_len)
    {
        $data = trim($data);

        if ($type == 'text_large' || $data == '') {
            return $type;
        }

        if (!$type) {
            if (is_numeric($data)) {
                if (strpos($data, '.') === false) {
                    $type = 'int';
                } else {
                    $type = 'float';
                }
            } else {
                if (filter_var($data, FILTER_VALIDATE_EMAIL) !== false) {
                    $type = 'email';
                } elseif (filter_var($data, FILTER_VALIDATE_URL) !== false) {
                    $type = 'link';
                } elseif ($max_len < 50) {
                    $type = 'text_small';
                } else {
                    $type = 'text_large';
                }
            }
        } else {
            if ($type == 'int' || $type == 'float' && is_numeric($data)) {
                if ($type == 'int' && strpos($data, '.') === false) {
                    $type = 'int';
                } else {
                    $type = 'float';
                }
            } else {
                if ($type == 'text_small' && $max_len < 50) {
                    $type = 'text_small';
                } elseif (filter_var($data, FILTER_VALIDATE_EMAIL) !== false) {
                    $type = 'email';
                } elseif (filter_var($data, FILTER_VALIDATE_URL) !== false) {
                    $type = 'link';
                } else {
                    $type = 'text_large';
                }
            }
        }

        return $type;
    }

    /**
     * act_add_new_finish
     *
     * @return  void
     */
    public function actCreateDatabase()
    {
        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Check for request forgeries (the create form's action URL carries the token)
        Request::checkToken(['get', 'post']);

        // Incoming
        $file = Request::getString('file', '');
        $dir = Request::getString('dir', '');
        $title = Request::getString('title', '');
        $desc = Request::getString('desc', '');
        $db_id = Request::getString('db_id', '');
        $hash = Request::getString('hash', \Hubzero\Utility\Date::of()->toUnix());
        $dd = Request::getString('dd', '');
        $d = json_decode($dd, true);

        $db = $this->getDsDb($this->model->get('id'));
        $table = [];

        // Add new or Recreate
        $recreate = ($db_id != '') ? true : false;

        if (!$file) {
            echo json_encode(['status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')]);
            return;
        }

        $repo_base = '/projects/' . $this->model->get('alias');
        $repo_base .= '/files/?action=download&subdir=' . trim($dir, '/');

        // Get project path
        $path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));
        $path .= DS;

        if ($dir != '') {
            $path .= $dir . DS;
        }

        $table['name'] = 'prj_db_' . $this->model->get('id') . '_' . sha1($dir . DS . $file);

        if ($recreate) {
            $sql = "DROP TABLE IF EXISTS `" . $table['name'] . "` ";
            $db->setQuery($sql);
            $db->query();
        }

        $sql = "CREATE TABLE IF NOT EXISTS `" . $table['name'] . "` ";

        $table['cols'][] = '__ds_rec_id int(11) NOT NULL AUTO_INCREMENT NOT NULL';

        // Confine the source file to this project's repository (as the preview
        // action does): the path above is built straight from the request
        $repoReal   = realpath(\Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias')));
        $targetReal = realpath($path . DS . $file);
        if (
            $repoReal === false || $targetReal === false
            || strpos($targetReal, $repoReal . DS) !== 0
        ) {
            echo json_encode(['status' => 'failed', 'msg' => Lang::txt('PLG_PROJECTS_DATABASES_INVALID_FILE')]);
            return;
        }

        if (is_file($targetReal) && ($handle = fopen($targetReal, "r")) !== false) {
            // Check if expert mode CSV
            $expert_mode = false;
            $col_labels = fgetcsv($handle);
            $col_prop = fgetcsv($handle);
            $data_start = fgetcsv($handle);

            if (isset($data_start[0]) && $data_start[0] == 'DATASTART') {
                $expert_mode = true;
            }

            // Non expert mode
            if (!$expert_mode) {
                $handle = fopen($path . '/' . $file, "r");
                $col_labels = fgetcsv($handle);
            }

            $dd = [];

            for ($i = 0; $i < count($col_labels); $i++) {
                $label = trim($col_labels[$i]);
                if ($label == '') {
                    $label = 'Column-' . $i;
                }

                $col_name = strtolower(preg_replace('/\W/', '_', $label));
                $col_name = substr($col_name, 0, (63 - strlen($i))) . '_' . $i;

                // dd may be short or not JSON at all
                $colType = (is_array($d) && isset($d[$i]['type'])) ? $d[$i]['type'] : '';

                switch ($colType) {
                    case 'text_small':
                        $col_type = 'VARCHAR(64)';
                        break;
                    case 'text_large':
                        $col_type = 'TEXT';
                        break;
                    case 'numeric':
                        $col_type = 'NUMERIC (65,4)';
                        break;
                    case 'float':
                        $col_type = 'DOUBLE';
                        break;
                    case 'int':
                        $col_type = 'BIGINT';
                        break;
                    case 'link':
                    case 'image':
                        $col_type = 'VARCHAR(2083)';
                        break;
                    case 'email':
                        $col_type = 'VARCHAR(256)';
                        break;
                    case 'date':
                        $col_type = 'DATE';
                        break;
                    case 'datetime':
                        $col_type = 'DATETIME';
                        break;
                    default:
                        $col_type = 'text';
                        break;
                }

                $table['cols'][] = '`' . $col_name . '` ' . $col_type . ' DEFAULT NULL';
                if (isset($d[$i])) {
                    $dd['cols'][$table['name'] . '.' . $col_name] = $d[$i];
                }
            }

            $sql .= '(' . implode(', ', $table['cols']) . ', PRIMARY KEY (`__ds_rec_id`))
				ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT=' . $db->quote($file);
            $db->setQuery($sql);
            $db->query();

            $count = 0;
            $vals = [];

            while ($r = fgetcsv($handle)) {
                $vals = [];
                for ($i = 0; $i < count($col_labels); $i++) {
                    $vals[] = (isset($r[$i]) && $r[$i] != '') ? $db->quote(trim($r[$i])) : 'NULL';
                }

                $sql = "INSERT INTO " . $table['name'] . " VALUES (NULL, " . implode(", ", $vals) . ")";

                $db->setQuery($sql);
                $db->query();
            }

            $dd['project'] = $this->model->get('id');
            $dd['database'] = 'prj_db_' . $this->model->get('id');
            $dd['table'] = $table['name'];
            $dd['title'] = $title;
            $dd['pk'] = $table['name'] . '.__ds_rec_id';
            $dd['repo_base'] = $repo_base;
            $dd = json_encode($dd);

            // Get project database object
            $objPD = new \Components\Projects\Tables\Database($this->database);

            // Recreate or Expert mode
            if ($recreate) {
                $objPD->loadRecord($db_id);

                if ($objPD->project != $this->model->get('id')) {
                    exit();
                }

                $objPD->title = $title;
                $objPD->source_revision = $hash;
                $objPD->description = $desc;
                $objPD->data_definition = $dd;
                $objPD->updated = Date::toSql();
                $objPD->updated_by = $this->uid;
                $objPD->updated_by = $this->uid;
                $msg = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED_DATABASE') . ' "' . $title . '"';
                $msg .= Lang::txt('PLG_PROJECTS_DATABASES_IN_PROJECT');
            } else {
                $objPD->project = $this->model->get('id');
                $objPD->database_name = $table['name'];
                $objPD->title = $title;
                $objPD->source_file = $file;
                $objPD->source_dir = $dir;
                $objPD->source_revision = $hash;
                $objPD->description = $desc;
                $objPD->data_definition = $dd;
                $objPD->created = Date::toSql();
                $objPD->created_by = $this->uid;

                $objPD->created_by = $this->uid;
                $msg = Lang::txt('PLG_PROJECTS_DATABASES_CREATED_DATABASE') . ' "' . $title . '"';
                $msg .= Lang::txt('PLG_PROJECTS_DATABASES_IN_PROJECT');
            }

            // Store new/update record
            $objPD->store();

            // Update source CSV file
            $this->saveCsv($objPD->id);

            // Success
            if ($objPD->id) {
                // Record project activity
                $this->model->recordActivity(
                    str_replace("'", "\'", $msg),
                    $objPD->id,
                    'databases',
                    $objPD->id,
                    'databases',
                    Route::url(
                        'index.php?option=' . $this->componentOption . '&alias=' . $this->model->get('alias')
                        . '&active=databases',
                    ),
                    'databases',
                    'databases',
                    1,
                );
                ob_clean();
                $this->msg = Lang::txt('PLG_PROJECTS_DATABASES_CREATED');
            }
        }

        $url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI'])
            . "/projects/" . $this->model->get('alias') . "/databases/";

        echo json_encode(['status' => 'success', 'data' => $url]);

        // Success message
        if (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        return;
    }

    /**
     * Save updated CSV file with headers
     *
     * @param   integer  $id  Database ID
     * @return  bool|null
     */
    public function saveCsv($id)
    {
        $db = $this->getDsDb($this->model->get('id'));

        // Get project database object
        $objPD = new \Components\Projects\Tables\Database($this->database);

        // Get project path
        $path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));
        $path .= DS;

        if ($objPD->loadRecord($id)) {
            $db = $this->getDsDb($objPD->project);
            $table = $objPD->database_name;
            $title = $objPD->title;
            $file = $objPD->source_file;
            $dir = ($objPD->source_dir != '') ? $objPD->source_dir . DS : '';
            $dd = json_decode($objPD->data_definition, true);

            $header = [];
            $field_list = [];
            foreach ($dd['cols'] as $field => $prop) {
                $field_list[] = $field;

                $header[0][] = $prop['label'];

                unset($prop['label']);
                unset($prop['idx']);
                unset($prop['styles']);

                $header[1][] = rtrim(
                    ltrim(str_replace('","', "\",\r\n\"", json_encode($prop)), '{'),
                    '}',
                );
            }

            $header[2] = ['DATASTART'];

            $path .= $dir;

            // The stored dir/file originally came from a request: confine the
            // target to this project's repository before writing and committing
            $repoReal   = realpath(\Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias')));
            $targetReal = realpath($path . $file);
            $dirReal    = realpath($path);
            if (
                $repoReal === false || $targetReal === false || $dirReal === false
                || strpos($targetReal, $repoReal . DS) !== 0
                || strpos($dirReal . DS, $repoReal . DS) !== 0
            ) {
                return false;
            }
            $fp = fopen($targetReal, 'w+');
            foreach ($header as $h) {
                fputcsv($fp, $h);
            }

            $sql = "SELECT " . implode(', ', $field_list) . " FROM $table";
            $db->setQuery($sql);
            $res = $db->query();

            while ($row = $res->loadNextRow(MYSQLI_NUM)) {
                fputcsv($fp, $row);
            }

            fclose($fp);

            // Commit update file
            $commit_message = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED_FILE') . ' ' . $file;
            $author = User::get('name') . ' <' . User::get('email') . '> ';

            chdir($dirReal);
            exec($this->gitpath . ' add ' . escapeshellarg($file));
            exec($this->gitpath . ' commit ' . escapeshellarg($file)
                . ' -m ' . escapeshellarg($commit_message)
                . ' --author=' . escapeshellarg($author) . ' 2>&1');

            $hash = \Hubzero\Utility\Date::of()->toUnix(); // Use the current time as a hash
            $objPD->source_revision = $hash;
            $objPD->store();

            $msg = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED_FILE') . ' "' . $file . '"'
                . Lang::txt('PLG_PROJECTS_DATABASES_IN_PROJECT');
            $this->model->recordActivity(
                str_replace("'", "\'", $msg),
                $file,
                'files',
                $file,
                'files',
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=files',
                ),
                'files',
                'files',
                1,
            );
            ob_clean();
        }

        return null;
    }

    /**
     * Delete database
     *
     * @return  array
     */
    public function actDelete()
    {
        // Require POST to prevent accidental deletion by crawlers
        if (Request::method() !== 'POST') {
            App::abort(405, Lang::txt('JERROR_METHOD_NOT_ALLOWED'));
        }

        Request::checkToken();

        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Incoming
        $id = Request::getString('db_id', false);
        $ds_db = $this->getDsDb($this->model->get('id'));

        // Get project database object
        $objPD = new \Components\Projects\Tables\Database($this->database);

        if ($objPD->loadRecord($id)) {
            $table = $objPD->database_name;
            $title = $objPD->title;

            if ($table && $table != '' && $objPD->project == $this->model->get('id')) {
                // Removing the record for this database
                $objPD->delete();

                // Removing mysql table for this database
                $sql = "DROP TABLE $table";
                $ds_db->setQuery($sql);
                $ds_db->query();

                $this->msg = Lang::txt('PLG_PROJECTS_DATABASES_DELETED');
            }

            // Record project activity
            $msg = Lang::txt('PLG_PROJECTS_DATABASES_REMOVED_DATABASE') . ' "' . $title . '"';
            $msg .= Lang::txt('PLG_PROJECTS_DATABASES_FROM_PROJECT');
            $this->model->recordActivity(
                str_replace("'", "\'", $msg),
                $id,
                'databases',
                $id,
                'databases',
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=databases',
                ),
                'databases',
                'databases',
                1,
            );
        }

        $url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI'])
            . "/projects/" . $this->model->get('alias') . "/databases/";

        // Pass success message
        if (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        // Redirect
        App::redirect($url);
    }

    /**
     * Update database
     *
     * @return  array
     */
    public function actUpdate()
    {
        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Check for request forgeries (the update dialog form posts the token)
        Request::checkToken();

        // Incoming
        $id = Request::getString('db_id', '');
        $title = Request::getString('db_title', '');
        $description = Request::getString('db_description', '');

        // Get project database object
        $objPD = new \Components\Projects\Tables\Database($this->database);

        if ($objPD->loadRecord($id)) {
            $dd = json_decode($objPD->data_definition, true);

            if ($title != '' && $objPD->project == $this->model->get('id')) {
                // Setting title and description
                $objPD->title = $title;
                $dd['title'] = $title;
                $objPD->description = $description;
                $objPD->data_definition = json_encode($dd);
                $objPD->store();

                $this->msg = Lang::txt('PLG_PROJECTS_DATABASES_UPDATED');
            }
        }

        $url = str_replace($_SERVER['SCRIPT_URL'], '', $_SERVER['SCRIPT_URI'])
            . "/projects/" . $this->model->get('alias') . "/databases/";

        // Pass success message
        if (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        // Redirect
        App::redirect($url);
    }

    /**
     * Making a copy of the database for publications
     *
     * Function to be called outside of the databases plugin
     *
     * @param   integer  $identifier  Database ID or name
     * @param   object   $project     Project object
     * @param   string   $base_path   File-repository base path
     * @return  integer|false
     */
    public function cloneDatabase($identifier = 0, $project = null, $base_path = null)
    {
        if (!$identifier || $project == null) {
            $this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_MISSING_ID'));
            return false;
        }

        $db = App::get('db');
        $ds_db = $this->getDsDb($project->get('id'));

        // Load database record
        $objPD = new \Components\Projects\Tables\Database($db);
        if (!$objPD->loadRecord($identifier)) {
            $this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_LOAD_RECORD'));
            return false;
        }

        $dd = json_decode($objPD->data_definition, true);

        // Get last version
        $objPDV = new \Components\Projects\Tables\DatabaseVersion($db);
        $version = $objPDV->getMaxVersion($objPD->database_name) + 1;

        // Start cloning
        $orig_table = $dd['table'];
        $new_table = $orig_table . '_' . $version;

        $dd['table'] = $new_table;

        $new_cols = [];
        foreach ($dd['cols'] as $col => $prop) {
            $col = explode('.', $col);
            $new_cols[$new_table . '.' . $col[1]] = $prop;
        }

        $dd['cols'] = $new_cols;
        $dd['pk'] = $new_table . '.__ds_rec_id';

        if ($base_path != null) {
            $dd['repo_base'] = $base_path;
        }

        $dd = json_encode($dd);

        // Make new version record
        $objPDV->database_name = $objPD->database_name;
        $objPDV->version = $version;
        $objPDV->data_definition = $dd;

        $objPDV->store();

        // Clone original table
        $sql = "CREATE TABLE $new_table LIKE $orig_table";
        $ds_db->setQuery($sql);
        $ds_db->query();

        // Copy table contents
        $sql = "INSERT INTO $new_table SELECT * FROM $orig_table";
        $ds_db->setQuery($sql);
        $ds_db->query();

        return $version;
    }

    /**
     * Remove databases used in a draft publications
     *
     * @param   integer  $identifier  Database ID or name
     * @param   object   $project     Project object
     * @param   integer  $version     Database version
     * @return  bool
     */
    public function removeDatabase($identifier = 0, $project = null, $version = null)
    {
        if (!$identifier || $project == null) {
            $this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_MISSING_ID'));
            return false;
        }

        if ($version === null || trim($version) == '') {
            $this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_INVALID_VERSION'));
            return false;
        }

        $db = App::get('db');
        $ds_db = $this->getDsDb($project->get('id'));

        // Load database record
        $objPD = new \Components\Projects\Tables\Database($db);
        if (!$objPD->loadRecord($identifier)) {
            $this->setError(Lang::txt('PLG_PROJECTS_DATABASES_ERROR_LOAD_RECORD'));
            return false;
        }

        // Remove record from database versions table
        $sql = 'DELETE FROM `#__project_database_versions` WHERE'
            . ' database_name = ' . $db->quote($objPD->database_name)
            . ' AND version = ' . $db->quote($version);

        $db->setQuery($sql);
        $db->query();

        // Remove database table
        $table_name = $objPD->database_name . '_' . $version;
        $sql = 'DROP TABLE ' . $table_name;
        $ds_db->setQuery($sql);
        return $ds_db->query();
    }

    /**
     * get_ds_db
     *
     * @param   string  $id
     * @return  object
     */
    public function getDsDb($id)
    {
        $opt = [];

        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . 'prj_db_' . $id;

        $opt['driver'] = 'mysql';
        $opt['host'] = $this->params->get('db_host');
        $opt['user'] = $this->params->get('db_user');
        $opt['password'] = $this->params->get('db_password');
        $opt['prefix'] = '';

        $db = \Hubzero\Database\Driver::getInstance($opt);

        $db->setQuery($sql);
        $db->query();

        $opt['database'] = 'prj_db_' . $id;

        $db = \Hubzero\Database\Driver::getInstance($opt);

        return $db;
    }
}
