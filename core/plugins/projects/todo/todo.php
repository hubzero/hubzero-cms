<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Include model

namespace Plugins\Projects\Todo;

use Hubzero\Plugin\Plugin;
use Hubzero\Utility\Sanitize;
use Hubzero\Utility\Str;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Utility\Arr;
use Exception;

/**
 * Projects todo's
 */
class Todo extends Plugin
{
    protected $area;
    /** @var \Hubzero\Database\Driver|null */
    protected $database;
    protected $mine;
    protected $task;
    protected $todoid;
    protected $uid;
    protected $model;
    /** @var \Components\Projects\Models\Todo|null */
    protected $todo;

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Component name
     *
     * @var  string
     */
    protected $componentOption = 'com_projects';

    /**
     * Store internal message
     *
     * @var  string|null
     */
    protected $msg = null;

    /**
     * Event call to determine if this plugin should return data
     *
     * @param   mixed  $alias
     * @return  array    Plugin name and title
     */
    public function &onProjectAreas($alias = null)
    {
        $area = [
            'name'    => 'todo',
            'title'   => Lang::txt('COM_PROJECTS_TAB_TODO'),
            'submenu' => null,
            'show'    => true,
            'icon'    => 'f08d',
        ];

        return $area;
    }

    /**
     * Event call to return count of items
     *
     * @param   object   $model   Project
     * @param   integer  $admin
     * @return  array    integer
     */
    public function &onProjectCount($model, $admin = 0)
    {
        $database = App::get('db');

        $objTD = new \Components\Projects\Tables\Todo($database);
        $counts['todo'] = $objTD->getTodos($model->get('id'), $filters = ['count' => 1]);

        if ($admin == 1) {
            $counts['todos_completed'] = $objTD->getTodos($model->get('id'), $filters = [
                'count' => 1,
                'state' => 1,
            ]);
        }

        return $counts;
    }

    /**
     * Event call to return data for a specific project
     *
     * @param   object  $model   Project model
     * @param   string  $action  Plugin task
     * @param   string  $areas   Plugins to return data
     * @return  array|null   Return array of html
     */
    public function onProject($model, $action = '', $areas = null)
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
            // Get our To do model
            $this->todo = new \Components\Projects\Models\Todo();

            // Set vars
            $this->task     = $action ? $action : Request::getString('action', '');
            $this->todoid   = Request::getInt('todoid', 0);
            $this->database = App::get('db');
            $this->uid      = User::get('id');

            switch ($this->task) {
                case 'save':
                    $arr['html'] = $this->save();
                    break;
                case 'changestate':
                    $arr['html'] = $this->save();
                    break;
                case 'delete':
                    $arr['html'] = $this->delete();
                    break;
                case 'assign':
                    $arr['html'] = $this->save();
                    break;
                case 'view':
                case 'new':
                case 'edit':
                    $arr['html'] = $this->item();
                    break;
                case 'savecomment':
                    $arr['html'] = $this->saveComment();
                    break;
                case 'deletecomment':
                    $arr['html'] = $this->deleteComment();
                    break;
                case 'reorder':
                case 'sortitems':
                    $arr['html'] = $this->reorder();
                    break;
                case 'page':
                default:
                    $arr['html'] = $this->page();
                    break;
            }
        }

        // Return data
        return $arr;
    }

    /**
     * Event call to get side content for main project page
     *
     * @param   object  $model
     * @return  string|false
     */
    public function onProjectMiniList($model)
    {
        if (!$model->exists() || !$model->access('content')) {
            return false;
        }

        // Get our To do model
        $this->todo = new \Components\Projects\Models\Todo();

        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'todo',
            'name'    => 'mini',
        ]);

        // Filters for returning results
        $view->set('filters', [
            'projects' => [$model->get('id')],
            'limit'    => $model->config()->get('sidebox_limit', 5),
            'start'    => 0,
            'sortby'   => 'due',
            'sortdir'  => 'ASC',
        ]);

        $view->set('items', $this->todo->entries('list', $view->get('filters')));
        $view->set('model', $model);
        return $view->loadTemplate();
    }

    /**
     * To do in multiple projects (members/groups plugins)
     *
     * @param   string   $area
     * @param   object   $model
     * @param   array    $projects
     * @param   integer  $uid
     * @param   array    $filters    Query filters
     * @return  string|null
     */
    public function onShared($area, $model, $projects, $uid, $filters)
    {
        // Check if our area is the one we want to return results for
        if ($area != 'todo') {
            return null;
        }

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'todo',
            'name'    => 'view',
            'layout'  => 'shared',
        ]);
        $view->set('limit', isset($filters['limit']) ? $filters['limit'] : 0);
        $view->set('filters', $filters);
        $view->set('uid', $uid);
        $view->set('model', $model);
        $view->set('database', App::get('db'));
        $view->set('todo', new \Components\Projects\Models\Todo());

        return $view->loadTemplate();
    }

    /**
     * View of items
     *
     * @return  string
     */
    public function page()
    {
        // Get default view from owner params
        $member = $this->model->member();
        $mparams = new \Hubzero\Config\Registry($member ? $member->params : '');
        $defaultView = $mparams->get('todo_layout', 'pinboard');

        // Incoming
        $layout = Request::getString('l', $defaultView) == 'pinboard' ? 'pinboard' : 'list';
        $mine = isset($this->mine) ? $this->mine : Request::getInt('mine', 0);

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'todo',
            'name'    => 'view',
            'layout'  => $layout,
        ]);

        // Filters for returning results
        $view->set('filters', [
            'projects'   => [$this->model->get('id')],
            'limit'      => Request::getInt('limit', $this->params->get('display_limit', 50)),
            'start'      => Request::getInt('limitstart', 0),
            'todolist'   => Request::getWord('list', ''),
            'state'      => isset($this->state) ? $this->state : Request::getInt('state', 0),
            'mine'       => $mine,
            'assignedto' => $mine == 1 ? $this->uid : 0,
            'sortby'     => Request::getString('sortby', 'priority'),
            'sortdir'    => Request::getString('sortdir', 'ASC'),
            'layout'     => $layout,
        ]);

        $view->set('option', $this->componentOption);
        $view->set('database', $this->database);
        $view->set('model', $this->model);
        $view->set('uid', $this->uid);
        $view->set('title', $this->area['title']);
        $view->set('todo', $this->todo);

        // Update view preference if changed
        if ($layout != $defaultView) {
            $objO = $this->model->table('Owner');
            $objO->saveParam(
                $this->model->get('id'),
                $this->uid,
                $param = 'todo_layout',
                $layout,
            );
        }

        return $view->loadTemplate();
    }

    /**
     * View of item
     *
     * @return  string
     */
    public function item()
    {
        // Incoming
        $todoid = $this->todoid ? $this->todoid : Request::getInt('todoid', 0);
        $layout = ($this->task == 'edit' || $this->task == 'new') ? 'edit' : 'default';

        // Check permission
        if ($this->task == 'edit' && !$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        $view = new \Hubzero\Plugin\View([
            'folder'  => $this->pluginType,
            'element' => $this->pluginName,
            'name'    => 'item',
            'layout'  => $layout,
        ]);
        $view->set('option', $this->option);
        $view->set('todo', $this->todo);
        $view->set('params', $this->model->params);
        $view->set('model', $this->model);

        // Get team members (to assign items to)
        $objO = $this->model->table('Owner');
        $view->set('team', $objO->getOwners($this->model->get('id'), $tfilters = ['status' => 1]));

        if (isset($this->entry) && is_object($this->entry)) {
            $view->set('row', $this->entry);
        } else {
            $view->set('row', $this->todo->entry($todoid));
        }

        if (!$view->get('row')->exists() && $this->task != 'new') {
            return $this->page();
        }

        // Append breadcrumbs
        Pathway::append(
            stripslashes(\Hubzero\Utility\Str::truncate($view->get('row')->get('content', ''), 40)),
            Route::url($this->model->link('todo') . '&action=view&todoid=' . $todoid),
        );

        $view->set('uid', $this->uid);
        $view->set('title', $this->area['title']);
        $view->set('list', Request::getString('list', ''));
        $view->set('ajax', Request::getInt('ajax', 0));

        return $view->loadTemplate();
    }

    /**
     * Save item
     *
     * @return  string|void
     */
    public function save()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $listcolor = Request::getString('list', '');
        $content   = Request::getString('content', '');
        $todoid    = Request::getInt('todoid', 0);
        $newlist   = Request::getString('newlist', '', 'post');
        $newcolor  = Request::getString('newcolor', '', 'post');
        $todoColors = ['orange', 'lightblue', 'green', 'purple', 'blue', 'black', 'red', 'yellow', 'pink'];
        if (!in_array($listcolor, $todoColors)) {
            $listcolor = $listcolor == 'none' ? 'none' : '';
        }
        if (!in_array($newcolor, $todoColors)) {
            $newcolor = '';
        }
        $page      = Request::getString('page', 'list', 'post');
        $assigned  = Request::getInt('assigned', 0);
        $mine      = Request::getInt('mine', 0);
        $state     = Request::getInt('state', 0);
        $ajax      = Request::getInt('ajax', 0);
        $task      = $this->task;

        $new = 0;

        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Check if assignee is owner
        $objO = $this->model->table('Owner');
        if ($assigned && !$objO->isOwner($assigned, $this->model->get('id'))) {
            $assigned = 0;
        }
        if ($mine && !$assigned) {
            $assigned = $this->uid;
        }

        // Initiate extended database class
        $objTD = new \Components\Projects\Tables\Todo($this->database);

        // Load up todo if exists
        if (!$objTD->loadTodo($this->model->get('id'), $todoid)) {
            $objTD->created_by = $this->uid;
            $objTD->created    = Date::toSql();
            $objTD->projectid  = $this->model->get('id');
            $assigned          = $assigned;
            $new               = 1;
        } else {
            $content = $content ? $content : $objTD->content;
        }

        // Prevent resubmit
        if ($task == 'save' && $content == '' && $newlist == '') {
            App::redirect($this->model->link('todo'));
        }

        // Save if not empty
        if ($task == 'save' && $content != '') {
            $content = rtrim(stripslashes($content));
            $objTD->content = $content ? $content : $objTD->content;
            $objTD->content = \Hubzero\Utility\Sanitize::stripAll($objTD->content);

            // Save access under details
            if (strlen($objTD->content) > 255) {
                $objTD->details = $objTD->content;
            }
            $objTD->content     = \Hubzero\Utility\Str::truncate($objTD->content, 255);

            $objTD->color       = $listcolor == 'none' ? '' : $listcolor;
            $objTD->assigned_to = $assigned;
            $objTD->state       = $state;

            // Get due date
            $due = trim(Request::getString('due', ''));

            if ($due && $due != 'mm/dd/yyyy') {
                $date = explode('/', $due);
                if (count($date) == 3) {
                    $month = $date[0];
                    $day   = $date[1];
                    $year  = $date[2];
                    if (intval($month) && intval($day) && intval($year)) {
                        if (strlen($day) == 1) {
                            $day = '0' . $day;
                        }

                        if (strlen($month) == 1) {
                            $month = '0' . $month;
                        }
                        if (checkdate($month, $day, $year)) {
                            $serverTimezone = Config::get('offset');
                            $userParams = json_decode(User::get('params'), 1);
                            $timezone  = Arr::getValue($userParams, 'timezone', $serverTimezone);

                            $date = Date::of("$year-$month-$day", $timezone);
                            $objTD->duedate = $date->toSql();
                        }
                    }
                } else {
                    $this->setError(Lang::txt('PLG_PROJECTS_TODO_TODO_WRONG_DATE_FORMAT'));
                }
            } else {
                // NULL, not '': the column is a nullable DATETIME and strict SQL
                // mode refuses an empty string, which 500'd every to-do saved
                // without a due date
                $objTD->duedate = null;
            }

            // Get last order
            $lastorder = $objTD->getLastOrder($this->model->get('id'));
            $neworder = $lastorder ? (int) $lastorder + 1 : 1;
            $objTD->priority = $todoid ? $objTD->priority : $neworder;

            // Get list name
            $objTD->todolist = $listcolor == 'none'
                ? null
                : $objTD->getListName($this->model->get('id'), $objTD->color);

            // Store content
            if (!$objTD->store()) {
                $this->setError($objTD->getError());
            } else {
                $this->msg = $todoid
                    ? Lang::txt('PLG_PROJECTS_TODO_TODO_ITEM_SAVED')
                    : Lang::txt('PLG_PROJECTS_TODO_TODO_NEW_ITEM_SAVED');
            }
        } elseif ($task == 'assign') {
            // Assign todo
            $changed = $objTD->assigned_to == $assigned ? 0 : 1;
            if ($changed) {
                $objTD->assigned_to = $assigned;
                $this->mine = 0; // do not send to My Todo's list

                // Store content
                if (!$objTD->store()) {
                    $this->setError($objTD->getError());
                } else {
                    $this->msg = $mine
                        ? Lang::txt('PLG_PROJECTS_TODO_TODO_ASSIGNED_TO_MINE')
                        : Lang::txt('PLG_PROJECTS_TODO_TODO_REASSIGNED');
                }
            }
        } elseif ($task == 'changestate') {
            // Complete todo
            $changed = $objTD->state == $state ? 0 : 1;
            if ($changed) {
                $objTD->state = $state;
                if ($state == 1) {
                    $objTD->closed = Date::toSql();
                    $objTD->closed_by = $this->uid;
                }
                // Store content
                if (!$objTD->store()) {
                    $this->setError($objTD->getError());
                } else {
                    $this->msg = $state == 1
                        ? Lang::txt('PLG_PROJECTS_TODO_TODO_MARKED_COMPLETED')
                        : Lang::txt('PLG_PROJECTS_TODO_TODO_MARKED_INCOMPLETE');

                    if ($state == 1) {
                        // Record activity
                        $aid = $this->model->recordActivity(
                            Lang::txt('PLG_PROJECTS_TODO_ACTIVITY_TODO_COMPLETED'),
                            $objTD->id,
                            'to do',
                            Route::url(
                                'index.php?option='
                                    . $this->componentOption
                                    . '&alias='
                                    . $this->model->get('alias')
                                    . '&active=todo'
                                    . '&action=view&todoid='
                                    . $objTD->id,
                            ),
                            'todo',
                            1,
                        );
                    }
                }
            }
        }

        // Save new empty list information
        if ($newlist != '' && $newcolor != '') {
            $new = 0;
            $newlist = \Hubzero\Utility\Sanitize::stripAll(trim($newlist));
            if (!$objTD->getListName($this->model->get('id'), $newcolor)) {
                $objTD = new \Components\Projects\Tables\Todo($this->database);
                $objTD->created_by = $this->uid;
                $objTD->created    = Date::toSql();
                $objTD->projectid  = $this->model->get('id');
                $objTD->content    = 'provisioned';
                $objTD->state      = 2; // inactive
                $objTD->todolist   = $newlist;
                $objTD->color      = $newcolor;

                // Store content
                if (!$objTD->store()) {
                    $this->setError(Lang::txt('PLG_PROJECTS_TODO_TODO_ERROR_LIST_SAVE'));
                } else {
                    $this->msg = Lang::txt('PLG_PROJECTS_TODO_TODO_LIST_SAVED');
                }
            }
        }

        // Record activity
        if ($new) {
            $aid = $this->model->recordActivity(
                Lang::txt('PLG_PROJECTS_TODO_ACTIVITY_TODO_ADDED'),
                $objTD->id,
                'to do',
                Route::url(
                    'index.php?option='
                        . $this->componentOption
                        . '&alias='
                        . $this->model->get('alias')
                        . '&active=todo'
                        . '&action=view&todoid='
                        . $objTD->id,
                ),
                'todo',
                1,
            );
            // Store activity ID
            if ($aid) {
                $objTD->activityid = $aid;
                $objTD->store();
            }
        }

        // Set redirect path
        if ($page == 'item') {
            $url = Route::url(
                'index.php?option='
                    . $this->componentOption
                    . '&alias='
                    . $this->model->get('alias')
                    . '&active=todo'
                    . '&action=view&todoid='
                    . $objTD->id,
            );
        } else {
            $url = Route::url(
                'index.php?option='
                    . $this->componentOption
                    . '&alias='
                    . $this->model->get('alias')
                    . '&active=todo&list='
                    . $objTD->color,
            );
        }

        // Go to view
        if ($ajax) {
            $this->todoid = $todoid;
            return $page == 'item'
                ? $this->item()
                : $this->page();
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        // Redirect
        App::redirect(Route::url($url));
    }

    /**
     * Delete item
     *
     * @return  string
     */
    public function delete()
    {
        // Check for request forgeries
        Request::checkToken();

        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Incoming
        $todoid = $this->todoid;
        $list = Request::getString('dl', '');

        $gobacklist = '';

        // Load todo
        $objTD = new \Components\Projects\Tables\Todo($this->database);
        if ($todoid && $objTD->loadTodo($this->model->get('id'), $todoid)) {
            // Get associated commenting activities
            $objC = new \Components\Projects\Tables\Comment($this->database);
            $activities = $objC->collectActivities($todoid, "todo");
            $activities[] = $objTD->activityid;

            // Store list name (for redirect)
            $gobacklist = $objTD->color;

            // Delete todo
            if (!$objTD->deleteTodo($this->model->get('id'), $todoid)) {
                $this->setError(Lang::txt('PLG_PROJECTS_TODO_TODO_DELETED_ERROR'));
            } else {
                // Delete all associated comments
                $comments = $objC->deleteComments($todoid, "todo");

                // Delete all associated activities
                foreach ($activities as $a) {
                    $objAA = $this->model->table('Activity');
                    $objAA->loadActivity($a, $this->model->get('id'));
                    $objAA->deleteActivity();
                }

                $this->msg = Lang::txt('PLG_PROJECTS_TODO_TODO_DELETED');
            }
        } elseif ($list && $objTD->getListName($this->model->get('id'), $list)) {
            // Are we deleting a list?
            $deleteall = Request::getInt('all', 0);

            if ($deleteall) {
                // Get all to-do's on list
                $todos = $objTD->getTodos($this->model->get('id'), $filters = ['todolist' => $list]);
                if (count($todos) > 0) {
                    foreach ($todos as $todo) {
                        if ($objTD->loadTodo($this->model->get('id'), $todo->id)) {
                            // Get associated commenting activities
                            $objC = new \Components\Projects\Tables\Comment($this->database);
                            $activities = $objC->collectActivities($todo->id, "todo");
                            $activities[] = $objTD->activityid;

                            // Delete todo
                            if ($objTD->deleteTodo($this->model->get('id'), $todo->id)) {
                                // Delete all associated comments
                                $comments = $objC->deleteComments($todo->id, "todo");

                                // Delete all associated activities
                                foreach ($activities as $a) {
                                    $objAA = new \Components\Projects\Tables\Activity($this->database);
                                    $objAA->loadActivity($a, $this->model->get('id'));
                                    $objAA->deleteActivity();
                                }
                            }
                        }
                    }
                }
            }

            // Clean-up colored items
            $objTD->deleteList($this->model->get('id'), $list);
            $this->msg = Lang::txt('PLG_PROJECTS_TODO_TODO_LIST_DELETED');
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        // Redirect back to todo list
        $url  = Route::url($this->model->link('todo'));
        $url .= $gobacklist ? '?list=' . $gobacklist : '';
        App::redirect($url);
    }

    /**
     * Reorder items
     *
     * @return  string
     */
    public function reorder()
    {
        // Check permission
        if (!$this->model->access('content')) {
            return $this->page();
        }

        // AJAX
        // Incoming
        $newid = Request::getInt('newid', 0);
        $oldid = Request::getInt('oldid', 0);
        $items = Request::getArray('item', []);

        if ($newid && $oldid) {
            $objTD1 = new \Components\Projects\Tables\Todo($this->database);
            $objTD1->loadTodo($this->model->get('id'), $oldid);

            $objTD2 = new \Components\Projects\Tables\Todo($this->database);
            $objTD2->loadTodo($this->model->get('id'), $newid);

            $priority1 = $objTD1->priority;
            $priority2 = $objTD2->priority;

            $objTD2->priority = $priority1;
            $objTD1->priority = $priority2;

            $objTD1->store();
            $objTD2->store();
        } elseif (!empty($items)) {
            $o = 1;
            foreach ($items as $item) {
                $objTD = new \Components\Projects\Tables\Todo($this->database);
                $objTD->loadTodo($this->model->get('id'), $item);
                $objTD->priority = $o;
                $objTD->store();
                $o++;
            }
        }

        // Go back to todo list
        return $this->page();
    }

    /**
     * Delete comment
     */
    protected function deleteComment()
    {
        // Check for request forgeries
        Request::checkToken();

        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Incoming
        $cid    = Request::getInt('cid', 0);
        $todoid = $this->todoid;

        // Instantiate comment
        $objC = new \Components\Projects\Tables\Comment($this->database);

        if ($objC->load($cid)) {
            // The comment's todo must belong to the current project
            $todo = new \Components\Projects\Tables\Todo($this->database);
            $todo->loadTodo($this->model->get('id'), $objC->itemid);
            if (!$todo->id) {
                throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
            }

            // Only the author (or a project manager) may remove a comment
            if ($objC->created_by != $this->uid && !$this->model->access('manager')) {
                throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
            }

            $activityid = $objC->activityid;

            // delete comment
            if ($objC->deleteComment()) {
                $this->msg = Lang::txt('PLG_PROJECTS_TODO_COMMENT_DELETED');
            }

            // delete associated activity
            $objAA = new \Components\Projects\Tables\Activity($this->database);
            if ($activityid && $objAA->load($activityid)) {
                $objAA->deleteActivity();
            }
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        // Redirect
        App::redirect(Route::url($this->model->link('todo') . '&action=view&todoid=' . $todoid));
    }

    /**
     * Save comment
     */
    protected function saveComment()
    {
        // Check for request forgeries
        Request::checkToken();

        // Check permission
        if (!$this->model->access('content')) {
            throw new \Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Incoming
        $itemid = Request::getInt('itemid', 0, 'post');
        $comment = trim(Request::getString('comment', '', 'post'));
        $parent_activity = Request::getInt('parent_activity', 0, 'post');

        // Clean-up
        $comment = \Hubzero\Utility\Sanitize::stripScripts($comment);
        $comment = \Hubzero\Utility\Sanitize::stripImages($comment);
        $comment = \Hubzero\Utility\Str::truncate($comment, 800);

        // The todo being commented on must belong to this project
        $todo = new \Components\Projects\Tables\Todo($this->database);
        if (!$itemid || !$todo->loadTodo($this->model->get('id'), $itemid)) {
            throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
        }

        // Instantiate comment
        $objC = new \Components\Projects\Tables\Comment($this->database);
        if ($comment) {
            $objC->itemid          = $itemid;
            $objC->tbl             = 'todo';
            $objC->parent_activity = $parent_activity;
            $objC->comment         = $comment;
            $objC->created         = Date::toSql();
            $objC->created_by      = $this->uid;
            if (!$objC->store()) {
                $this->setError($objC->getError());
            } else {
                $this->msg = Lang::txt('PLG_PROJECTS_TODO_COMMENT_POSTED');
            }
            // Get new entry ID
            if (!$objC->id) {
                $objC->checkin();
            }

            // Record activity
            if ($objC->id) {
                $what = Lang::txt('COM_PROJECTS_TODO_ITEM');
                $url  = Route::url($this->model->link('todo') . '&action=view&todoid=' . $itemid);
                $aid  = $this->model->recordActivity(
                    Lang::txt('COM_PROJECTS_COMMENTED') . ' ' . Lang::txt('COM_PROJECTS_ON') . ' ' . $what,
                    $objC->id,
                    $what,
                    $url,
                    'quote',
                    0,
                );

                // Store activity ID
                if ($aid) {
                    $objC->activityid = $aid;
                    $objC->store();
                }
            }
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        // Redirect
        App::redirect(Route::url($this->model->link('todo') . '&action=view&todoid=' . $itemid));
    }
}
