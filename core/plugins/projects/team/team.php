<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Projects team
 */

namespace Plugins\Projects\Team;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

class Team extends Plugin
{
    protected $area;
    protected $config;
    /** @var \Hubzero\Database\Driver|null */
    protected $database;
    protected $task;
    protected $uid;
    protected $model;

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
     * @param   string  $alias
     * @return  array   Plugin name and title
     */
    public function &onProjectAreas($alias = null)
    {
        $area = [
            'name'    => 'team',
            'title'   => Lang::txt('PLG_PROJECTS_TEAM_TAB_TEAM'),
            'submenu' => null,
            'show'    => true,
            'icon'    => 'f042',
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
        $counts = [
            'team' => count($model->team([], true)),
        ];

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
            // Set vars
            $act = Request::getString('action', '');
            $this->task     = $action ? $action : $act;
            if ($act && ($this->task == 'edit' || $this->task == 'setup')) {
                $this->task = $act;
            }
            $this->database = App::get('db');
            $this->uid      = User::get('id');
            $this->config   = $model->config();

            switch ($this->task) {
                case 'edit':
                case 'setup':
                    $arr['html'] = $this->display(1);
                    break;

                case 'delete':
                case 'deleteit':
                    $arr['html'] = $this->delete();
                    break;

                case 'changerole':
                case 'assignrole':
                    $arr['html'] = $this->changeRole();
                    break;

                case 'changeowner':
                    $arr['html'] = $this->changeOwner();
                    break;

                case 'save':
                    $arr['html'] = $this->save();
                    break;

                case 'sync':
                    $arr['html'] = $this->sync();
                    break;

                case 'choose':
                    $arr['html'] = $this->choose();
                    break;

                case 'quit':
                    $arr['html'] = $this->quit();
                    break;

                case 'view':
                default:
                    $arr['html'] = $this->display();
                    break;

                case 'select':
                case 'newauthor':
                    $arr['html'] = $this->select();
                    break;
                case 'approvemembership':
                    $arr['html'] = $this->approveMembership();
                    break;
                case 'denymembership':
                    $arr['html'] = $this->denyMembership();
                    break;
            }
        }

        // Return data
        return $arr;
    }

    /**
     * Event call to get side content for main project page
     * Will show recent visits and who is online
     *
     * @param   object  $model
     * @return  mixed
     */
    public function onProjectMiniList($model)
    {
        if (!$model->exists() || !$model->access('content')) {
            return false;
        }

        // Get team
        $team = $model->team();

        // Display team visits only on multiple-member projects
        if (count($team) == 1) {
            return false;
        }

        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'team',
            'name'    => 'mini',
        ]);
        $view->set('team', $team);
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
        if (!$model->params->get('team_public', 0)) {
            return false;
        }

        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'team',
            'name'    => 'view',
            'layout'  => 'horizontal',
        ]);

        // Get team
        $view->set('team', $model->team($filters = ['status' => 1]));
        $view->set('model', $model);
        return $view->loadTemplate();
    }

    /**
     * View team
     *
     * @param   integer  $edit
     * @return  string
     */
    public function display($edit = 0)
    {
        $layout = $edit ? 'edit' : 'view';
        $setup  = $this->task == 'setup' ? 1 : 0;

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'team',
            'name'    => $layout,
        ]);

        $view->set('total', count($this->model->team()));

        // Instantiate project owner
        $viewFilters = $view->get('filters');
        $viewFilters['limit'] = Request::getInt('limit', Config::get('list_limit'));
        $viewFilters['start'] = Request::getInt('limitstart', 0);
        $viewFilters['sortby'] = strtolower(Request::getWord('sortby', 'name'));
        $viewFilters['sortdir'] = strtoupper(Request::getWord('sortdir', 'ASC'));
        $viewFilters['status'] = Request::getString('status', 'active');
        if (!in_array($viewFilters['sortdir'], ['ASC', 'DESC'])) {
            $viewFilters['sortdir'] = 'ASC';
        }
        if (!in_array($viewFilters['sortby'], ['group', 'added', 'date', 'role', 'name', 'status'])) {
            $viewFilters['sortby'] = 'name';
        }
        if (!$edit) {
            $viewFilters['online'] = 1;
        }
        if ($viewFilters['limit'] > $view->get('total')) {
            $viewFilters['start'] = 0;
        }
        $view->set('filters', $viewFilters);

        // Get all active team members
        $view->set('team', $this->model->team($viewFilters, true));

        // Get managers count
        $view->set('managers_count', count($this->model->table('Owner')->getIds($this->model->get('id'), $role = 1)));

        // Get count of project groups
        $groups = $this->model->table('Owner')->getProjectGroups($this->model->get('id'));

        $userId = User::getInstance()->get('id');
        $projectId = $this->model->get('id');
        $view->set('currentUser', \Components\Projects\Models\Orm\Owner::oneByProjectAndUser($projectId, $userId));
        $view->set('count_groups', $groups ? count($groups) : 0);
        $view->set('params', $this->model->params);
        $view->set('option', $this->componentOption);
        $view->set('database', $this->database);
        $view->set('model', $this->model);
        $view->set('uid', $this->uid);
        $view->set('setup', $setup);
        $view->set('config', $this->config);
        $view->set('task', $this->task);
        $view->set('edit', $edit);
        $view->set('title', $this->area['title']);

        // Get messages and errors
        $view->set('msg', $this->msg);
        $view->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Choose team from group members
     *
     * @return  string
     */
    protected function choose()
    {
        $no_html = (Request::getInt('no_html', 0) || Request::getInt('ajax', 0));

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => $this->pluginName,
            'name'    => 'edit',
            'layout'  => 'choose',
        ]);

        $view->set('filters', []);
        $viewFilters = $view->get('filters');
        $viewFilters['sortby'] = strtolower(Request::getWord('sortby', 'name'));
        $viewFilters['sortdir'] = strtoupper(Request::getWord('sortdir', 'ASC'));
        $view->set('filters', $viewFilters);
        if (!in_array($view->get('filters')['sortdir'], ['ASC', 'DESC'])) {
            $viewFilters = $view->get('filters');
            $viewFilters['sortdir'] = 'ASC';
            $view->set('filters', $viewFilters);
        }
        if (!in_array($view->get('filters')['sortby'], ['role', 'name'])) {
            $viewFilters = $view->get('filters');
            $viewFilters['sortby'] = 'name';
            $view->set('filters', $viewFilters);
        }

        $view->set('params', $this->model->params);
        $view->set('option', $this->componentOption);
        $view->set('controller', Request::getCmd('controller', 'projects'));
        $view->set('database', $this->database);
        $view->set('model', $this->model);
        $view->set('uid', $this->uid);
        $view->set('config', $this->config);
        $view->set('task', $this->task);
        $view->set('section', $this->pluginName);
        $view->set('step', 1);
        $view->set('ajax', $no_html);
        $view->setErrors($this->getErrors());

        $output = $view->loadTemplate();

        if ($no_html) {
            // We need to
            echo $output;
            exit();
        }

        return $output;
    }

    /**
     * Browser within publications NEW
     *
     * @return  string
     */
    public function select()
    {
        // Incoming
        $props  = Request::getString('p', '');
        $ajax   = Request::getInt('ajax', 0);
        $pid    = Request::getInt('pid', 0);
        $vid    = Request::getInt('vid', 0);

        // Parse props for curation
        $parts   = explode('-', $props);
        $block   = (isset($parts[0]) && in_array($parts[0], ['content', 'extras'])) ? $parts[0] : 'authors';
        $step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;

        $layout = $this->task == 'newauthor' || $this->model->isProvisioned() ? 'newauthor' : 'default';

        // Output HTML
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'projects',
            'element' => 'team',
            'name'    => 'selector',
            'layout'  => $layout,
        ]);

        $view->set('publication', new \Components\Publications\Models\Publication($pid, null, $vid));

        // The publication must belong to this project
        if (
            !$view->get('publication')->exists()
            || $view->get('publication')->get('project_id') != $this->model->get('id')
        ) {
            $this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_NO_PUBID'));

            // Output error
            $view = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'files',
                'name'    => 'error',
            ]);

            $view->set('title', '');
            $view->set('option', $this->componentOption);
            $view->setError($this->getError());
            return $view->loadTemplate();
        }

        // Set curation
        $view->get('publication')->setCuration();

        // Make sure block exists, else use default
        if (!$view->get('publication')->_curationModel->setBlock($block, $step)) {
            $block = 'authors';
        }

        // Get css
        if (!$ajax) {
            \Hubzero\Document\Assets::addPluginStylesheet('projects', 'team', 'selector');
        }

        // Instantiate project owner
        $objO = $this->model->table('Owner');
        $viewFilters = $view->get('filters');
        $viewFilters['limit'] =  0;
        $viewFilters['start'] = Request::getInt('t_limitstart', 0);
        $viewFilters['sortby'] = Request::getString('t_sortby', 'name');
        $viewFilters['sortdir'] = Request::getString('t_sortdir', 'ASC');
        $viewFilters['status'] = 'active';
        $viewFilters['pub_versionid'] = $vid;
        $view->set('filters', $viewFilters);

        // Get all active team members
        $view->set('team', $objO->getOwners($this->model->get('id'), $view->get('filters')));

        // Get current authors
        $pa = $view->get('publication')->table('Author');
        $view->set('authors', $pa->getAuthors($vid));

        // Exclude any owners?
        $view->set('exclude', []);

        $view->set('mc', Event::trigger('hubzero.onGetSingleEntry', [['members', 'uid', 'uid']]));

        $view->set('option', $this->model->isProvisioned() ? 'com_publications' : $this->componentOption);
        $view->set('database', $this->database);
        $view->set('model', $this->model);
        $view->set('uid', $this->uid);
        $view->set('ajax', $ajax);
        $view->set('task', $this->task);
        $view->set('block', $block);
        $view->set('step', $step);
        $view->set('props', $props);

        // Get messages and errors
        $view->set('msg', $this->msg);
        $view->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Save member
     */
    protected function save()
    {
        // Managing team membership is a manager/owner action
        if (!$this->model->access('manager')) {
            App::abort(403, Lang::txt('ALERTNOTAUTH'));
        }

        // Incoming
        $newm    = Request::getVar('newmember', '', 'post');
        if (is_string($newm)) {
            $newm = trim(urldecode($newm));
            $newm = explode(',', $newm);
            $newm = array_filter($newm);
        }
        $newm    = (array) $newm;
        $groups  = urldecode(trim(Request::getString('newgroup', '')));
        $role    = Request::getInt('role', 0);

        // Result collectors
        $m_added   = 0; // count of individual members added
        $m_invited = 0; // count of individuals invited
        $g_added   = 0; // count of members from new group
        $uids      = []; // ids/emails of added people
        $names     = []; // names/emails of added people
        $invalid   = []; // collector for invalid names
        $sent      = [];

        // Setup stage?
        $setup = $this->model->inSetup();

        // Get owner class
        $objO = $this->model->table('Owner');

        // Instantiate a new registration object

        $xregistration = new \Components\Members\Models\Registration();

        // Owner names not supplied
        if (empty($newm) && !$groups) {
            if (!$setup) {
                // Temporarily disabling this as it can throw a message at times we don't want one.
                // @TODO: Better error handling? Completely new UI?
                //$this->setError(Lang::txt('PLG_PROJECTS_TEAM_NO_NAMES_SUPPLIED'));
            } else {
                return;
            }
        } else {
            // Do we have new authors?
            if (!empty($newm)) {
                // This will be coming from the "choose group members" form
                //
                // Hackish and ugly but needed a way to specify that the incoming
                // authors were from a specific group
                $forgroup = Request::getInt('group_id', 0);

                for ($i = 0, $n = count($newm); $i < $n; $i++) {
                    $cid = strtolower(trim($newm[$i]));
                    $uid = 0;
                    if ($cid == '') {
                        continue;
                    }

                    $validUser = User::getInstance($cid);
                    $uid = $validUser->get('id');
                    // By ID with format of Name (ID)
                    $parts =  preg_split("/[(]/", $cid);
                    if (count($parts) == 2) {
                        $name = $parts[0];
                        $uid = preg_replace('/[)]/', '', $parts[1]);
                    } elseif (intval($cid) && $uid) {
                        // By user ID
                        $uid = $cid;
                    } elseif (!strstr($cid, ' ') && $uid) {
                        // By username
                        $uid = $validUser->get('id');
                    } else {
                        // By email
                        // Anchored at both ends: the address is stored, and a trailing
                        // quote was enough to reach the INSERT
                        $regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+$/';
                        if (preg_match($regex, $cid)) {
                            // This is an email - check if user with the email exists
                            $uid = $xregistration->getEmailId($cid);
                            if (!$uid) {
                                // Make sure we aren't inviting twice
                                $invitee = $objO->checkInvited($this->model->get('id'), $cid);
                                if (!$invitee) {
                                    // Generate invitation code
                                    $code = \Components\Projects\Helpers\Html::generateCode();

                                    // Add invitee record
                                    if ($objO->saveInvite($this->model->get('id'), $cid, $code, '', $role)) {
                                        $uids[] = $cid;
                                        $m_invited++;
                                        if (!$setup && $this->config->get('messaging') == 1) {
                                            $this->sendInviteEmail(0, $cid, $code, $role);
                                            $sent[] = $cid;
                                        }
                                    }
                                } elseif ($objO->load($invitee)) {
                                    // Previously deleted invite
                                    if ($objO->status == 2) {
                                        $objO->status = 0;
                                        $objO->role = $role;
                                        $uids[] = $cid;
                                        $objO->store();
                                        $m_invited++;
                                        if (!$setup && $this->config->get('messaging') == 1) {
                                            $this->sendInviteEmail(0, $cid, $objO->invited_code, $objO->role);
                                            $sent[] = $cid;
                                        }
                                    }
                                }
                            }
                        } else {
                            $invalid[] = $cid;
                        }
                    }

                    if (!$uid or !is_numeric($uid)) {
                        continue;
                    } else {
                        $user = User::getInstance($uid);
                        if (!$user->get('id')) {
                            $invalid[] = $uid;
                            continue;
                        }
                    }

                    // Save new author
                    $native = ($this->model->access('owner')) ? 1 : 0;
                    if (
                        $objO->saveOwners(
                            $this->model->get('id'),
                            $this->uid,
                            $uid,
                            $forgroup,
                            $role,
                            $status = 1,
                            $native,
                        )
                    ) {
                        $uids[] = $uid;
                    }
                }
            }

            if ($groups) {
                // Save new authors from group
                $g_added = $objO->saveOwners(
                    $this->model->get('id'),
                    $this->uid,
                    0,
                    $groups,
                    $role,
                    $status = 1,
                    $native = 0,
                );
                if ($objO->getError()) {
                    $this->setError($objO->getError());
                }

                if ($g_added) {
                    $uids = array_merge($uids, $g_added);
                }
            }
        }

        // Did we add anyone new?
        $uids = array_unique($uids);
        if (count($uids) > 0) {
            $this->msg = Lang::txt('PLG_PROJECTS_TEAM_SUCCESS_ADDED_OR_INVITED')
                . ' '
                . count($uids)
                . ' '
                . Lang::txt('PLG_PROJECTS_TEAM_NEW')
                . ' '
                . Lang::txt('PLG_PROJECTS_TEAM_MEMBERS');

            if (count($invalid) > 0) {
                $this->msg .= '<br />' . Lang::txt('PLG_PROJECTS_TEAM_MEMBERS_INVALID_NAMES');
            }

            if (!$setup) {
                $note  = strtolower(Lang::txt('PLG_PROJECTS_TEAM_SUCCESS_ADDED_OR_INVITED')) . ' ';
                for ($i = 0; $i < count($uids); $i++) {
                    $uu = $uids[$i];
                    if ($uu && is_numeric($uu)) {
                        $xuser = User::getInstance($uids[$i]);
                        $note .= is_numeric($uids[$i]) && is_object($xuser) ? $xuser->get('name') : $uids[$i];
                    } else {
                        $note  .= $uids[$i];
                    }

                    if ($i > 1) {
                        $left = count($uids) - 3;
                        if ($left) {
                            $note .= ' '
                                . Lang::txt('PLG_PROJECTS_TEAM_AND')
                                . ' '
                                . $left
                                . ' '
                                . Lang::txt('PLG_PROJECTS_TEAM_MORE')
                                . ' ';
                            $note .= $left == 1
                                ? Lang::txt('PLG_PROJECTS_TEAM_ACTIVITY_PERSON')
                                : Lang::txt('PLG_PROJECTS_TEAM_ACTIVITY_PERSONS');
                        }
                        break;
                    }
                    $note  .= $i == (count($uids) - 1) ? '' : ', ';
                }
                $note .= ' ' . Lang::txt('PLG_PROJECTS_TEAM_TO_PROJECT_TEAM');

                // Send out emails
                if ($this->config->get('messaging') == 1) {
                    foreach ($uids as $user) {
                        if (in_array($user, $sent)) {
                            continue;
                        }
                        $this->sendInviteEmail($user, '', '', $role);
                        $sent[] = $user;
                    }
                }
            }

            // Sync with system group
            $objO->sysGroup($this->model->get('alias'), $this->config->get('group_prefix', 'pr-'));
        } elseif (count($invalid) > 0) {
            $this->setError(
                Lang::txt('PLG_PROJECTS_TEAM_MEMBERS_INVALID_NAMES')
                    . '<br />'
                    . Lang::txt('PLG_PROJECTS_TEAM_MEMBERS_INVALID_NAMES_EXPLAIN'),
            );
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        $url = $setup
                ? Route::url($this->model->link('setup') . '&section=team')
                : Route::url($this->model->link('edit') . '&section=team');

        App::redirect($url);
    }

    /**
     * Sync group members
     */
    protected function sync()
    {
        // Pulling in group members is a manager action
        if (!$this->model->access('manager')) {
            App::abort(403, Lang::txt('ALERTNOTAUTH'));
        }

        // Setup stage?
        $setup = $this->model->inSetup();

        if ($this->model->groupOwner()) {
            // Save group sync settings
            $this->model->set('sync_group', 1);

            if (!$this->model->store()) {
                $this->setError($this->model->getError());
            } else {
                // Are we syncing group membership?
                if ($this->model->get('sync_group')) {
                    $objO = $this->model->table('Owner');
                    $objO->saveOwners(
                        $this->model->get('id'),
                        User::get('id'),
                        0,
                        $this->model->get('owned_by_group'),
                        0,
                        1,
                        1,
                        '',
                        $split_group_roles = 0,
                    );
                }
            }
        }

        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        }

        $url = $setup
                ? Route::url($this->model->link('setup') . '&section=team')
                : Route::url($this->model->link('edit') . '&section=team');

        App::redirect($url);
    }

    /**
     * Delete member(s)
     *
     * @return  mixed
     */
    protected function delete()
    {
        // Managing team membership is a manager/owner action
        if (!$this->model->access('manager')) {
            App::abort(403, Lang::txt('ALERTNOTAUTH'));
        }

        // Incoming
        $checked = Request::getArray('owner', []);
        $groups  = Request::getArray('group', []);

        // Are we setting up project?
        $setup = $this->model->inSetup() ? 1 : 0;

        // Instantiate project owner
        $objO = $this->model->table('Owner');

        if (!empty($checked)) {
            // Get owners info from owner id(s)
            $selected = $objO->getInfo($this->model->get('id'), $checked, $groups);
        } else {
            $selected = [];
            $this->setError(Lang::txt('PLG_PROJECTS_TEAM_ERROR_NOONE_TO_DELETE'));
        }

        if ($this->task == 'delete') {
            // Output HTML
            $view = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'team',
                'name'    => 'delete',
            ]);

            $view->set('selected', $selected);
            $view->set('checked', $checked);
            $view->set('option', $this->componentOption);
            $view->set('model', $this->model);
            $view->set('uid', $this->uid);
            $view->set('setup', $setup);
            $view->set('aid', $objO->getOwnerID($this->model->get('id'), $this->uid));
            $view->set('msg', isset($this->msg) ? $this->msg : '');
            $view->setErrors($this->getErrors());

            return $view->loadTemplate();
        } else {
            // Get all managers
            $all = $objO->getIds($this->model->get('id'), $role = 1);
            $remaining = array_diff($all, $checked);
            $deleted = 0;

            // Cannot delete if no managers remain
            if ($remaining) {
                // Perform delete
                $deleted = $objO->removeOwners($this->model->get('id'), $checked, 1);
                if ($deleted) {
                    $this->msg = Lang::txt('PLG_PROJECTS_TEAM_OWNERS_DELETED');
                }
            } else {
                if (count($all) > 0) {
                    $left = array_diff($checked, [$all[0]]); // leave one manager
                    $deleted = $objO->removeOwners($this->model->get('id'), $left, 1);
                }
                $this->setError(Lang::txt('PLG_PROJECTS_TEAM_OWNERS_DELETE_NOMANAGERS'));
            }

            if ($deleted) {
                // Sync with system group
                $objO->sysGroup($this->model->get('alias'), $this->config->get('group_prefix', 'pr-'));
            }

            // Pass error or success message
            if ($this->getError()) {
                Notify::message($this->getError(), 'error', 'projects');
            } elseif (!empty($this->msg)) {
                Notify::message($this->msg, 'success', 'projects');
            }

            $url  = 'index.php?option=' . $this->componentOption . '&alias=' . $this->model->get('alias') . '&task=';
            $url .= $setup ? 'setup' : 'edit';
            $url .= '&active=team';

            App::redirect(Route::url($url));
        }
    }

    /**
     * Approve membership request
     *
     * @return mixed
     */
    protected function approveMembership()
    {
        // Both Approve buttons post the token
        Request::checkToken();
        $projectId = $this->model->get('id');
        $userId = User::getInstance()->get('id');
        $currentUser = \Components\Projects\Models\Orm\Owner::oneByProjectAndUser($projectId, $userId);
        $ownerId = Request::getInt('owner', 0);
        if ($currentUser->isManager() && $ownerId != 0) {
            $owner = \Components\Projects\Models\Orm\Owner::oneByProjectAndUser($projectId, $ownerId);
            $owner->set('status', 1);
            $owner->set('added', Date::of()->toSql());
            if ($owner->save()) {
                $ownerEmail = [];
                $ownerEmail[] = $owner->user->get('email');
                $subject = Lang::txt('COM_PROJECTS_EMAIL_MEMBERSHIPREQUEST_ACCEPTED');
                $message = '';

                $this->sendMemberRequestEmail($ownerEmail, $subject, $message);
                $url = Route::url('index.php?option=' . $this->componentOption
                    . '&task=team' . '&alias=' . $this->model->get('alias'), false);
                Notify::success(Lang::txt('PLG_PROJECTS_TEAM_MEMBERSHIP_APPROVED'));
                App::redirect($url);
            }
        } else {
            App::abort(403, Lang::txt('PLG_PROJECTS_TEAM_NOT_PERMITTED'));
        }
    }

    /**
     *  Deny membership request
     *
     * @return mixed
     */
    protected function denyMembership()
    {
        $projectId = $this->model->get('id');
        $userId = User::getInstance()->get('id');
        $currentUser = \Components\Projects\Models\Orm\Owner::oneByProjectAndUser($projectId, $userId);
        $ownerId = Request::getInt('owner', 0);
        $confirm = Request::getInt('confirm', 0);
        if ($currentUser->isManager() && $ownerId != 0) {
            $owner = \Components\Projects\Models\Orm\Owner::oneByProjectAndUser($projectId, $ownerId);
            if ($confirm != 1) {
                $view = new \Hubzero\Plugin\View([
                    'folder'  => 'projects',
                    'element' => 'team',
                    'name'    => 'memberrequest',
                ]);

                $view->set('option', $this->componentOption);
                $view->set('model', $this->model);
                $view->set('owner', $owner);
                $view->setErrors($this->getErrors());

                return $view->loadTemplate();
            }

            // The denial itself must be posted
            if (Request::method() !== 'POST') {
                App::abort(405, Lang::txt('JERROR_METHOD_NOT_ALLOWED'));
            }

            Request::checkToken();

            $owner->set('status', 4);
            $message = Request::getString('message');
            $params = $owner->params;
            if ($message) {
                $params->set('denyMessage', $message);
            }
            $owner->set('params', $params->toString());
            if ($owner->save()) {
                $ownerEmail = [];
                $ownerEmail[] = $owner->user->get('email');
                $subject = Lang::txt('PLG_PROJECTS_TEAM_MEMBERSHIP_DENIED');
                $message = $owner->params->get('denyMessage');

                $this->sendMemberRequestEmail($ownerEmail, $subject, $message);
                Notify::success('Membership request has been denied');
                $url = Route::url('index.php?option=' . $this->componentOption
                    . '&task=team' . '&alias=' . $this->model->get('alias'), false);
                App::redirect($url);
            }
        } else {
            App::abort(403, Lang::txt('PLG_PROJECTS_TEAM_NOT_PERMITTED'));
        }
    }

    /** @return void */
    private function sendMemberRequestEmail($email, $subject, $message)
    {
        \Components\Projects\Helpers\Html::sendHUBMessage(
            $this->componentOption,
            $this->model,
            $email,
            $subject,
            'projects_project_membershiprequest',
            'memberrequest',
            $message,
        );
    }

    /**
     * Change project owner
     *
     * @return  mixed
     */
    protected function changeOwner()
    {
        // Role and ownership changes are manager actions
        if (!$this->model->access('manager')) {
            App::abort(403, Lang::txt('ALERTNOTAUTH'));
        }
        // Incoming
        $confirm = Request::getInt('confirm', 0, 'post');
        $user    = Request::getInt('owned_by_user', $this->model->get('owned_by_user'), 'post');
        $group   = Request::getInt('owned_by_group', 0, 'post');

        if ($confirm) {
            // The group must be one the owner belongs to (the choices the form offers)
            if ($group && $group != $this->model->get('owned_by_group')) {
                $allowed = [];
                foreach (\Hubzero\User\Helper::getGroups($user, 'members', 1) as $g) {
                    $allowed[] = $g->gidNumber;
                }
                if (!in_array($group, $allowed)) {
                    App::abort(403, Lang::txt('ALERTNOTAUTH'));
                }
            }

            // Load project owner table class
            $objO = $this->model->table('Owner');
            $objO->loadOwner($this->model->get('id'), $user);

            if (!$objO->id) {
                App::abort(404, Lang::txt('Error loading user'));
            }

            // Change in individual ownership
            if ($user != $this->model->get('owned_by_user')) {
                $this->model->set('owned_by_user', $user);
                $this->model->store();

                // Make sure user is manager
                $objO->role = 1;
                $objO->store();
            }

            // Change in group ownership
            if ($group != $this->model->get('owned_by_group')) {
                $this->model->set('owned_by_group', $group);
                if (!$group) {
                    $this->model->set('sync_group', 0);
                }
                $this->model->store();

                // Make sure project lead is affiliated with group
                $objO->groupid = $group;
                $objO->store();
            }

            $this->msg = Lang::txt('PLG_PROJECTS_TEAM_OWNERSHIP_UPDATED');
        } else {
            // Output HTML
            $view = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'team',
                'name'    => 'changeowner',
            ]);

            // Get groups project owner belongs to
            $view->set('groups', \Hubzero\User\Helper::getGroups($this->model->get('owned_by_user'), 'members', 1));
            if ($this->model->groupOwner()) {
                $viewGroups = $view->get('groups');
                $viewGroups[] = $this->model->groupOwner();
                $view->set('groups', $viewGroups);
            }

            $view->set('option', $this->componentOption);
            $view->set('model', $this->model);
            $view->set('msg', isset($this->msg) ? $this->msg : '');
            $view->set('title', $this->area['title']);
            if ($this->getError()) {
                $view->setError($this->getError());
            }
            return $view->loadTemplate();
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        App::redirect(Route::url($this->model->link('edit') . '&section=team'));
    }

    /**
     * Quit project
     *
     * @return  mixed
     */
    protected function quit()
    {
        // Incoming
        $confirm = Request::getInt('confirm', 0, 'post');

        // Instantiate project owner
        $objO = $this->model->table('Owner');

        // Check to make sure we are not deleting last manager
        $onlymanager = 0;
        if ($this->model->access('manager')) {
            $managers = $objO->getIds($this->model->get('id'), $role = 1);
            if (count($managers) == 1) {
                $onlymanager = 1;
            }
        }

        // Remove member from team if not owner & other managers exist
        if ($confirm && !$onlymanager && !$this->model->access('owner')) {
            // Check for request forgeries
            Request::checkToken();

            $deleted = $objO->removeOwners($this->model->get('id'), [$this->uid]);
            if ($deleted) {
                $this->msg = Lang::txt('PLG_PROJECTS_TEAM_MEMBER_QUIT_SUCCESS');

                // Record activity
                $aid = $this->model->recordActivity(
                    Lang::txt('PLG_PROJECTS_TEAM_PROJECT_QUIT'),
                    0,
                    '',
                    '',
                    'team',
                    0,
                );

                // Sync with system group
                $objO->sysGroup($this->model->get('alias'), $this->model->config('group_prefix', 'pr-'));
            }
        } else {
            // Output HTML
            $view = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'team',
                'name'    => 'quit',
            ]);

            $view->set('group', $this->model->groupOwner('id'));
            $view->set('onlymanager', $onlymanager);
            $view->set('option', $this->componentOption);
            $view->set('database', $this->database);
            $view->set('model', $this->model);
            $view->set('uid', $this->uid);
            $view->set('config', $this->config);
            $view->set('msg', isset($this->msg) ? $this->msg : '');
            $view->set('title', $this->area['title']);
            if ($this->getError()) {
                $view->setError($this->getError());
            }
            return $view->loadTemplate();
        }

        // Pass error or success message
        if ($this->getError()) {
            Notify::message($this->getError(), 'error', 'projects');
        } elseif (!empty($this->msg)) {
            Notify::message($this->msg, 'success', 'projects');
        }

        App::redirect(Route::url('index.php?option=' . $this->componentOption));
    }

    /**
     * Change member role
     *
     * @return  string
     */
    protected function changeRole()
    {
        // Role and ownership changes are manager actions
        if (!$this->model->access('manager')) {
            App::abort(403, Lang::txt('ALERTNOTAUTH'));
        }
        // Incoming
        $checked = Request::getArray('owner', []);
        $groups  = Request::getArray('group', []);
        $owner   = Request::getString('owner', '');
        $role    = Request::getInt('role', 0);

        if ($owner) {
            $checked = [$owner];
        }

        // Are we setting up project?
        $s = $this->model->inSetup() ? 1 : 0;

        // Instantiate project owner
        $objO = $this->model->table('Owner');

        if (!empty($checked)) {
            // Get owners info from owner id(s)
            $selected = $objO->getInfo($this->model->get('id'), $checked, $groups);
        } else {
            $selected = [];
            $this->setError(Lang::txt('PLG_PROJECTS_TEAM_ERROR_NOONE_TO_REASSIGN_ROLE'));
        }

        if ($this->task == 'changerole') {
            // Output HTML
            $view = new \Hubzero\Plugin\View([
                'folder'  => 'projects',
                'element' => 'team',
                'name'    => 'role',
            ]);

            $view->set('selected', $selected);
            $view->set('checked', $checked);
            $view->set('option', $this->componentOption);
            $view->set('model', $this->model);
            $view->set('uid', $this->uid);
            $view->set('aid', $objO->getOwnerID($this->model->get('id'), $this->uid));
            $view->set('msg', isset($this->msg) ? $this->msg : '');
            if ($this->getError()) {
                $view->setError($this->getError());
            }
            return $view->loadTemplate();
        } else {
            // Changing role(s) to collaborator
            $left = $checked;
            if ($role == 0) {
                // Get all managers
                $all = $objO->getIds($this->model->get('id'), 1);
                $remaining = array_diff($all, $checked);
                if (!$remaining && count($all) > 0) {
                    $left = array_diff($checked, [$all[0]]); // leave one manager
                    $this->setError(Lang::txt('PLG_PROJECTS_TEAM_OWNERS_REASSIGN_NOMANAGERS'));
                }
            }

            if ($objO->reassignRole($this->model->get('id'), $left, 1, $role)) {
                $this->msg = Lang::txt('PLG_PROJECTS_TEAM_OWNERS_ROLE_CHANGED');

                // Sync with system group
                $objO->sysGroup($this->model->get('alias'), $this->config->get('group_prefix', 'pr-'));
            }

            $this->task = $s ? 'setup' : 'edit';

            return $this->display($edit = 1);
        }
    }

    /**
     * Send invite email
     *
     * @param   integer  $uid
     * @param   string   $email
     * @param   string   $code
     * @param   integer  $role
     * @param   object|string $model
     * @param   string   $option
     * @return  boolean  True on success
     */
    public function sendInviteEmail($uid = 0, $email = '', $code = '', $role = 0, $model = '', $option = 'com_projects')
    {
        $uid   = $uid ? $uid : 0;
        if (!$email && $uid) {
            $email = User::getInstance($uid)->get('email');
        }
        //$email = $email ? $email : User::get('email');

        if (!$email || (!$uid && !$code)) {
            return false;
        }

        $option = $option ? $option : $this->componentOption;
        $model  = $model ? $model : $this->model;

        if (!$model->exists()) {
            return false;
        }

        $database = App::get('db');

        // Validate email
        $regex = '/^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-]+)+/';
        if (!preg_match($regex, $email)) {
            return false;
        }

        // Set up email config
        $from = [
            'name'      => Config::get('sitename') . ' ' . Lang::txt(strtoupper($option)),
            'email'     => Config::get('mailfrom'),
            'multipart' => md5(date('U')),
        ];

        // Email message subject
        if ($model->isProvisioned()) {
            $objPub = new \Components\Publications\Tables\Publication($database);
            $pub = $objPub->getProvPublication($model->get('id'));

            if (!$pub || !$pub->id) {
                return false;
            }

            $subject = $uid
                    ? Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_ADDED_PROV')
                    : Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_INVITE_PROV');
        } else {
            $subject = $uid
                    ? Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_ADDED') . ' ' . $model->get('alias')
                    : Lang::txt('COM_PROJECTS_EMAIL_SUBJECT_INVITE') . ' ' . $model->get('alias');
        }

        // Message body
        $eview = new \Hubzero\Mail\View([
            'base_path' => Component::path('com_projects') . DS . 'site',
            'name'      => 'emails',
            'layout'    => 'invite_plain',
        ]);

        $eview->set('option', $option);
        $eview->set('project', $model);
        $eview->set('code', $code);
        $eview->set('email', $email);
        $eview->set('uid', $uid);
        $eview->set('role', $role);
        $eview->set('pub', isset($pub) ? $pub : '');

        $message['plaintext'] = $eview->loadTemplate(false);
        $message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

        // HTML email
        $eview->setLayout('invite_html');
        $message['multipart'] = $eview->loadTemplate();
        $message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

        if ($uid != 0 && $uid != false) {
            // Send HUB message
            $recipient = User::getInstance($uid);

            $email = new \Hubzero\Mail\Message();
            $email
                ->setSubject($subject)
                ->addFrom($from['email'], $from['name'])
                ->addPart($message['plaintext'], 'text/plain')
                ->addPart($message['multipart'], 'text/html')
                ->setTo($recipient->email, $recipient->name);

            if ($email->send()) {
                return true;
            }
        } else {
            if (
                \Components\Projects\Helpers\Html::email(
                    $email,
                    Config::get('sitename') . ': ' . $subject,
                    $message,
                    $from,
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
