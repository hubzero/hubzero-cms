<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site\Controllers;

use Hubzero\Config\Registry;
use Hubzero\User\Group;
use Hubzero\Utility\Sanitize;
use Components\Groups\Models\Page;
use Components\Groups\Helpers;
use Components\Groups\Models\Tags;
use Components\Groups\Models\Log;
use Components\Groups\Models\Recent;
use Components\Groups\Models\Orm\Field;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Event;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\Session;

/**
 * Groups controller class
 *
 * @property  \Hubzero\User\Group  $group  The unsaved group saveTask() hands back to editTask()
 * @property  string               $tags   The unsaved tags saveTask() hands back to editTask()
 * @property  int                  $lid    The upload folder id saveTask() hands back to editTask()
 */
class Groups extends Base
{
    /**
     * Override Execute Method
     *
     * @return  void
     */
    public function execute()
    {
        // Get the cname, active tab, and action for plugins
        $this->cn     = Request::getString('cn', '');
        $this->active = Request::getCmd('active', '');
        $this->action = Request::getCmd('action', '');
        $this->task   = Request::getCmd('task', '');

        // Handles misrouted request
        if ($this->task == 'pages') {
            App::redirect(Route::url('index.php?option=' . $this->option . '&cn=' . $this->cn . '&controller=pages'));
        }

        // Are we serving up a file
        $uri = $_SERVER['REQUEST_URI'];
        if (strstr($uri, 'Image:')) {
            $file = strstr($uri, 'Image:');
        } elseif (strstr($uri, 'File:')) {
            $file = strstr($uri, 'File:');
        }

        // If we have a file
        if (isset($file)) {
            $this->downloadTask($file);

            return;
        }

        // Check in for user
        Helpers\Pages::checkinForUser();

        // Continue with parent execute method
        parent::execute();
    }

    /**
     * Intro Page
     *
     * @return  void
     */
    public function displayTask()
    {
        // Build the title
        $this->buildTitle();

        // Build pathway
        $this->buildPathway();

        // Vars
        $mytags = '';
        $this->view->set('mygroups', [
            'members'    => null,
            'invitees'   => null,
            'applicants' => null,
        ]);
        $this->view->set('populargroups', []);
        $this->view->set('interestinggroups', []);

        // If we have a users profile load their groups and groups matching their tags
        if (!User::isGuest()) {
            // Get users tags

            $mt = new \Components\Members\Models\Tags(User::get('id'));
            $mytags = $mt->render('string');

            // Get users groups
            $viewMygroups = $this->view->get('mygroups');
            $viewMygroups['members'] = \Hubzero\User\Helper::getGroups(User::get('id'), 'members', 1);
            $this->view->set('mygroups', $viewMygroups);
            $viewMygroups = $this->view->get('mygroups');
            $viewMygroups['invitees'] = \Hubzero\User\Helper::getGroups(User::get('id'), 'invitees', 1);
            $this->view->set('mygroups', $viewMygroups);
            $this->view->mygroups['applicants'] = \Hubzero\User\Helper::getGroups(User::get('id'), 'applicants', 1);
            $this->view->mygroups = array_filter($this->view->mygroups);

            // Get groups user may be interested in
            $this->view->set('interestinggroups', Group\Helper::getGroupsMatchingTagString(
                $mytags,
                \Hubzero\User\Helper::getGroups(User::get('id')),
            ));
        }

        // Get the popular groups
        $this->view->set('populargroups', Group\Helper::getPopularGroups(3));

        // Get featured groups
        $this->view->set('featuredgroups', Group\Helper::getFeaturedGroups(
            $this->config->get('intro_featuredgroups_list', ''),
        ));

        // Set some vars for view
        $this->view->set('config', $this->config);
        $this->view->set('title', $this->_title);

        // Get view notifications
        $this->view->set('notifications', ($this->getNotifications()) ? $this->getNotifications() : []);

        // Display view
        $this->view
            ->setLayout('display')
            ->display();
    }

    /**
     * Browse Groups
     *
     * @return  void
     */
    public function browseTask()
    {
        // Build the title
        $this->buildTitle();

        // Build pathway
        $this->buildPathway();

        // Build list of filters
        $filters = [
            'type'      => [1, 3],
            'published' => Request::getInt('published', 1),
            'limit'     => 'all',
            'fields'    => ['COUNT(*)'],
            'search'    => Request::getString('search', ''),
            'sortby'    => strtolower(Request::getWord('sortby', 'title')),
            'policy'    => strtolower(Request::getWord('policy', '')),
            'index'     => htmlentities(Request::getString('index', '')),
        ];

        if (!in_array($filters['published'], [1, 2])) {
            $filters['published'] = 1;
        }

        // Make sure we have a valid sort filter
        if (!in_array($filters['sortby'], ['alias', 'title'])) {
            $filters['sortby'] = 'title';
        }

        // Make sure we have a valid policy filter
        if (!in_array($filters['policy'], ['open', 'restricted', 'invite', 'closed'])) {
            $filters['policy'] = '';
        }

        // Get a record count
        $total = Group::find($filters);

        // Filters for returning results
        $filters['limit']  = Request::getInt('limit', Config::get('list_limit'));
        $filters['limit']  = ($filters['limit']) ? $filters['limit'] : 'all';
        $filters['start']  = Request::getInt('limitstart', 0);
        $filters['fields'] = ['cn', 'description', 'published', 'gidNumber', 'type', 'public_desc', 'join_policy'];

        // Get a list of all groups
        $groups = Group::find($filters);

        // Get view notifications
        $notifications = ($this->getNotifications()) ? $this->getNotifications() : [];

        // Display view
        $this->view
            ->set('total', $total)
            ->set('groups', $groups)
            ->set('filters', $filters)
            ->set('title', $this->_title)
            ->set('authorized', $this->authorize())
            ->set('notifications', $notifications)
            ->setLayout('browse')
            ->display();
    }

    /**
     * View Group
     *
     * @return  void
     */
    public function viewTask()
    {
        // Set the needed layout
        $this->view->setLayout('view');

        // Validate the incoming cname
        if (!$this->validCn($this->cn, true)) {
            $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
        }

        // Load the group object
        $this->view->set('group', Group::getInstance($this->cn));

        // Check to make sure we were able to load group
        if (
            !is_object($this->view->get('group'))
            || !$this->view->get('group')->get('gidNumber')
            || !$this->view->get('group')->get('cn')
        ) {
            $this->suggestNonExistingGroupTask();
            return;
        }

        // A group may set its own site template. Guarded on is_object rather
        // than relying on the not-found check above still sitting in front of
        // it: Group::getInstance() returns false for an unknown cn, and this
        // block has been moved above that check before now, which turns every
        // hit on a missing group into a warning in the error log.
        if (is_object($this->view->get('group'))) {
            $gparams       = new Registry($this->view->get('group')->get('params'));
            $page_template = $gparams->get('page_template');

            if ($page_template) {
                App::get('template')->template = $page_template;
            }
        }

        // Ensure it's an allowable group type to display
        if (!in_array($this->view->get('group')->get('type'), [1, 3])) {
            $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
        }

        // Ensure the group is published
        if (!$this->view->get('group')->get('published')) {
            $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
        }

        // Ensure the group has been published or has been approved
        if (!$this->view->get('group')->get('approved')) {
            // Get list of members & managers & invitees
            $managers = $this->view->get('group')->get('managers');
            $members  = $this->view->get('group')->get('members');
            $invitees = $this->view->get('group')->get('invitees');
            $members_invitees = array_merge($members, $invitees);
            $managers_members_invitees = array_merge($managers, $members, $invitees);

            // If user is not member, manager, or invitee deny access
            if (!in_array(User::get('id'), $managers_members_invitees)) {
                $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
            }

            // If user is NOT manager but member or invitee
            if (!in_array(User::get('id'), $managers) && in_array(User::get('id'), $members_invitees)) {
                $this->unapprovedGroupTask();

                return;
            }

            // Set notification and clear after
            $this->setNotification(Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'), 'warning');
        }

        // Get the group params
        $this->view->set('gparams', new \Hubzero\Config\Registry($this->view->get('group')->get('params')));

        // Check authorization
        $this->view->set('authorized', Helpers\View::authorize($this->view->get('group')));

        // Get active tab
        $this->view->set('tab', Helpers\View::getTab($this->view->get('group')));
        $this->view->set('trueTab', strtolower(Request::getString('active', 'overview')));
        if ($this->view->get('group')->get('approved') != 1 && $this->view->get('trueTab') != 'overview') {
            $this->unapprovedGroupTask();

            return;
        }

        // Record the user
        if (!User::isGuest() && in_array(User::get('id'), $this->view->get('group')->get('members'))) {
            Recent::hit(User::get('id'), $this->view->get('group')->get('gidNumber'));
        }

        // Get group pages if any
        $pageArchive = Page\Archive::getInstance();
        $pages = $pageArchive->pages('list', [
            'gidNumber' => $this->view->get('group')->get('gidNumber'),
            'state'     => [0,1],
            'orderby'   => 'lft ASC',
        ]);

        // Custom error handling for super groups
        Helpers\View::attachCustomErrorHandler($this->view->get('group'));

        // Add the overview content
        $overviewContent = '';
        $activePage      = null;
        if ($this->view->get('tab') == 'overview') {
            // Add home page to pages list
            $pages = Helpers\Pages::addHomePage($this->view->get('group'), $pages);

            // Fetch the active page
            $activePage = Helpers\Pages::getActivePage($this->view->get('group'), $pages);

            // Are we on the login?
            if ($this->view->get('trueTab') == 'login') {
                $overviewContent = Helpers\View::superGroupLogin($this->view->get('group'));
            }

            // Check to see if we have super group component or PHP page
            if (
                $overviewContent == null
                && $this->config->get('super_components', 0)
            ) {
                $overviewContent = Helpers\View::superGroupComponents(
                    $this->view->get('group'),
                    $this->view->get('trueTab'),
                );
            }

            // Do we have group PHP pages?
            if ($overviewContent == null) {
                $overviewContent = Helpers\View::superGroupPhpPages($this->view->get('group'));
            }

            // Set overview content
            if ($overviewContent == null) {
                $overviewContent = Helpers\Pages::displayPage($this->view->get('group'), $activePage);
            }
        }

        // Build the title
        $this->buildTitle($pages);

        // Build pathway
        $this->buildPathway($pages);

        // Set some vars for view
        $this->view->set('title', $this->_title);
        $this->view->set('content', Helpers\View::displaySectionsContent($this->view->get('group'), $overviewContent));
        $this->view->set('activePage', $activePage);
        $this->view->set('notifications', ($this->getNotifications()) ? $this->getNotifications() : []);

        // Check session if this is a newly submitted entry. Trigger a proper event if so.
        if (Session::get('newsubmission.group')) {
            // Unset the new submission session flag
            Session::set('newsubmission.group');
            Event::trigger('content.onAfterContentSubmission', ['Group']);
        }

        // Is this a super group?
        if ($this->view->get('group')->isSuperGroup()) {
            // Use group template file if we have it
            Request::setVar('tmpl', 'group');

            // Must call here cause otherwise doesnt load template
            $this->view->css()->js();

            // Load super group template
            // parse & render
            $superGroupTemplate = new Helpers\Template();
            $superGroupTemplate
                ->set('group', $this->view->get('group'))
                ->set('tab', $this->view->get('trueTab'))
                ->set('content', $this->view->get('content'))
                ->set('page', $this->view->get('activePage'))
                ->parse()
                ->render();

            // Echo content & stop execution
            return $superGroupTemplate->output(true);
        }

        // Display view
        $this->view->display();
    }

    /**
     *  Show add group
     *
     * @return  void
     */
    public function newTask()
    {
        if (!User::authorise('core.create', $this->option)) {
            App::redirect(
                Route::url('index.php?option=' . $this->option),
                Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'),
                'warning',
            );
        }

        $this->editTask();
    }

    /**
     *  Show group edit
     *
     * @return  void
     */
    public function editTask()
    {
        // set the neeced layout
        $this->view->setLayout('edit');

        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask(Lang::txt('COM_GROUPS_CREATE_MUST_BE_LOGGED_IN'));

            return;
        }

        if (!User::authorise('core.edit', $this->option)) {
            App::redirect(
                Route::url('index.php?option=' . $this->option),
                Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'),
                'warning',
            );
        }

        // Are we creating a new group?
        if ($this->task == 'new') {
            // Instantiate an Group object
            $this->view->set('group', new Group());

            // set some group vars for view
            $this->view->get('group')->set('cn', Request::getString('suggested_cn', ''));
            $this->view->get('group')->set('join_policy', $this->config->get('join_policy'));
            $this->view->get('group')->set('discoverability', $this->config->get('discoverability', 0));
            $this->view->get('group')->set('discussion_email_autosubscribe', null);

            $this->view->set('tags', '');

            // Set title
            $this->view->set('title', Lang::txt('COM_GROUPS_NEW_TITLE'));
        } else {
            // Check to make sure we have cname
            if (!$this->cn) {
                $this->errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
            }

            // Load the group page
            $this->view->set('group', Group::getInstance($this->cn));

            // Ensure we found the group info
            if (!$this->view->get('group') || !$this->view->get('group')->get('gidNumber')) {
                $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
            }

            // Check authorization
            // Published = 2 = archived. Archived is a read-only mode.
            if (
                $this->view->get('group')->published == 2
                || ($this->authorize() != 'manager' && !$this->authorizedForTask('group.edit'))
            ) {
                $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
            }

            // Get the group's interests (tags)
            $gt = new Tags($this->view->get('group')->get('gidNumber'));
            $this->view->set('tags', $gt->render('string'));

            // Set title
            $this->view->set(
                'title',
                Lang::txt('COM_GROUPS_EDIT_TITLE', $this->view->get('group')->get('description')),
            );
        }

        // Create dir for uploads
        $this->view->set('lid', time() . rand(0, 1000));
        if ($this->lid != '') {
            $this->view->set('lid', $this->lid);
        } elseif ($this->view->get('group')->get('gidNumber')) {
            $this->view->set('lid', $this->view->get('group')->get('gidNumber'));
        }

        // Are we passing a group from save method
        if ($this->group) {
            $this->view->set('group', $this->group);
            $this->view->set('tags', $this->tags);
        }

        // Path to group assets
        $asset_path = PATH_APP
            . DS
            . trim($this->config->get('uploadpath', '/site/groups'), DS)
            . DS
            . $this->view->get('lid')
            . DS
            . 'uploads';

        // If path is a directory then load images
        $this->view->set('logos', []);
        if (is_dir($asset_path)) {
            // Get all images that are in group asset folder and could be a possible group logo
            $this->view->set('logos', Filesystem::files(
                $asset_path,
                '.jpg|.jpeg|.png|.gif|.svg|.PNG|.JPG|.JPEG|.GIF|.SVG',
                true,
                true,
            ));
        }

        // Trigger the functions that return the areas we'll be using
        // then add overview to array
        $hub_group_plugins = Event::trigger('groups.onGroupAreas', []);
        array_unshift($hub_group_plugins, [
            'name'             => 'overview',
            'title'            => 'Overview',
            'default_access'   => 'anyone',
            'display_menu_tab' => true,
        ]);
        $this->view->set('hub_group_plugins', $hub_group_plugins);

        // Get plugin access
        $this->view->set('group_plugin_access', Group\Helper::getPluginAccess($this->view->get('group')));

        // Build the title
        $this->buildTitle();

        // Build pathway
        $this->buildPathway();

        $this->view->task = $this->task;
        $this->view->set('config', $this->config);

        // Get view notifications
        $this->view->set('notifications', ($this->getNotifications()) ? $this->getNotifications() : []);
        $this->view->set('customFields', Field::all()->ordered('ordering'));
        $customAnswers = [];
        foreach ($this->view->get('customFields') as $field) {
            $fieldName = $field->get('name');
            $customAnswers[$fieldName] = $field->collectGroupAnswers($this->view->get('group')->get('gidNumber'));
        }
        $this->view->set('customAnswers', $customAnswers);
        // Display view
        $this->view->display();
    }

    /**
     *  Save group settings
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask(Lang::txt('COM_GROUPS_CREATE_MUST_BE_LOGGED_IN'));

            return;
        }

        Request::checkToken();

        // Incoming
        $g_gidNumber = Request::getInt('gidNumber', 0, 'post');
        $c_gidNumber = Request::getString('gidNumber', 0, 'post');
        if ((string) $g_gidNumber !== (string) $c_gidNumber) {
            App::abort(404, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
        }

        if (
            (!$g_gidNumber && !User::authorise('core.create', $this->option))
            || ($g_gidNumber && !User::authorise('core.edit', $this->option))
        ) {
            App::redirect(
                Route::url('index.php?option=' . $this->option),
                Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'),
                'warning',
            );
        }

        $g_cn              = trim(Request::getString('cn', '', 'post'));
        $g_description     = preg_replace(
            '/\s+/',
            ' ',
            trim(strip_tags(Request::getString('description', Lang::txt('NONE'), 'post'))),
        );
        $g_discoverability = Request::getInt('discoverability', 0, 'post');
        $g_public_desc     = Sanitize::clean(trim(Request::getString('public_desc', '', 'post')));
        $g_private_desc    = Sanitize::clean(trim(Request::getString('private_desc', '', 'post')));
        $g_restrict_msg    = Sanitize::html(trim(Request::getString('restrict_msg', '', 'post')));
        $g_join_policy     = Request::getInt('join_policy', 0, 'post');
        $tags              = trim(Request::getString('tags', ''));
        $lid               = Request::getInt('lid', 0, 'post');
        $customization     = Request::getArray('group', [], 'POST');
        $plugins           = Request::getArray('group_plugin', '', 'POST');
        $params            = Request::getArray('params', [], 'POST');

        $g_discussion_email_autosubscribe = Request::getInt('discussion_email_autosubscribe', 0, 'post');

        // Are we editing or creating?
        if ($g_gidNumber) {
            $group = Group::getInstance($g_gidNumber);
            $this->task = 'edit';

            // authorize() and _authorizedForTask() read $this->cn, which is
            // whatever cn the request carried; the group being written is the
            // posted gidNumber. Authorise against that group.
            if (!$group || !$group->get('gidNumber')) {
                App::abort(404, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
            }
            $this->cn = $group->get('cn');
            // Snapshot the pre-save state for groups.onGroupAfterSave listeners.
            // Group::getInstance() is a singleton cache, so a second call would
            // return the *same* instance as $group and reflect the post-save
            // values; clone it instead, matching the admin controllers.
            $before = clone($group);
        } else {
            $this->task = 'new';
            $group  = new Group();
            $before = new Group();
        }

        // Check authorization
        // Published = 2 = archived. Archived is a read-only mode.
        if (
            $group->published == 2
            || ($this->authorize() != 'manager' && $g_gidNumber != 0 && !$this->authorizedForTask('group.edit'))
        ) {
            $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
        }

        // Check for any missing info
        if (!$g_cn) {
            $this->setNotification(
                Lang::txt('COM_GROUPS_SAVE_ERROR_MISSING_INFORMATION')
                    . ': '
                    . Lang::txt('COM_GROUPS_DETAILS_FIELD_CN'),
                'error',
            );
        }
        if (!$g_description) {
            $this->setNotification(
                Lang::txt('COM_GROUPS_SAVE_ERROR_MISSING_INFORMATION')
                    . ': '
                    . Lang::txt('COM_GROUPS_DETAILS_FIELD_DESCRIPTION'),
                'error',
            );
        }

        // Ensure the data passed is valid
        if ($g_cn == 'new' || $g_cn == 'browse') {
            $this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_INVALID_ID'), 'error');
        }
        if (!$this->validCn($g_cn)) {
            $this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_INVALID_ID'), 'error');
        }
        if ($this->task == 'new' && Group::exists($g_cn, true)) {
            $this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_ID_TAKEN'), 'error');
        }

        // Get the logo
        $logo = '';
        if (isset($customization['logo'])) {
            $logo_path = PATH_APP
                . DS
                . trim($this->config->get('uploadpath', '/site/groups'), DS)
                . DS
                . $group->get('gidNumber')
                . DS
                . 'uploads'
                . DS;
            $logo_path = substr($logo_path, strlen(PATH_ROOT));
            $logo = substr($customization['logo'], strlen($logo_path));
        }

        // Plugin settings
        $plugin_access = '';
        foreach ($plugins as $plugin) {
            $plugin_access .= $plugin['name'] . '=' . $plugin['access'] . ',' . "\n";
        }

        // Run content through validation and spam filters
        if (trim($g_public_desc)) {
            $results = Event::trigger('content.onContentBeforeSave', [
                'com_groups.group.public_desc',
                &$g_public_desc,
                ($this->task == 'new'),
            ]);
            foreach ($results as $result) {
                if ($result === false) {
                    $this->setNotification(Lang::txt('COM_GROUPS_SAVE_ERROR_FAILED_VALIDATION'), 'error');
                    break;
                }
            }
        }

        $customFields = Field::all()->rows();
        $customFieldForm = Request::getArray('customfields', []);
        foreach ($customFields as $field) {
            $field->setFormAnswers($customFieldForm);
            if (!$field->validate()) {
                $this->setNotification($field->getError(), 'error');
            }
        }
        // Push back into edit mode if any errors
        if ($this->getNotifications()) {
            $group->set('cn', $g_cn);
            $group->set('description', $g_description);
            $group->set('public_desc', $g_public_desc);
            $group->set('private_desc', $g_private_desc);
            $group->set('join_policy', $g_join_policy);
            $group->set('restrict_msg', $g_restrict_msg);
            $group->set('discoverability', $g_discoverability);
            $group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
            $group->set('logo', $logo);
            $group->set('plugins', $plugin_access);

            $this->lid = $lid;
            $this->group = $group;
            $this->tags = $tags;
            $this->editTask();

            return;
        }

        // Build the e-mail message
        if ($this->task == 'new') {
            $subject = Lang::txt('COM_GROUPS_SAVE_EMAIL_REQUESTED_SUBJECT', $g_cn);
            $type = 'groups_created';
        } else {
            $subject = Lang::txt('COM_GROUPS_SAVE_EMAIL_UPDATED_SUBJECT', $g_cn);
            $type = 'groups_changed';
        }

        if ($this->task == 'new') {
            $group->set('cn', $g_cn);
            $group->set('type', 1);
            $group->set('published', 1);
            $group->set('approved', $this->config->get('auto_approve', 1));
            $group->set('created', Date::toSql());
            $group->set('created_by', User::get('id'));
            $group->add('managers', [User::get('id')]);
            $group->add('members', [User::get('id')]);
            $group->create();
        }

        // Merge incoming settings with existing params
        $params  = new Registry($params);
        $gParams = new Registry($group->get('params'));
        $gParams->merge($params);

        // Snapshot the pre-change values so the manager notification email can
        // highlight what changed. These must be read here, before the setters
        // below mutate the group. ($before is the same cached instance as
        // $group via Group::getInstance(), so it cannot be relied on to hold
        // the old values.) Only meaningful for an edit; a new group has no
        // "before", so the email shows no change markers.
        $before_vals = [];
        if ($this->task != 'new') {
            $beforeTags   = new Tags($group->get('gidNumber'));
            $beforeParams = new Registry($group->get('params'));
            $before_vals  = [
                'description'                    => $group->get('description'),
                'tags'                           => $beforeTags->render('string'),
                'public_desc'                    => $group->get('public_desc'),
                'private_desc'                   => $group->get('private_desc'),
                'logo'                           => $group->get('logo'),
                'join_policy'                    => $group->get('join_policy'),
                'restrict_msg'                   => $group->get('restrict_msg'),
                'discoverability'                => $group->get('discoverability'),
                'discussion_email_autosubscribe' => $group->get('discussion_email_autosubscribe'),
                'access'                         => \Hubzero\User\Group\Helper::getPluginAccess($group),
                'page_comments'                  => $beforeParams->get('page_comments'),
                'page_author'                    => $beforeParams->get('page_author'),
            ];
        }

        // Set group vars & Save group
        $group->set('description', $g_description);
        $group->set('public_desc', $g_public_desc);
        $group->set('private_desc', $g_private_desc);
        $group->set('join_policy', $g_join_policy);
        $group->set('restrict_msg', $g_restrict_msg);
        $group->set('discoverability', $g_discoverability);
        $group->set('logo', $logo);
        $group->set('plugins', $plugin_access);
        $group->set('discussion_email_autosubscribe', $g_discussion_email_autosubscribe);
        $group->set('params', $gParams->toString());
        $group->update();

        foreach ($customFields as $field) {
            $field->saveGroupAnswers($group->get('gidNumber'));
        }
        // Process tags
        $gt = new Tags($group->get('gidNumber'));
        $gt->setTags($tags, User::get('id'));

        // Rename the temporary upload directory if it exist
        $log_comments = '';

        Event::trigger('groups.onGroupAfterSave', [$before, $group]);

        if ($this->task == 'new') {
            if ($lid != $group->get('gidNumber')) {
                $config = $this->config;
                $bp = PATH_APP . DS . trim($this->config->get('uploadpath', '/site/groups'), DS);
                if (is_dir($bp . DS . $lid)) {
                    rename($bp . DS . $lid, $bp . DS . $group->get('gidNumber'));
                }
            }

            $log_action = 'group_created';

            // Trigger the functions that delete associated content
            // Should return logs of what was deleted
            $logs = Event::trigger('groups.onGroupNew', [$group]);
            if (count($logs) > 0) {
                $log_comments .= implode('', $logs);
            }
        } else {
            $log_action   = 'group_edited';
        }

        // log invites
        Log::log([
            'gidNumber' => $group->get('gidNumber'),
            'action'    => $log_action,
            'comments'  => $log_comments,
        ]);

        // Build the e-mail message
        // Note: this is done *before* pushing the changes to the group so we can show, in the message, what was changed
        $eview = new \Hubzero\Mail\View([
            'name'   => 'emails',
            'layout' => 'saved_plain',
        ]);

        $eview->set('option', $this->option);
        $eview->set('user', User::getInstance());
        $eview->set('group', $group);
        $eview->set('before', $before_vals);
        $message['plaintext'] = $eview->loadTemplate(false);
        $message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

        $eview->setLayout('saved');
        $message['multipart'] = $eview->loadTemplate();
        $message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

        // Get the administrator e-mail
        $emailadmin = Config::get('mailfrom');

        // Get the "from" info
        $from = [
            'name'  => Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->name)),
            'email' => Config::get('mailfrom'),
        ];

        // Only email managers if updating group
        if ($type == 'groups_changed') {
            // build array of managers
            $managers = $group->get('managers');

            // create new message
            Plugin::import('xmessage');

            if (!Event::trigger('onSendMessage', [$type, $subject, $message, $from, $managers, $this->option])) {
                $this->setNotification(Lang::txt('GROUPS_ERROR_EMAIL_MANAGERS_FAILED'), 'error');
            }
        }

        // Only inform site admin if the group wasn't auto-approved
        if (!$this->config->get('auto_approve', 1) && $group->get('approved') == 0) {
            // Always notify the site admin, plus any configured reviewers
            $reviewers = array_merge([$emailadmin], explode(',', $this->config->get('group_reviewer', '')));
            $reviewers = array_unique(array_filter(array_map('trim', $reviewers)));

            if (!empty($reviewers)) {
                // Create approval subject
                $subject = Lang::txt('COM_GROUPS_SAVE_WAITING_APPROVAL', Config::get('sitename'));

                // build approval message
                $link  = 'https://' . trim($_SERVER['HTTP_HOST'], '/') . '/groups/' . $group->get('cn');
                $link2 = 'https://'
                    . trim($_SERVER['HTTP_HOST'], '/')
                    . '/administrator/index.php?option='
                    . $this->option;
                // Escape the user-supplied description for the HTML part
                $descHtml = htmlspecialchars($group->get('description'), ENT_QUOTES, 'UTF-8');
                $html  = Lang::txt(
                    'COM_GROUPS_SAVE_WAITING_APPROVAL_HTML_DESC',
                    $link,
                    $group->get('cn'),
                    $descHtml,
                    $link2,
                );
                $plain = Lang::txt(
                    'COM_GROUPS_SAVE_WAITING_APPROVAL_DESC',
                    $group->get('cn'),
                    $group->get('description'),
                    $link2,
                );

                foreach ($reviewers as $reviewer) {
                    // Create new message
                    $message = new \Hubzero\Mail\Message();

                    // Build message object and send
                    $message
                        ->setSubject($subject)
                        ->addFrom($from['email'], $from['name'])
                        ->setTo($reviewer)
                        ->addHeader('X-Mailer', 'PHP/' . phpversion())
                        ->addHeader('X-Component', 'com_groups')
                        ->addHeader('X-Component-Object', 'group_pending_approval')
                        ->addHeader('X-Component-ObjectId', $group->get('gidNumber'))
                        ->addPart($plain, 'text/plain')
                        ->addPart($html, 'text/html')
                        ->send();
                }
            }
        }

        // Create home page
        if ($this->task == 'new') {
            // Create page
            $page = new Page([
                'gidNumber' => $group->get('gidNumber'),
                'parent'    => 0,
                'lft'       => 1,
                'rgt'       => 2,
                'depth'     => 0,
                'alias'     => 'overview',
                'title'     => 'Overview',
                'state'     => 1,
                'privacy'   => 'default',
                'home'      => 1,
            ]);
            $page->store(false);

            // Create page version
            $version = new Page\Version([
                'pageid'     => $page->get('id'),
                'version'    => 1,
                'content'    => "<!-- {FORMAT:HTML} -->\n<p>[[Group.DefaultHomePage()]]</p>",
                'created'    => Date::toSql(),
                'created_by' => User::get('id'),
                'approved'   => 1,
            ]);
            $version->store(false);
        }

        $url = Route::url('index.php?option=' . $this->option . '&cn=' . $group->get('cn'));

        // Log activity
        $recipients = [
            ['group', $group->get('gidNumber')],
            ['user', User::get('id')],
        ];
        foreach ($group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => ($this->task == 'new' ? 'created' : 'updated'),
                'scope'       => 'group',
                'scope_id'    => $group->get('gidNumber'),
                'description' => Lang::txt(
                    'COM_GROUPS_ACTIVITY_GROUP_' . ($this->task == 'new' ? 'CREATED' : 'UPDATED'),
                    '<a href="'
                        . $url
                        . '">'
                        . htmlspecialchars((string) ($group->get('description')), ENT_QUOTES, 'UTF-8')
                        . '</a>',
                ),
                'details'     => [
                    'title'     => $group->get('description'),
                    'url'       => $url,
                    'cn'        => $group->get('cn'),
                    'gidNumber' => $group->get('gidNumber'),
                ],
            ],
            'recipients' => $recipients,
        ]);

        // Show success message to user
        if ($this->task == 'new') {
            $this->setNotification(Lang::txt('COM_GROUPS_CREATED_SUCCESS', $group->get('description')), 'passed');
            // If the new group is published, set the session flag indicating the new submission
            Session::set('newsubmission.group', true);
        } else {
            $this->setNotification(Lang::txt('COM_GROUPS_UPDATED_SUCCESS', $group->get('description')), 'passed');
        }

        // Redirect back to the group page
        App::redirect($url);
    }

    /**
     * Show confirm delete view
     *
     * @return  void
     */
    public function deleteTask()
    {
        // Set the neeced layout
        $this->view->setLayout('delete');

        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask(Lang::txt('COM_GROUPS_DELETE_MUST_BE_LOGGED_IN'));

            return;
        }

        // Check to make sure we have  cname
        if (!$this->cn) {
            $this->errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
        }

        // Load the group page
        $this->view->set('group', Group::getInstance($this->cn));

        // Ensure we found the group info
        if (!$this->view->get('group') || !$this->view->get('group')->get('gidNumber')) {
            $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
        }

        // Check authorization
        if ($this->authorize() != 'manager') {
            $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
        }

        // Get the group params
        $gparams = new Registry($this->view->get('group')->get('params'));

        // If membership is managed in separate place disallow action
        if ($gparams->get('membership_control', 1) == 0) {
            $this->setNotification(Lang::txt('COM_GROUPS_MEMBERSHIP_MANAGED_ELSEWHERE'), 'error');
            App::redirect(Route::url('index.php?option=com_groups&cn=' . $this->view->get('group')->get('cn')));
        }

        // Start log
        $this->view->set(
            'log',
            Lang::txt('COM_GROUPS_DELETE_MEMBER_LOG', count($this->view->get('group')->get('members'))),
        );

        // Trigger the functions that delete associated content
        // Should return logs of what was deleted
        $logs = Event::trigger('groups.onGroupDeleteCount', [$this->view->get('group')]);
        if (count($logs) > 0) {
            $viewLog = $this->view->get('log');
            $viewLog .= '<br />' . implode('<br />', $logs);
            $this->view->set('log', $viewLog);
        }

        // Build the title
        $this->buildTitle();

        // Build pathway
        $this->buildPathway();

        // Get view notifications
        $this->view->set('notifications', ($this->getNotifications()) ? $this->getNotifications() : []);

        // Set some vars for view
        $this->view->set(
            'title',
            Lang::txt('COM_GROUPS_DELETE_GROUP') . ': ' . $this->view->get('group')->get('description'),
        );
        $this->view->set('msg', Request::getString('msg', ''));

        // Display view
        $this->view->display();
    }

    /**
     * Permanently delete group
     *
     * @return  void
     */
    public function doDeleteTask()
    {
        // Check if they're logged in
        if (User::isGuest()) {
            // Return to the confirmation form rather than to this POST-only task
            return $this->loginTask(
                Lang::txt('COM_GROUPS_DELETE_MUST_BE_LOGGED_IN'),
                'index.php?option=' . $this->option . '&cn=' . $this->cn . '&task=delete',
            );
        }

        // Require POST to prevent accidental deletion by crawlers
        if (Request::method() !== 'POST') {
            App::abort(405, Lang::txt('COM_GROUPS_ERROR_METHOD_NOT_ALLOWED'));
        }

        // Check for request forgeries
        Request::checkToken();

        // Check to make sure we have  cname
        if (!$this->cn) {
            $this->errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
        }

        // Load the group page
        $group = Group::getInstance($this->cn);

        // Ensure we found the group info
        if (!$group || !$group->get('gidNumber')) {
            $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
        }

        // Check authorization
        if ($this->authorize() != 'manager') {
            $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
        }

        // Get request vars
        $confirm_delete = Request::getString('confirmdel', '');
        $message = trim(Request::getString('msg', '', 'post'));

        // Check to make sure we have confirmed
        if (!$confirm_delete || $confirm_delete != $group->get('cn')) {
            $this->setNotification(Lang::txt('COM_GROUPS_DELETE_MISSING_CONFIRM_MESSAGE'), 'error');
            $this->deleteTask();

            return;
        }

        // Start log
        $log  = Lang::txt('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $group->get('cn')) . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_ID') . ': ' . $group->get('gidNumber') . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_CNAME') . ': ' . $group->get('cn') . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_TITLE') . ': ' . $group->get('description') . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_DISCOVERABILITY') . ': ' . $group->get('discoverability') . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_PUBLIC_TEXT') . ': ' . stripslashes($group->get('public_desc')) . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_PRIVATE_TEXT') . ': ' . stripslashes($group->get('private_desc')) . "\n";
        $log .= Lang::txt('COM_GROUPS_GROUP_RESTRICTED_MESSAGE')
            . ': '
            . stripslashes($group->get('restrict_msg'))
            . "\n";

        // Get number of group members
        $members  = $group->get('members');
        $managers = $group->get('managers');

        // Log ids of group members
        if ($members) {
            $log .= Lang::txt('COM_GROUP_MEMBERS') . ': ' . implode(' ', $members) . "\n";
        }
        $log .= Lang::txt('COM_GROUP_MANAGERS') . ': ' . implode(' ', $managers) . "\n";

        // Trigger the functions that delete associated content
        // Should return logs of what was deleted
        $logs = Event::trigger('groups.onGroupDelete', [$group]);
        if (count($logs) > 0) {
            $log .= implode('', $logs);
        }

        // Build the file path
        $path = PATH_APP
            . DS
            . trim($this->config->get('uploadpath', '/site/groups'), DS)
            . DS
            . $group->get('gidNumber');
        if (is_dir($path)) {
            // Attempt to delete the file
            if (!Filesystem::deleteDirectory($path)) {
                $this->setNotification(Lang::txt('UNABLE_TO_DELETE_DIRECTORY'), 'error');
            }
        }

        // Clone the deleted group
        $deletedgroup = clone($group);

        // Delete group
        if (!$group->delete()) {
            $this->setNotification($group->error, 'error');
            $this->deleteTask();

            return;
        }

        // Build the "from" info for e-mails
        $from = [];
        $from['name']  = Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->name));
        $from['email'] = Config::get('mailfrom');

        // E-mail subject
        $subject = Lang::txt('COM_GROUPS_DELETE_MESSAGE_SUBJECT', $deletedgroup->get('cn'));

        // Build the e-mail message
        $eview = new \Hubzero\Component\View(['name' => 'emails','layout' => 'deleted']);
        $eview->set('option', $this->option);
        $eview->set('sitename', Config::get('sitename'));
        $eview->set('user', User::getInstance());
        $eview->set('gcn', $deletedgroup->get('cn'));
        $eview->set('msg', $message);
        $eview->set('group', $deletedgroup);
        $html = $eview->loadTemplate();
        $html = str_replace("\n", "\r\n", $html);

        // Build array of email recipients
        $groupMembers = [];
        foreach ($members as $member) {
            $profile = User::getInstance($member);
            if ($profile) {
                $groupMembers[$profile->get('email')] = $profile->get('name');
            }
        }

        // Create new message
        $message = new \Hubzero\Mail\Message();

        // Build message object and send
        $message
            ->setSubject($subject)
            ->addFrom($from['email'], $from['name'])
            ->setTo($from['email'], $from['name'])
            ->setBcc($groupMembers)
            ->addHeader('X-Mailer', 'PHP/' . phpversion())
            ->addHeader('X-Component', 'com_groups')
            ->addHeader('X-Component-Object', 'group_deleted')
            ->addHeader('X-Component-ObjectId', $deletedgroup->get('gidNumber'))
            ->addPart($html, 'text/plain')
            ->send();

        // Log deleted group
        Log::log([
            'gidNumber' => $deletedgroup->get('gidNumber'),
            'action'    => 'group_deleted',
            'comments'  => $log,
        ]);

        // Log activity
        $recipients = [
            ['group', $deletedgroup->get('gidNumber')],
            ['user', User::get('id')],
        ];
        foreach ($managers as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => 'deleted',
                'scope'       => 'group',
                'scope_id'    => $deletedgroup->get('gidNumber'),
                'description' => Lang::txt(
                    'COM_GROUPS_ACTIVITY_GROUP_DELETED',
                    '<a href="'
                        . Route::url('index.php?option=' . $this->option . '&cn=' . $deletedgroup->get('cn'))
                        . '">'
                        . htmlspecialchars((string) ($deletedgroup->get('description')), ENT_QUOTES, 'UTF-8')
                        . '</a>',
                ),
                'details'     => [
                    'title'     => $deletedgroup->get('description'),
                    'url'       => Route::url('index.php?option=' . $this->option . '&cn=' . $deletedgroup->get('cn')),
                    'cn'        => $deletedgroup->get('cn'),
                    'gidNumber' => $deletedgroup->get('gidNumber'),
                ],
            ],
            'recipients' => $recipients,
        ]);

        // Redirect back to the groups page
        $this->setNotification(Lang::txt('COM_GROUPS_DELETE_SUCCESS', $deletedgroup->get('description')), 'passed');
        App::redirect(Route::url('index.php?option=' . $this->option));
    }

    /**
     * View to Suggest User to Create Group
     *
     * @return  void
     */
    public function suggestNonExistingGroupTask()
    {
        // Throw 404 error
        header("HTTP/1.0 404 Not Found");

        // Set notification
        $this->setNotification(
            Lang::txt(
                'COM_GROUPS_CREATE_SUGGEST',
                Route::url(
                    'index.php?option='
                        . $this->option
                        . '&task=new'
                        . (is_numeric($this->cn) ? '' : '&suggested_cn=' . $this->cn),
                ),
            ),
            'warning',
        );

        // Get view notifications
        $notifications = ($this->getNotifications()) ? $this->getNotifications() : [];

        // Display view
        $this->view
            ->set('title', 'Groups')
            ->set('notifications', $notifications)
            ->setLayout('suggest')
            ->display();
    }

    /**
     * Group is Unapproved
     *
     * @return  void
     */
    public function unapprovedGroupTask()
    {
        // Get view notifications
        $notifications = ($this->getNotifications()) ? $this->getNotifications() : [];

        // Display view
        $this->view
            ->set('title', 'Group: Unapproved')
            ->set('notifications', $notifications)
            ->setLayout('unapproved')
            ->display();
    }

    /**
     * Return data for the autocompleter
     *
     * @return  void
     */
    public function autocompleteTask()
    {
        $filters = [
            'limit'  => 20,
            'start'  => 0,
            'search' => trim(Request::getString('value', '')),
        ];

        $query = "SELECT t.gidNumber, t.cn, t.description
					FROM `#__xgroups` AS t
					WHERE (t.type=1 OR t.type=3) AND (LOWER(t.cn) LIKE "
            . $this->database->quote('%' . $filters['search'] . '%') . " "
        . "OR LOWER(t.description) LIKE " . $this->database->quote('%' . $filters['search'] . '%') . ")
					ORDER BY t.description ASC";

        $this->database->setQuery($query);
        $rows = $this->database->loadObjectList();

        // Output search results in JSON format
        $json = [];
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $name = str_replace("\n", '', stripslashes(trim((string) $row->description)));
                $name = str_replace("\r", '', $name);

                $item = [
                    'id'   => $row->cn,
                    'name' => $name,
                ];
                $json[] = $item;
            }
        }

        echo json_encode($json);
    }

    /**
     * Get a list of members
     *
     * @return  void
     */
    public function memberslistTask()
    {
        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask();

            return;
        }

        // Fetch results
        $filters = [];
        $filters['cn'] = trim(Request::getString('group', ''));

        // Without a group this handed every account on the hub to any
        // logged-in user; and a hidden group's roster is for its members
        // (support's ticket-owner picker relies on visible groups' lists).
        $group = $filters['cn'] ? Group::getInstance($filters['cn']) : null;
        if (
            !$group || !$group->get('gidNumber')
            || ($group->get('discoverability') == 1
            && !$group->isMember(User::get('id'))
            && !User::authorise('core.admin', $this->option))
        ) {
            echo '{"members":[]}';
            exit();
        }

        // Limit number of returned rows because PHP runs out of memory and the hub fails with large numbers
        if ($filters['cn']) {
            $query = "SELECT u.username, u.name
						FROM `#__users` AS u, `#__xgroups_members` AS m, `#__xgroups` AS g
						WHERE g.cn=" . $this->database->quote($filters['cn'])
                . " AND g.gidNumber=m.gidNumber AND m.uidNumber=u.id AND "
            . "u.block = '0'
						ORDER BY u.name ASC LIMIT 10000";
        } else {
            $query = "SELECT a.username, a.name
						FROM `#__users` AS a
						WHERE a.block = '0'
						ORDER BY a.name LIMIT 10000";
        }

        $this->database->setQuery($query);
        $rows = $this->database->loadObjectList();

        // Output search results in JSON format
        $json = [];
        if ($filters['cn'] == '') {
            $json[] = '{"username":"","name":"No User"}';
        }
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $json[] = json_encode([
                    'username' => $row->username,
                    'name' => htmlentities(stripslashes($row->name), ENT_COMPAT, 'UTF-8'),
                ]);
            }
        }

        echo '{"members":[' . implode(',', $json) . ']}';
    }

    /**
     * Get a group's availability
     *
     * @param   object  $group  Group
     * @return  string|null|bool
     */
    public function groupavailabilityTask($group = null)
    {
        //get the group
        $group = (!is_null($group)) ? $group : Request::getString('group', '');
        $group = trim($group);

        if ($group == '') {
            return null;
        }

        // Ensure the data passed is valid
        if (($group == 'new' || $group == 'browse') || (!$this->validCn($group)) || (Group::exists($group, true))) {
            $availability = false;
        } else {
            $availability = true;
        }

        if (Request::getInt('no_html', 0) == 1) {
            echo json_encode(['available' => $availability]);
            return null;
        }

        return $availability;
    }

    /**
     * Download a file
     *
     * @param   string  $filename  File name
     *
     * @return  void
     */
    public function downloadTask($filename = '')
    {
        // Get the group
        $group = Group::getInstance($this->cn);

        // Make sure we have a group
        if (!is_object($group)) {
            return;
        }

        // Authorize
        $authorized = $this->authorize();

        // Get the file name
        if (substr(strtolower($filename), 0, 5) == 'image') {
            $file = urldecode(substr($filename, 6));
        } elseif (substr(strtolower($filename), 0, 4) == 'file') {
            $file = urldecode(substr($filename, 5));
        } else {
            return;
        }

        // Clean up file, strip double "uploads" & trim directory sep
        $file = preg_replace('/^\/uploads\//', '', $file);
        $file = ltrim($file, DS);

        // Get extension
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        // If were on the wiki we need to output files a specific way
        // @TODO: Re-implement this! Should not have hard-coded refs to other components
        if ($this->active == 'wiki') {
            // Get access level for wiki
            $access = Group\Helper::getPluginAccess($group, 'wiki');

            // Check to make sure user has access to wiki section
            if (
                ($access == 'members' && !in_array(User::get('id'), $group->get('members')))
                || ($access == 'registered' && User::isGuest())
            ) {
                $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
            }

            // Load wiki page from db

            $page = new \Components\Wiki\Models\Page();

            $pagename = Request::getString('pagename');
            $scope = Request::getString('scope', $group->get('cn') . DS . 'wiki');
            if ($scope) {
                $parts = explode('/', $scope);
                if (count($parts) > 2) {
                    $pagename = array_pop($parts);
                    if (strtolower($filename) == strtolower($pagename)) {
                        $pagename = array_pop($parts);
                    }
                    $scope = implode('/', $parts);
                }
                $scope = str_replace($group->get('cn') . '/wiki', '', $scope);
                $scope = ($scope ? trim($scope, '/') . '/' : $scope);
            }
            $page = \Components\Wiki\Models\Page::oneByPath($scope . $pagename, 'group', $group->get('gidNumber'));

            // An unknown page is a blank row: its id is null and the base path
            // below would collapse to the wiki root, where every page's
            // attachments live under <page id>/<file>.
            if (!$page->get('id')) {
                $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_FILE_NOT_FOUND'));
                return;
            }

            // Check specific wiki page access
            if (
                $page->get('access') == 1
                && !in_array(User::get('id'), $group->get('members'))
                && $authorized != 'admin'
            ) {
                $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
                return;
            }

            // Get the config and build base path
            $wiki_config = Component::params('com_wiki');
            $base_path = $wiki_config->get('filepath') . DS . $page->get('id');
        } elseif ($this->active == 'blog') {
            // Get access setting of group blog
            $access = Group\Helper::getPluginAccess($group, 'blog');

            // Make sure user has access to blog
            if (
                ($access == 'members' && !in_array(User::get('id'), $group->get('members')))
                || ($access == 'registered' && User::isGuest())
            ) {
                $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
            }

            // Make sure we have a group id of the proper length
            $groupID = Group\Helper::niceidformat($group->get('gidNumber'));

            // Build path to blog folder
            $base_path = $this->config->get('uploadpath') . DS . $groupID . DS . 'blog';
            if (!file_exists(PATH_APP . DS . $base_path . DS . $file)) {
                $base_path = $this->config->get('uploadpath')
                    . DS
                    . $group->get('gidNumber')
                    . DS
                    . 'uploads'
                    . DS
                    . 'blog';
            }
        } else {
            // Get access level for overview or other group pages
            $access = Group\Helper::getPluginAccess($group, 'overview');

            // Check to make sure we can access it
            if (
                ($access == 'members' && !in_array(User::get('id'), $group->get('members')))
                || ($access == 'registered' && User::isGuest())
            ) {
                $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
            }

            // Build the path
            $base_path = $this->config->get('uploadpath');
            $base_path .= DS . $group->get('gidNumber') . DS . 'uploads';
        }

        // Trim base path
        $base_path = ltrim($base_path, DS);

        // Only can serve files from within /site/groups/{group_id}/uploads/
        $pathCheck = PATH_APP . DS . $base_path;

        // Final path of file
        $file_path     = $base_path . DS . $file;
        $alt_file_path = null;

        // If super group offer alt path outside uploads
        if ($group->isSuperGroup()) {
            // do not serve gitignore or PHP files
            if ($file != '.gitignore' && strpos($file, '.php') === false) {
                // do not allow serving anything from config or .git directory
                $replace_base = ['/uploads', '/config', '/.git'];

                $alt_file_path = str_replace($replace_base, '', $base_path) . DS . $file;

                // If super group can serve files anywhere inside /site/groups/{group_id}
                $altPathCheck  = PATH_APP . DS . ltrim($alt_file_path);
            }
        }

        // Ensure the file exist
        if (!file_exists(PATH_APP . DS . $file_path)) {
            if ($alt_file_path == null || !file_exists(PATH_APP . DS . $alt_file_path)) {
                $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_FILE_NOT_FOUND'));
                return;
            } else {
                $file_path = $alt_file_path;
                $pathCheck = $altPathCheck;
            }
        }

        // Get full path, expanding ../
        if ($realPath = realpath(PATH_APP . DS . $file_path)) {
            // Make sure requested file is within acceptable dir
            if (strpos($realPath, $pathCheck) === false) {
                $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
                return;
            }
        }

        // New content server
        $contentServer = new \Hubzero\Content\Server();
        $contentServer->filename(PATH_APP . DS . $file_path);
        $contentServer->disposition('attachment');
        $contentServer->acceptranges(true);

        // Do we need to manually set mime type?
        switch ($extension) {
            case 'css':
                $contentServer->setContentType('text/css');
                break;
            case 'js':
                $contentServer->setContentType('application/javascript');
                break;
            default:
                break;
        }

        // Serve up the file
        if (!$contentServer->serve()) {
            App::abort(404, Lang::txt('COM_GROUPS_SERVER_ERROR'));
        }

        exit();
    }

    /**
     * Check if a group alias is valid
     *
     * @param   integer  $cn        Group alias
     * @param   boolean  $allowDashes  Allow dashes in cn
     * @return  boolean  True if valid, false if not
     */
    private function validCn($cn, $allowDashes = false)
    {
        $regex = '/^[0-9a-z]+[_0-9a-z]*$/u';
        if ($allowDashes) {
            $regex = '/^[0-9a-z]+[-_0-9a-z]*$/u';
        }

        if (\Hubzero\Utility\Validate::reserved('group', $cn)) {
            return false;
        }

        if (preg_match($regex, $cn)) {
            if (is_numeric($cn) && intval($cn) == $cn && $cn >= 0) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }
}
