<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Include note model

namespace Plugins\Projects\Notes;

use Hubzero\Utility\Str;
use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Document;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

/**
 * Projects Notes (wiki) plugin
 */
class Notes extends Plugin
{
    protected $area;
    protected $config;
    protected $database;
    protected $task;
    protected $uid;
    protected $model;
    protected $note;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Name of project group
     *
     * @var  array
     */
    protected $group = null;

    /**
     * Name of master scope
     *
     * @var  array
     */
    protected $masterScope = null;

    /**
     * Name of page
     *
     * @var  array
     */
    protected $pagename = null;

    /**
     * Tool record (tool wiki)
     *
     * @var  array
     */
    protected $tool = null;

    /**
     * Controller name
     *
     * @var  array
     */
    protected $controllerName = null;

    /**
     * Store internal message
     *
     * @var  array|null
     */
    protected $msg = null;

    /**
     * Component name
     *
     * @var  string
     */
    protected $componentOption = 'com_projects';

    /**
     * Event call to determine if this plugin should return data
     *
     * @param   string  $alias
     * @return  array   Plugin name and title
     */
    public function &onProjectAreas($alias = null)
    {
        $area = [
            'name'    => 'notes',
            'title'   => Lang::txt('COM_PROJECTS_TAB_NOTES'),
            'submenu' => null,
            'show'    => true,
            'icon'    => 'f0c1',
        ];

        return $area;
    }

    /**
     * Event call to return count of items
     *
     * @param   object  $model  Project
     * @return  array   integer
     */
    public function &onProjectCount($model)
    {
        $group_prefix = $model->config()->get('group_prefix', 'pr-');
        $groupname = $group_prefix . $model->get('alias');
        $scope = 'projects' . DS . $model->get('alias') . DS . 'notes';

        // Get our model
        $note = new \Components\Projects\Models\Note($scope, $groupname, $model->get('id'));

        $counts['notes'] = $note->getNoteCount();

        return $counts;
    }

    /**
     * Event call to return data for a specific project
     *
     * @param   object  $model   Project model
     * @param   string  $action  Plugin task
     * @param   string  $areas   Plugins to return data
     * @param   string  $tool    Name of tool wiki belongs to
     * @return  array|null   Return array of html
     */
    public function onProject($model, $action = '', $areas = null, $tool = null)
    {
        $returnhtml = true;

        $arr = [
            'html'     => '',
            'metadata' => '',
        ];

        // Get this area details
        $this->area = $this->onProjectAreas();

        // Check if our area is in the array of areas we want to return results for
        if (is_array($areas)) {
            if (empty($this->area) || !in_array($this->area['name'], $areas)) {
                return null;
            }
        }
        // Check that project exists
        if (!$model->exists()) {
            return $arr;
        }

        // Check authorization
        if (!$model->access('member')) {
            return $arr;
        }

        // Model
        $this->model = $model;

        // Are we returning HTML?
        if ($returnhtml) {
            // Load wiki language file
            Lang::load('com_wiki') || Lang::load('com_wiki', Component::path('com_wiki') . DS . 'site');

            if ($model->access('readonly')) {
                Component::params('com_wiki')->set('access-page-view', true);
                Component::params('com_wiki')->set('access-check-done', true);
            }

            // Set vars
            $this->database = App::get('db');
            $this->uid      = User::get('id');

            // Load component configs
            $this->config = $model->config();
            $this->group = $this->config->get('group_prefix', 'pr-') . $this->model->get('alias');

            // Incoming
            $this->pagename = trim(Request::getString('pagename', ''));
            if (strstr($this->pagename, '/')) {
                $bits = explode('/', $this->pagename);
                $this->pagename = array_pop($bits);
                $scope = implode('/', $bits);
                Request::setVar('scope', $scope);
            }
            $this->masterScope = 'projects' . DS . $this->model->get('alias') . DS . 'notes';

            // Get our model
            $this->note = new \Components\Projects\Models\Note(
                $this->masterScope,
                $this->group,
                $this->model->get('id'),
            );

            // What's the task?
            $this->task = $action ? $action : Request::getString('action', 'view');

            // Publishing?
            if ($this->task == 'browser') {
                return $this->browser();
            }

            // Import some needed libraries
            switch ($this->task) {
                case 'upload':
                case 'download':
                case 'deletefolder':
                case 'deletefile':
                case 'media':
                case 'list':
                    $this->controllerName = 'media';
                    break;

                case 'history':
                case 'compare':
                case 'approve':
                case 'deleterevision':
                    $this->controllerName = 'history';
                    break;

                case 'editcomment':
                case 'addcomment':
                case 'savecomment':
                case 'reportcomment':
                case 'removecomment':
                case 'comments':
                    $this->controllerName = 'comments';
                    $cid = Request::getInt('cid', 0);
                    if ($cid) {
                        Request::setVar('comment', $cid);
                    }
                    break;

                case 'delete':
                case 'edit':
                case 'save':
                case 'rename':
                case 'saverename':
                default:
                    $this->controllerName = 'pages';
                    break;
            }

            if (
                substr(strtolower($this->pagename), 0, strlen('image:')) == 'image:'
                || substr(strtolower($this->pagename), 0, strlen('file:')) == 'file:'
            ) {
                $this->controllerName = 'media';
                $this->task = 'download';
            }

            if (
                !file_exists(
                    Component::path('com_wiki')
                        . DS
                        . 'site'
                        . DS
                        . 'controllers'
                        . DS
                        . $this->controllerName
                        . '.php',
                )
            ) {
                $this->controllerName = 'pages';
            }
            // Listing/unlisting?
            if ($this->task == 'publist' || $this->task == 'unlist') {
                $arr['html'] = $this->list();
            } elseif ($this->task == 'share') {
                $arr['html'] = $this->share();
            } else {
                // Display page
                $arr['html'] = $this->page();
            }
        }

        // Return data
        return $arr;
    }

    /**
     * View of project note
     *
     * @return  string
     */
    public function page()
    {
        // Incoming
        $preview = trim(Request::getString('preview', ''));
        $note    = Request::getArray('page', [], 'post');
        $scope   = trim(Request::getString('scope', $this->masterScope), '/');
        $scope   = trim(str_replace($this->masterScope, '', $scope), '/');
        $pagePrefix = '';

        // Output HTML (wrap for notes)
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'notes',
            'name'    => 'wrap',
            'layout'  => 'wrap',
        ]);

        // Get first project note
        $view->set('firstNote', $this->note->getFirstNote($pagePrefix));

        // Default view to first available note if no page is requested
        if (!$this->pagename && $this->task != 'new' && $this->task != 'save') {
            $this->pagename = $view->firstNote ? $view->firstNote : '';
        }

        // Are we saving?
        $save   = $this->task == 'save' ? 1 : 0;
        $rename = $this->task == 'saverename' ? 1 : 0;
        $canDelete = 1;

        // Get page
        $view->set('page', $this->note->page($this->pagename));
        $view->set('content', null);
        $exists = $view->page->get('id') ? true : false;

        Request::setVar('pagename', ($scope ? $scope . '/' : '') . $this->pagename);
        Request::setVar('action', $this->task);
        Request::setVar('scope', $scope);
        Request::setVar('group_cn', $this->group);

        Request::setVar('tool', $this->tool);
        Request::setVar('project', $this->model);
        Request::setVar('candelete', $canDelete);

        if (!$view->page->get('id') && $this->task == 'view' && $view->page->get('namespace') != 'special') {
            // Output HTML (wrap for notes)
            $nview = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'notes',
                'name'    => 'pages',
                'layout'  => 'doesnotexist',
            ]);
            $nview->set('scope', $scope);
            $nview->set('option', $this->componentOption);
            $nview->set('project', $this->model);
            $view->set('content', $nview->loadTemplate());
        }

        $basePath = Component::path('com_wiki') . DS . 'site';
        if ($this->task == 'edit' || $this->task == 'new' || $this->task == 'save') {
            $basePath = __DIR__;
            if (!$this->model->access('content')) {
                throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
            }
        }

        if (!$view->content) {
            $controllerName = "Components\Wiki\Site\Controllers\\" . ucfirst($this->controllerName);
            // Instantiate controller
            $controller = new $controllerName([
                'base_path' => $basePath,
                'scope'     => 'project',
                'scope_id'  => $this->model->get('id'),
                'sub'       => true,
            ]);

            // Catch any echoed content with ob
            ob_start();
            $controller->execute();

            // Record activity
            if ($save && !$preview && !$this->getError() && !$controller->getError()) {
                $what  = $exists
                    ? Lang::txt('COM_PROJECTS_NOTE_EDITED')
                    : Lang::txt('COM_PROJECTS_NOTE_ADDED');
                $what .= $exists ? ' "' . $controller->page->get('title') . '" ' : '';
                $what .= ' ' . Lang::txt('COM_PROJECTS_NOTE_IN_NOTES');
                $aid = $this->model->recordActivity(
                    $what,
                    $controller->page->get('id'),
                    'notes',
                    Route::url(
                        'index.php?option='
                            . $this->componentOption
                            . '&alias='
                            . $this->model->get('alias')
                            . '&active=notes',
                    ),
                    'notes',
                    0,
                );

                // Record page order for new pages
                $lastorder = $this->note->getLastNoteOrder($scope);
                $order = intval($lastorder + 1);
                $this->note->saveNoteOrder($scope, $order);
            }

            // Make sure all scopes of subpages are valid after rename
            if ($rename) {
                // Incoming
                $oldpagename = trim(Request::getString('oldpagename', '', 'post'));
                $newpagename = trim(Request::getString('newpagename', '', 'post'));
                $this->note->fixScopePaths($scope, $oldpagename, $newpagename);
            }

            $controller->redirect();
            $view->content = ob_get_contents();
            ob_end_clean();
        }

        // Fix pathway (com_wiki screws it up)
        $this->fixupPathway();

        // Get messages and errors
        $view->set('msg', isset($this->msg) ? $this->msg : null);
        if ($this->getError()) {
            $view->setError($this->getError());
        }

        $view->set('title', $this->area['title']);
        $view->set('note', $this->note);
        $view->set('task', $this->task);
        $view->set('option', $this->componentOption);
        $view->set('database', $this->database);
        $view->set('project', $this->model);
        $view->set('uid', $this->uid);
        $view->set('pagename', $this->pagename);
        $view->set('scope', $scope);
        $view->set('preview', $preview);
        $view->set('group', $this->group);
        $view->set('params', $this->params);

        return $view->loadTemplate();
    }

    /**
     * List/unlist on public project page
     */
    protected function list()
    {
        // Incoming
        $id = trim(Request::getInt('p', 0));

        // Load requested page
        $page = $this->note->page($id);
        if (!$page->get('id')) {
            App::redirect(Route::url($this->model->link('notes')));
        }

        $listed = $this->task == 'publist' ? 1 : 0;

        // Get/update public stamp for page
        if ($this->note->getPublicStamp($page->get('id'), true, $listed)) {
            $this->msg = $this->task == 'publist'
                ? Lang::txt('COM_PROJECTS_NOTE_MSG_LISTED')
                : Lang::txt('COM_PROJECTS_NOTE_MSG_UNLISTED');

            Notify::message($this->msg, 'success', 'projects');

            App::redirect(
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&scope='
                        . $page->get('scope')
                        . '&pagename='
                        . $page->get('pagename'),
                ),
            );
        }

        App::redirect(Route::url($this->model->link('notes')));
    }

    /**
     * Get public link and list/unlist
     *
     * @return  string
     */
    protected function share()
    {
        // Incoming
        $id = trim(Request::getInt('p', 0));

        // Load requested page
        $page = $this->note->page($id);
        if (!$page->get('id')) {
            App::redirect(Route::url($this->model->link('notes')));
        }

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'notes',
            'name'    => 'pubsettings',
        ]);

        // Get/update public stamp for page
        $view->set('publicStamp', $this->note->getPublicStamp($page->get('id'), true));
        $view->set('option', $this->componentOption);

        if (!$view->publicStamp) {
            $this->setError(Lang::txt('PLG_PROJECTS_NOTES_ERROR_SHARE'));

            // Output error
            $view = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'files',
                'name'    => 'error',
            ]);

            $view->set('title', '');
            $view->setErrors($this->getErrors());

            return $view->loadTemplate();
        }

        $view->set('project', $this->model);
        $view->set('url', Route::url($this->model->link('notes')));
        $view->set('config', $this->model->config());
        $view->set('page', $page);
        $view->set('revision', $page->revision('current'));
        $view->set('masterscope', 'projects' . DS . $this->model->get('alias') . DS . 'notes');
        $view->set('params', $this->params);
        $view->set('ajax', Request::getInt('ajax', 0));
        $view->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Fix pathway
     *
     * @return  void
     */
    public function fixupPathway()
    {
        Pathway::clear();

        // Add group
        if ($this->model->groupOwner()) {
            Pathway::append(
                Lang::txt('COM_PROJECTS_GROUPS_COMPONENT'),
                Route::url('index.php?option=com_groups'),
            );
            Pathway::append(
                \Hubzero\Utility\Str::truncate($this->model->groupOwner('description'), 50),
                Route::url('index.php?option=com_groups&cn=' . $this->model->groupOwner('cn')),
            );
            Pathway::append(
                Lang::txt('COM_PROJECTS_PROJECTS'),
                Route::url('index.php?option=com_groups&cn=' . $this->model->groupOwner('cn') . '&active=projects'),
            );
        } else {
            Pathway::append(
                Lang::txt('COMPONENT_LONG_NAME'),
                Route::url('index.php?option=' . $this->componentOption),
            );
        }

        if ($this->model->exists()) {
            Pathway::append(
                stripslashes($this->model->get('title')),
                Route::url('index.php?option=' . $this->componentOption . '&alias=' . $this->model->get('alias')),
            );
        }

        if ($this->tool && $this->tool->id) {
            Pathway::append(
                ucfirst(Lang::txt('COM_PROJECTS_PANEL_TOOLS')),
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=tools',
                ),
            );

            Pathway::append(
                \Hubzero\Utility\Str::truncate($this->tool->title, 50),
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=tools&tool='
                        . $this->tool->id,
                ),
            );

            Pathway::append(
                ucfirst(Lang::txt('COM_PROJECTS_TOOLS_TAB_WIKI')),
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=tools&tool='
                        . $this->tool->id
                        . '&action=wiki',
                ),
            );
        } else {
            Pathway::append(
                ucfirst(Lang::txt('COM_PROJECTS_TAB_NOTES')),
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=notes',
                ),
            );
        }
    }

    /**
     * List project notes available for publishing
     *
     * @return  array|false
     */
    public function browser()
    {
        // Incoming
        $ajax      = Request::getInt('ajax', 0);
        $primary   = Request::getInt('primary', 1);
        $versionid = Request::getInt('versionid', 0);

        if (!$ajax) {
            return false;
        }

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'notes',
            'name'    => 'browser',
        ]);

        // Get current attachments
        $pContent = new \Components\Publications\Tables\Attachment($this->database);
        $role  = $primary ? '1' : '0';
        $other = $primary ? '0' : '1';

        $view->set('attachments', $pContent->getAttachments(
            $versionid,
            $filters = ['role' => $role, 'type' => 'note'],
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
        $html = $view->loadTemplate();

        $arr = [
            'html'     => $html,
            'metadata' => '',
        ];

        return $arr;
    }

    /**
     * Serve wiki page (usually via public link)
     *
     * @param   string   $type
     * @param   integer  $projectid
     * @param   string   $query
     * @return  boolean
     */
    public function serve($type = '', $projectid = 0, $query = '')
    {
        $this->area = $this->onProjectAreas();
        if ($type != $this->area['name']) {
            return false;
        }
        $data = json_decode($query);

        if (!isset($data->pageid) || !$projectid) {
            return false;
        }

        $this->loadLanguage();

        $database = App::get('db');
        $this->componentOption = 'com_projects';

        // Instantiate a project
        $this->model = new \Components\Projects\Models\Project($projectid);

        if (!$this->model->exists()) {
            return false;
        }

        $groupname = $this->model->config()->get('group_prefix', 'pr-') . $this->model->get('alias');
        $scope = 'projects' . DS . $this->model->get('alias') . DS . 'notes';

        // Get our model
        $this->note = new \Components\Projects\Models\Note($scope, $groupname, $projectid);

        // Fix pathway (com_wiki screws it up)
        $this->fixupPathway();

        // Load requested page
        $page = $this->note->page($data->pageid);
        if (!$page->get('id')) {
            return false;
        }

        // Write title & build pathway
        Document::setTitle(Lang::txt(strtoupper($this->componentOption)) . ': '
            . stripslashes($this->model->get('title')) . ' - ' . stripslashes($page->get('title')));

        // Instantiate a new view
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'notes',
            'name'    => 'pubview',
        ]);
        $view->set('option', $this->componentOption);
        $view->set('model', $this->model);
        $view->set('page', $page);
        $view->set('revision', $page->revision('current'));
        $view->set('masterscope', 'projects' . DS . $this->model->get('alias') . DS . 'notes');

        // Output HTML
        if ($this->getError()) {
            $view->setError($this->getError());
        }

        $view->display();
        return true;
    }

    /**
     * Event call to get side content for main project page
     *
     * @param   object  $model
     * @return  mixed
     */
    public function onProjectMiniList($model)
    {
        if (!$model->exists() || !$model->access('content')) {
            return false;
        }

        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'notes',
            'name'    => 'mini',
        ]);

        $group = $model->config()->get('group_prefix', 'pr-') . $model->get('alias');
        $masterScope = 'projects' . DS . $model->get('alias') . DS . 'notes';

        // Get our model
        $note = new \Components\Projects\Models\Note(
            $masterScope,
            $group,
            $model->get('id'),
        );
        $view->set('notes', $note->getNotes());
        $view->set('model', $model);

        return $view->loadTemplate();
    }

    /**
     * Event call to get content for public project page
     *
     * @param   object  $model
     * @return  mixed
     */
    public function onProjectPublicList($model)
    {
        if (!$model->exists() || !$model->isPublic()) {
            return false;
        }

        if (!$model->params->get('notes_public', 0)) {
            return false;
        }

        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'notes',
            'name'    => 'publist',
        ]);

        $database = App::get('db');
        $objSt    = new \Components\Projects\Tables\Stamp($database);

        $view->set('items', $objSt->getPubList($model->get('id'), 'notes'));
        $view->set('page', \Components\Wiki\Models\Page::blank());
        $view->set('model', $model);

        return $view->loadTemplate();
    }
}
