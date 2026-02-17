<?php

namespace Plugins\Groups\Blog;

use Hubzero\Plugin\Plugin;

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Groups Plugin class for blog entries
 *
 */
class Blog extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     *
     * @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
     */
    protected $_autoloadLanguage = true;

    /**
     * Loads the plugin language file
     *
     * @param   string   $extension  The extension for which a language file should be loaded
     * @param   string   $basePath   The basepath to use
     * @return  boolean  True, if the file has successfully loaded.
     */
    public function loadLanguage($extension = '', $basePath = PATH_APP)
    {
        if (empty($extension)) {
            $extension = 'plg_' . $this->_type . '_' . $this->_name;
        }

        $group = \Hubzero\User\Group::getInstance(Request::getCmd('cn'));
        if ($group && $group->isSuperGroup()) {
            $basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
        }

        $lang = \App::get('language');
        $pluginPath = DS . 'plugins' . DS . $this->_type . DS . $this->_name;
        return $lang->load(strtolower($extension), $basePath, null, false, true)
            || $lang->load(strtolower($extension), PATH_APP . $pluginPath, null, false, true)
            || $lang->load(strtolower($extension), PATH_APP . $pluginPath, null, false, true)
            || $lang->load(strtolower($extension), PATH_CORE . $pluginPath, null, false, true);
    }

    /**
     * Return the alias and name for this category of content
     *
     * @return  array
     */
    public function &onGroupAreas()
    {
        $area = array(
            'name' => $this->_name,
            'title' => Lang::txt('PLG_GROUPS_BLOG'),
            'default_access' => $this->params->get('plugin_access', 'members'),
            'display_menu_tab' => $this->params->get('display_tab', 1),
            'icon' => 'f075'
        );
        return $area;
    }

    /**
     * Return data on a group view (this will be some form of HTML)
     *
     * @param   object   $group       Current group
     * @param   string   $option      Name of the component
     * @param   string   $authorized  User's authorization level
     * @param   integer  $limit       Number of records to pull
     * @param   integer  $limitstart  Start of records to pull
     * @param   string   $action      Action to perform
     * @param   array    $access      What can be accessed
     * @param   array    $areas       Active area(s)
     * @return  array
     */
    public function onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas = null)
    {
        $return = 'html';
        $active = $this->_name;

        // The output array we're returning
        $arr = array(
            'html'     => '',
            'metadata' => array()
        );

        //get this area details
        $this_area = $this->onGroupAreas();

        // Check if our area is in the array of areas we want to return results for
        if (is_array($areas) && $limit) {
            if (!in_array($this_area['name'], $areas)) {
                $return = 'metadata';
            }
        }

        include_once Component::path('com_blog') . DS . 'models' . DS . 'archive.php';

        $this->model = new Components\Blog\Models\Archive('group', $group->get('gidNumber'));

        // are we returning html
        if ($return == 'html') {
            // set group members plugin access level
            $group_plugin_acl = $access[$active];

            // get the group members
            $members = $group->get('members');

            // if set to nobody make sure cant access
            if ($group_plugin_acl == 'nobody') {
                $arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
                return $arr;
            }

            // check if guest and force login if plugin access is registered or members
            if (
                User::isGuest()
                && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')
            ) {
                $url = Route::url(
                    'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=' . $active,
                    false,
                    true
                );

                App::redirect(
                    Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
                    Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($active)),
                    'warning'
                );
                return;
            }

            // check to see if user is member and plugin access requires members
            if (
                !in_array(User::get('id'), $members)
                && $group_plugin_acl == 'members'
                && $authorized != 'admin'
            ) {
                $msgTxt = Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active));
                $arr['html'] = '<p class="info">' . $msgTxt . '</p>';
                return $arr;
            }

            // user vars
            $this->authorized = $authorized;

            // group vars
            $this->group      = $group;
            $this->members    = $members;

            // Set some variables so other functions have access
            $this->action     = $action;
            $this->option     = $option;
            $this->database   = App::get('db');

            //get the plugins params
            $this->params = \Hubzero\Plugin\Params::getParams($group->gidNumber, 'groups', $this->_name);

            if ($authorized == 'manager' || $authorized == 'admin') {
                $this->params->set('access-edit-comment', true);
                $this->params->set('access-delete-comment', true);
            }

            // Append to document the title
            Document::setTitle(Document::getTitle() . ': ' . Lang::txt('PLG_GROUPS_BLOG'));

            switch ($this->action) {
                // Feeds
                case 'feed.rss':
                    $this->feed();
                    break;
                case 'feed':
                    $this->feed();
                    break;
                // Settings
                case 'savesettings':
                    $arr['html'] = $this->savesettings();
                    break;
                case 'settings':
                    $arr['html'] = $this->settings();
                    break;
                // Comments
                case 'savecomment':
                    $arr['html'] = $this->savecomment();
                    break;
                case 'newcomment':
                    $arr['html'] = $this->newcomment();
                    break;
                case 'editcomment':
                    $arr['html'] = $this->entry();
                    break;
                case 'deletecomment':
                    $arr['html'] = $this->deletecomment();
                    break;
                // Entries
                case 'save':
                    $arr['html'] = $this->save();
                    break;
                case 'new':
                    $arr['html'] = $this->newEntry();
                    break;
                case 'edit':
                    $arr['html'] = $this->edit();
                    break;
                case 'delete':
                    $arr['html'] = $this->delete();
                    break;
                case 'entry':
                    $arr['html'] = $this->entry();
                    break;
                case 'archive':
                case 'browse':
                default:
                    $arr['html'] = $this->browse();
                    break;
            }
        }

        $filters = array(
            'scope'    => 'group',
            'scope_id' => $group->get('gidNumber'),
            'state'    => 1,
            'access'   => User::getAuthorisedViewLevels()
        );

        // Build the HTML meant for the "profile" tab's metadata overview
        $arr['metadata']['count'] = $this->model->entries($filters)->total();

        return $arr;
    }

    /**
     * Remove any associated data when group is deleted
     *
     * @param   object  $group  Group being deleted
     * @return  string  Log of items removed
     */
    public function onGroupDelete($group)
    {
        // Import needed libraries
        include_once Component::path('com_blog') . DS . 'models' . DS . 'archive.php';

        $entries = Components\Blog\Models\Entry::all()
            ->whereEquals('scope', 'group')
            ->whereEquals('scope_id', $group->get('gidNumber'))
            ->rows();

        // Start the log text
        $log = Lang::txt('PLG_GROUPS_BLOG_LOG') . ': ';

        if (count($entries) > 0) {
            // Loop through all the IDs for pages associated with this group
            foreach ($entries as $entry) {
                $entry->set('state', 2);
                $entry->save();

                // Add the ID to the log
                $log .= $entry->get('id') . ' ' . "\n";
            }
        } else {
            $log .= Lang::txt('PLG_GROUPS_BLOG_NO_RESULTS_FOUND') . "\n";
        }

        // Return the log
        return $log;
    }

    /**
     * Return a count of items that will be removed when group is deleted
     *
     * @param   object  $group  Group to delete
     * @return  string
     */
    public function onGroupDeleteCount($group)
    {
        include_once Component::path('com_blog') . DS . 'models' . DS . 'archive.php';

        $entries = \Components\Blog\Models\Entry::all()
            ->whereEquals('scope', 'group')
            ->whereEquals('scope_id', $group->get('gidNumber'))
            ->count();

        return Lang::txt('PLG_GROUPS_BLOG_LOG') . ': ' . $entries;
    }

    /**
     * Parse an SEF URL into its component bits
     * stripping out the path leading up to the blog plugin
     *
     * @return  string
     */
    private function parseUrl()
    {
        static $path;

        if (!$path) {
            $path = Request::path();

            $path = str_replace(Request::base(true), '', $path);
            $path = str_replace('index.php', '', $path);
            $path = '/' . trim($path, '/');

            $blog = '/groups/' . $this->group->get('cn') . '/blog';

            if ($path == $blog) {
                $path = array();
                return $path;
            }

            $path = ltrim($path, '/');
            $path = explode('/', $path);
            $path = array_map('urldecode', $path);

            $paths = array();
            $start = false;
            foreach ($path as $bit) {
                if ($bit == 'groups' && !$start) {
                    $start = true;
                    continue;
                }
                if ($start) {
                    $paths[] = preg_replace('/[^a-zA-Z0-9_\-\:]/', '', $bit);
                }
            }
            if (count($paths) >= 2) {
                array_shift($paths); // Remove group cn
                array_shift($paths); // Remove 'blog'
            }
            $path = $paths;
        }

        return $path;
    }

    /**
     * Display a list of latest blog entries
     *
     * @return  string
     */
    private function browse()
    {
        // Filters for returning results
        $filters = array(
            'year'       => Request::getInt('year', 0),
            'month'      => Request::getInt('month', 0),
            'scope'      => 'group',
            'scope_id'   => $this->group->get('gidNumber'),
            'search'     => Request::getString('search', ''),
            'authorized' => false,
            'state'      => 1,
            'access'     => User::getAuthorisedViewLevels()
        );

        // See what information we can get from the path
        $path = Request::path();
        if (strstr($path, '/')) {
            $bits = $this->parseUrl();

            // if we have 3 pieces, then there is year/month/entry
            // display entry
            if (count($bits) > 2) {
                return $this->entry();
            }

            $filters['year']  = (isset($bits[0]) && is_numeric($bits[0])) ? $bits[0] : $filters['year'];
            $filters['month'] = (isset($bits[1]) && is_numeric($bits[1])) ? $bits[1] : $filters['month'];
        }
        if ($filters['year'] > date("Y")) {
            $filters['year'] = 0;
        }
        if ($filters['month'] > 12) {
            $filters['month'] = 0;
        }

        if (
            $this->authorized == 'member'
            || $this->authorized == 'manager'
            || $this->authorized == 'admin'
        ) {
            array_push($filters['access'], 5);
            $filters['authorized'] = true;
        }

        $view = $this->view('default', 'browse')
            ->set('option', $this->option)
            ->set('group', $this->group)
            ->set('config', $this->params)
            ->set('archive', $this->model)
            ->set('task', $this->action)
            ->set('filters', $filters)
            ->set('canpost', $this->getPostingPermissions())
            ->set('authorized', $this->authorized)
            ->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Display an RSS feed of latest entries
     *
     * @return  string
     */
    private function feed()
    {
        if (!$this->params->get('feeds_enabled', 1)) {
            return $this->browse();
        }

        // Filters for returning results
        $filters = array(
            'limit'      => Request::getInt('limit', Config::get('list_limit')),
            'start'      => Request::getInt('limitstart', 0),
            'year'       => Request::getInt('year', 0),
            'month'      => Request::getInt('month', 0),
            'scope'      => 'group',
            'scope_id'   => $this->group->get('gidNumber'),
            'search'     => Request::getString('search', ''),
            'created_by' => Request::getInt('author', 0),
            'state'      => 1
        );

        $path = Request::path();
        if (strstr($path, '/')) {
            $bits = $this->parseUrl();

            $filters['year']  = (isset($bits[0]) && is_numeric($bits[0])) ? $bits[0] : $filters['year'];
            $filters['month'] = (isset($bits[1]) && is_numeric($bits[1])) ? $bits[1] : $filters['month'];
        }
        if ($filters['year'] > date("Y")) {
            $filters['year'] = 0;
        }
        if ($filters['month'] > 12) {
            $filters['month'] = 0;
        }

        // Set the mime encoding for the document
        Document::setType('feed');

        // Start a new feed object
        $doc = Document::instance();
        $urlStr = 'index.php?option=' . $this->option . '&cn='
            . $this->group->get('cn') . '&active=' . $this->_name;
        $doc->link = Route::url($urlStr);

        // Build some basic RSS document information
        $doc->title       = Config::get('sitename') . ': ' . Lang::txt('Groups') . ': '
            . stripslashes($this->group->get('description')) . ': ' . Lang::txt('Blog');
        $doc->description = Lang::txt(
            'PLG_GROUPS_BLOG_RSS_DESCRIPTION',
            $this->group->get('cn'),
            Config::get('sitename')
        );
        $doc->copyright   = Lang::txt(
            'PLG_GROUPS_BLOG_RSS_COPYRIGHT',
            date("Y"),
            Config::get('sitename')
        );
        $doc->category    = Lang::txt('PLG_GROUPS_BLOG_RSS_CATEGORY');

        $rows = $this->model->entries($filters)
            ->ordered()
            ->paginated()
            ->rows();

        // Start outputing results if any found
        if ($rows->count() > 0) {
            foreach ($rows as $row) {
                $item = new \Hubzero\Document\Type\Feed\Item();

                // Strip html from feed item description text
                $item->description = $row->content;
                $item->description = \Hubzero\Utility\Sanitize::stripAll(
                    strip_tags(html_entity_decode($item->description))
                );
                if ($this->params->get('feed_entries') == 'partial') {
                    $item->description = \Hubzero\Utility\Str::truncate($item->description, 300);
                }
                $item->description = '<![CDATA[' . $item->description . ']]>';

                // Load individual item creator class
                $item->title       = html_entity_decode(strip_tags($row->get('title')));
                $item->link        = Route::url($row->link());
                $item->date        = date('r', strtotime($row->published()));
                $item->category    = '';
                $item->author      = $row->creator()->get('name');

                // Loads item info into rss array
                $doc->addItem($item);
            }
        }

        // Output the feed
        echo $doc->render();
        exit();
    }

    /**
     * Determine permissions to post an entry
     *
     * @return  boolean  True if user cna post, false if not
     */
    private function getPostingPermissions()
    {
        if ($this->group->published != 1) {
            return false;
        }

        switch ($this->params->get('posting')) {
            case 1:
                if ($this->authorized == 'manager' || $this->authorized == 'admin') {
                    return true;
                }
                break;

            case 0:
            default:
                if ($this->authorized == 'member' || $this->authorized == 'manager' || $this->authorized == 'admin') {
                    return true;
                } else {
                    return false;
                }
                break;
        }

        return false;
    }

    /**
     * Display a blog entry
     *
     * @return  string
     */
    private function entry()
    {
        if (isset($this->entry) && is_object($this->entry)) {
            $row = $this->entry;
        } else {
            $path = Request::path();
            if (strstr($path, '/')) {
                $bits = $this->parseUrl();

                $alias = end($bits);
            }

            $row = \Components\Blog\Models\Entry::oneByScope(
                $alias,
                $this->model->get('scope'),
                $this->model->get('scope_id')
            );
        }

        if (!$row->get('id') || $row->isDeleted()) {
            App::abort(404, Lang::txt('PLG_GROUPS_BLOG_NO_ENTRY_FOUND'));
            return; // $this->browse(); Can cause infinite loop
        }

        // Check authorization
        $isUnpublishedAndNotOwner = $row->get('state') == 0
            && User::get('id') != $row->get('created_by')
            && $this->authorized != 'member'
            && $this->authorized != 'manager'
            && $this->authorized != 'admin';
        if (
            ($row->get('access') == 2 && User::isGuest())
            || $isUnpublishedAndNotOwner
        ) {
            App::abort(403, Lang::txt('PLG_GROUPS_BLOG_NOT_AUTH'));
            return;
        }

        // make sure the group owns this
        if ($row->get('scope_id') != $this->group->get('gidNumber')) {
            App::abort(403, Lang::txt('PLG_GROUPS_BLOG_NOT_AUTH'));
            return;
        }

        // Filters for returning results
        $filters = array(
            'limit'      => 10,
            'start'      => 0,
            'scope'      => 'group',
            'scope_id'   => $this->group->get('gidNumber'),
            'created_by' => 0,
            'state'      => 1,
            'access'     => User::getAuthorisedViewLevels()
        );

        if (
            $this->authorized == 'member'
            || $this->authorized == 'manager'
            || $this->authorized == 'admin'
        ) {
            array_push($filters['access'], 5);
            $filters['authorized'] = true;
        } else {
            $filters['authorized'] = false;
        }

        Event::trigger('blog.onBlogView', array($row));

        $view = $this->view('default', 'entry')
            ->set('option', $this->option)
            ->set('group', $this->group)
            ->set('config', $this->params)
            ->set('archive', $this->model)
            ->set('task', $this->action)
            ->set('row', $row)
            ->set('filters', $filters)
            ->set('canpost', $this->getPostingPermissions())
            ->set('authorized', $this->authorized)
            ->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Display a form for creating an entry
     *
     * @return  string
     */
    private function newEntry()
    {
        return $this->edit();
    }

    /**
     * Display a form for editing an entry
     *
     * @param   object  $entry
     * @return  string
     */
    private function edit($entry = null)
    {
        $blogUrl = 'index.php?option=' . $this->option . '&cn='
            . $this->group->get('cn') . '&active=' . $this->_name;
        $blog = Route::url($blogUrl);

        if (User::isGuest()) {
            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . base64_encode($blog))
            );
            return;
        }

        if ($this->group->published != 1 || !$this->authorized || !$this->getPostingPermissions()) {
            App::redirect(
                $blog,
                Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'),
                'error'
            );
            return;
        }

        // Load the entry
        if (!is_object($entry)) {
            $entry = \Components\Blog\Models\Entry::oneOrNew(Request::getInt('entry', 0));
        }

        // Does it exist?
        if ($entry->isNew()) {
            // Set some defaults
            $entry->set('allow_comments', 1);
            $entry->set('state', 1);
            $entry->set('scope', 'group');
            $entry->set('scope_id', $this->group->get('gidNumber'));
        }

        $view = $this->view('default', 'edit')
            ->set('option', $this->option)
            ->set('group', $this->group)
            ->set('task', $this->action)
            ->set('config', $this->params)
            ->set('entry', $entry)
            ->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Save an entry
     *
     * @return  void
     */
    private function save()
    {
        if (User::isGuest()) {
            $blogUrl = 'index.php?option=' . $this->option . '&cn='
                . $this->group->get('cn') . '&active=' . $this->_name;
            $blog = Route::url($blogUrl, false, true);

            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . base64_encode($blog)),
                Lang::txt('GROUPS_LOGIN_NOTICE'),
                'warning'
            );
            return;
        }

        if ($this->group->published != 1 || !$this->authorized || !$this->getPostingPermissions()) {
            $this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
            return $this->browse();
        }

        // Check for request forgeries
        Request::checkToken();

        $entry = Request::getArray('entry', array(), 'post');

        if (isset($entry['publish_up']) && $entry['publish_up'] != '') {
            $entry['publish_up']   = Date::of($entry['publish_up'], Config::get('offset'))->toSql();
        }

        if (isset($entry['publish_down']) && $entry['publish_down'] != '') {
            $entry['publish_down'] = Date::of($entry['publish_down'], Config::get('offset'))->toSql();
        }

        // make sure we dont want to turn off comments
        $entry['allow_comments'] = (isset($entry['allow_comments'])) ? : 0;

        // Instantiate model
        $row = Components\Blog\Models\Entry::oneOrNew($entry['id'])->set($entry);
        if ($row->get('alias') == '') {
            $alias = $row->automaticAlias($row);
        }

        if ($row->isNew()) {
            $item = Components\Blog\Models\Entry::oneByScope(
                $alias,
                $this->model->get('scope'),
                $this->model->get('scope_id')
            );

            if ($item->get('id')) {
                $this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_ALIAS_EXISTS'));
                return $this->edit($row);
            }
        }

        // Store new content
        if (!$row->save()) {
            $this->setError($row->getError());
            return $this->edit($row);
        }

        // Process tags
        if (!$row->tag(Request::getString('tags', ''))) {
            $this->setError($row->getError());
            return $this->edit($row);
        }

        // Record the activity
        $recipients = array(['group', $this->group->get('gidNumber')]);

        if (!in_array($row->get('created_by'), $this->group->get('managers'))) {
            $recipients[] = ['user', $row->get('created_by')];
        }

        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        $action = ($entry['id'] ? 'updated' : 'created');
        $langKey = 'PLG_GROUPS_BLOG_ACTIVITY_ENTRY_' . ($entry['id'] ? 'UPDATED' : 'CREATED');
        $link = '<a href="' . Route::url($row->link()) . '">' . $row->get('title') . '</a>';
        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => $action,
                'scope'       => 'blog.entry',
                'scope_id'    => $row->get('id'),
                'description' => Lang::txt($langKey, $link),
                'details'     => array(
                    'title' => $row->get('title'),
                    'url'   => Route::url($row->link())
                )
            ],
            'recipients' => $recipients
        ]);

        App::redirect(
            Route::url($row->link())
        );
    }

    /**
     * Delete an entry
     *
     * @return  string
     */
    private function delete()
    {
        if (User::isGuest()) {
            $this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
            return;
        }

        if ($this->group->published != 1 || !$this->authorized || !$this->getPostingPermissions()) {
            $this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
            return $this->browse();
        }

        // Incoming
        $id = Request::getInt('entry', 0);
        if (!$id) {
            return $this->browse();
        }

        $process    = Request::getString('process', '');
        $confirmdel = Request::getString('confirmdel', '');

        // Initiate a blog entry object
        $entry = Components\Blog\Models\Entry::oneOrFail($id);

        // Did they confirm delete?
        if (!$process || !$confirmdel) {
            if ($process && !$confirmdel) {
                $this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_CONFIRM_DELETION'));
            }

            // Output HTML
            $view = $this->view('default', 'delete')
                ->set('option', $this->option)
                ->set('group', $this->group)
                ->set('task', $this->action)
                ->set('config', $this->params)
                ->set('entry', $entry)
                ->set('authorized', $this->authorized)
                ->setErrors($this->getErrors());

            return $view->loadTemplate();
        }

        // Check for request forgeries
        Request::checkToken();

        // Delete the entry itself
        $entry->set('state', 2);

        if (!$entry->save()) {
            $this->setError($entry->getError());
        }

        // Record the activity
        $recipients = array(['group', $this->group->get('gidNumber')]);

        if (!in_array($entry->get('created_by'), $this->group->get('managers'))) {
            $recipients[] = ['user', $entry->get('created_by')];
        }

        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => 'deleted',
                'scope'       => 'blog.entry',
                'scope_id'    => $id,
                'description' => Lang::txt('PLG_GROUPS_BLOG_ACTIVITY_ENTRY_DELETED', $entry->get('title')),
                'details'     => array(
                    'title' => $entry->get('title'),
                    'url'   => Route::url($entry->link())
                )
            ],
            'recipients' => $recipients
        ]);

        // Return the topics list
        return $this->browse();
    }

    /**
     * Save a comment
     *
     * @return  string
     */
    private function savecomment()
    {
        // Ensure the user is logged in
        if (User::isGuest()) {
            $blogUrl = 'index.php?option=' . $this->option . '&cn='
                . $this->group->get('cn') . '&active=' . $this->_name;
            $blog = Route::url($blogUrl, false, true);

            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . base64_encode($blog)),
                Lang::txt('GROUPS_LOGIN_NOTICE'),
                'warning'
            );
            return;
        }

        if ($this->group->published != 1) {
            $this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
            return $this->browse();
        }

        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $data = Request::getArray('comment', array(), 'post');

        // Instantiate a new comment object and pass it the data
        $comment = Components\Blog\Models\Comment::oneOrNew($data['id'])->set($data);

        // Store new content
        if (!$comment->save()) {
            $this->setError($comment->getError());
            return $this->entry();
        }

        // Record the activity
        $entry = Components\Blog\Models\Entry::oneOrFail($comment->get('entry_id'));

        $recipients = array(['group', $this->group->get('gidNumber')]);

        if (!in_array($comment->get('created_by'), $this->group->get('managers'))) {
            $recipients[] = ['user', $comment->get('created_by')];
        }

        if ($comment->get('parent')) {
            if (!in_array($comment->parent()->get('created_by'), $this->group->get('managers'))) {
                $recipients[] = ['user', $comment->parent()->get('created_by')];
            }
        }

        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        $action = ($data['id'] ? 'updated' : 'created');
        $langKey = 'PLG_GROUPS_BLOG_ACTIVITY_COMMENT_' . ($data['id'] ? 'UPDATED' : 'CREATED');
        $commentUrl = Route::url($entry->link() . '#c' . $comment->get('id'));
        $link = '<a href="' . $commentUrl . '">' . $entry->get('title') . '</a>';
        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => $action,
                'scope'       => 'blog.entry.comment',
                'scope_id'    => $comment->get('id'),
                'anonymous'   => $comment->get('anonymous', 0),
                'description' => Lang::txt($langKey, $comment->get('id'), $link),
                'details'     => array(
                    'title'    => $entry->get('title'),
                    'entry_id' => $entry->get('id'),
                    'url'      => $entry->link() . '#c' . $comment->get('id')
                )
            ],
            'recipients' => $recipients
        ]);

        return $this->entry();
    }

    /**
     * Delete a comment
     *
     * @return  string
     */
    private function deletecomment()
    {
        // Ensure the user is logged in
        if (User::isGuest()) {
            $this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
            return;
        }

        if ($this->group->published != 1) {
            $this->setError(Lang::txt('PLG_GROUPS_BLOG_ERROR_PERMISSION_DENIED'));
            return $this->browse();
        }

        // Incoming
        $id = Request::getInt('comment', 0);
        if (!$id) {
            return $this->entry();
        }

        // Initiate a blog comment object
        $comment = Components\Blog\Models\Comment::oneOrFail($id);

        // Delete all comments on an entry
        $comment->set('state', $comment::STATE_DELETED);

        // Delete the entry itself
        if (!$comment->save()) {
            $this->setError($comment->getError());
        }

        // Record the activity
        $recipients = array(['group', $this->group->get('gidNumber')]);

        if (!in_array($comment->get('created_by'), $this->group->get('managers'))) {
            $recipients[] = ['user', $comment->get('created_by')];
        }

        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        $entry = Components\Blog\Models\Entry::oneOrFail($comment->get('entry_id'));

        $link = '<a href="' . Route::url($entry->link()) . '">' . $entry->get('title') . '</a>';
        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => 'deleted',
                'scope'       => 'blog.entry.comment',
                'scope_id'    => $comment->get('id'),
                'description' => Lang::txt(
                    'PLG_GROUPS_BLOG_ACTIVITY_COMMENT_DELETED',
                    $comment->get('id'),
                    $link
                ),
                'details'     => array(
                    'title'    => $entry->get('title'),
                    'entry_id' => $entry->get('id'),
                    'url'      => $entry->link()
                )
            ],
            'recipients' => $recipients
        ]);

        // Return the topics list
        return $this->entry();
    }

    /**
     * Display blog settings
     *
     * @return  string
     */
    private function settings()
    {
        if (User::isGuest()) {
            $this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
            return;
        }

        if ($this->group->published != 1 || ($this->authorized != 'manager' && $this->authorized != 'admin')) {
            $this->setError(Lang::txt('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
            return $this->browse();
        }

        // Output HTML
        $view = $this->view('default', 'settings')
            ->set('option', $this->option)
            ->set('group', $this->group)
            ->set('task', $this->action)
            ->set('config', $this->params)
            ->set('model', $this->model)
            ->set('authorized', $this->authorized)
            ->setErrors($this->getErrors());

        return $view->loadTemplate();
    }

    /**
     * Save blog settings
     *
     * @return  void
     */
    private function savesettings()
    {
        if (User::isGuest()) {
            $this->setError(Lang::txt('GROUPS_LOGIN_NOTICE'));
            return;
        }

        if ($this->group->published != 1 || ($this->authorized != 'manager' && $this->authorized != 'admin')) {
            $this->setError(Lang::txt('PLG_GROUPS_BLOG_NOT_AUTHORIZED'));
            return $this->browse();
        }

        // Check for request forgeries
        Request::checkToken();

        $row = \Hubzero\Plugin\Params::oneByPluginOrNew($this->group->get('gidNumber'), $this->_type, $this->_name);

        // Get parameters
        $params = new \Hubzero\Config\Registry(Request::getArray('params', array(), 'post'));
        $row->set('params', $params->toString());

        // Store new content
        if (!$row->save()) {
            $this->setError($row->getError());
            return $this->settings();
        }

        // Record the activity
        $recipients = array(['group', $this->group->get('gidNumber')]);
        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => 'updated',
                'scope'       => 'blog.settings',
                'scope_id'    => $row->get('id'),
                'description' => Lang::txt('PLG_GROUPS_BLOG_ACTIVITY_SETTINGS_UPDATED')
            ],
            'recipients' => $recipients
        ]);

        $settingsUrl = 'index.php?option=com_groups&cn=' . $this->group->get('cn')
            . '&active=' . $this->_name . '&action=settings';
        App::redirect(
            Route::url($settingsUrl),
            Lang::txt('PLG_GROUPS_BLOG_SETTINGS_SAVED'),
            'passed'
        );
    }
}
