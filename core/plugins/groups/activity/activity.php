<?php

namespace Plugins\Groups\Activity;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Group plugin class for activity
 */
class Activity extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $_autoloadLanguage = true;

    /**
     * Return the alias and name for this category of content
     *
     * @return  array
     */
    public function &onGroupAreas()
    {
        $area = array(
            'name' => $this->_name,
            'title' => Lang::txt('PLG_GROUPS_ACTIVITY'),
            'default_access' => $this->params->get('plugin_access', 'members'),
            'display_menu_tab' => $this->params->get('display_tab', 1),
            'icon' => 'f056'
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

        //are we returning html
        if ($return == 'html') {
            //set group members plugin access level
            $group_plugin_acl = $access[$active];

            //get the group members
            $members = $group->get('members');

            //if set to nobody make sure cant access
            if ($group_plugin_acl == 'nobody') {
                $arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_OFF', ucfirst($active)) . '</p>';
                return $arr;
            }

            $this->group = $group;
            $this->base  = 'index.php?option=com_groups&cn=' . $group->get('cn');

            //check if guest and force login if plugin access is registered or members
            if (
                User::isGuest()
                && ($group_plugin_acl == 'registered' || $group_plugin_acl == 'members')
            ) {
                $this->loginRedirect();
                return;
            }

            $siteAdmin = User::authorise('core.admin', 'com_groups');

            //check to see if user is member and plugin access requires members
            if (
                !in_array(User::get('id'), $members)
                && $group_plugin_acl == 'members'
                && !$siteAdmin
            ) {
                $arr['html'] = '<p class="info">' . Lang::txt('GROUPS_PLUGIN_REQUIRES_MEMBER', ucfirst($active)) . '</p>';
                return $arr;
            }

            // Managers and site administrators may hide any entry in the feed.
            // Starring marks the entry for the whole group, so it is for members.
            $this->canModerate = ($authorized == 'manager' || $siteAdmin);
            $this->canStar     = ($this->canModerate || $authorized == 'member');

            $action = Request::getCmd('action', 'feed');

            if ($this->group->published != 1) {
                $action = 'feed';
            }

            switch ($action) {
                case 'post':
                    $arr['html'] = $this->postAction();
                    break;
                case 'remove':
                    $arr['html'] = $this->removeAction();
                    break;
                case 'unstar':
                    $arr['html'] = $this->starAction();
                    break;
                case 'star':
                    $arr['html'] = $this->starAction();
                    break;
                case 'feed':
                default:
                    $arr['html'] = $this->feedAction();
                    break;
            }

            $arr['html'] = $this->feedAction();
        }

        // Get the number of unread messages
        $unread = \Hubzero\Activity\Recipient::all()
            ->whereEquals('scope', 'group')
            ->whereEquals('scope_id', $group->get('gidNumber'))
            ->whereEquals('state', 1)
            ->where('viewed', 'IS', null)
            ->total();

        // Return total message count
        $arr['metadata']['count'] = $unread;

        // Return data
        return $arr;
    }

    /**
     * Show a feed
     *
     * @return  string
     */
    protected function feedAction()
    {
        $filters = array();
        $filters['filter'] = Request::getWord('filter');
        $filters['search'] = Request::getString('q');
        $filters['limit']  = Request::getInt('limit', Config::get('list_limit'));
        $filters['start']  = Request::getInt('start', 0);

        if (!in_array($filters['filter'], ['starred'])) {
            $filters['filter'] = '';
        }

        $recipient = \Hubzero\Activity\Recipient::all();

        $r = $recipient->getTableName();
        $l = \Hubzero\Activity\Log::blank()->getTableName();

        $scopes = array('group');
        if (in_array(User::get('id'), $this->group->get('managers'))) {
            $scopes[] = 'group_managers';
        }

        $recipient
            ->select($r . '.*')
            ->including('log')
            ->join($l, $l . '.id', $r . '.log_id')
            ->whereIn($r . '.scope', $scopes)
            ->whereEquals($r . '.scope_id', $this->group->get('gidNumber'))
            ->whereEquals($r . '.state', \Hubzero\Activity\Recipient::STATE_PUBLISHED);

        if ($filters['filter'] == 'starred') {
            $recipient->whereEquals($r . '.starred', 1);
        }

        if ($filters['search']) {
            $recipient->whereLike($l . '.description', $filters['search']);
        }

        if (!$filters['filter'] && !$filters['search']) {
            $recipient->whereEquals($l . '.parent', 0);
        }

        $total = $recipient->copy()->total();

        $entries = $recipient
            ->ordered()
            //->paginated()
            ->limit($filters['limit'])
            ->start($filters['start'])
            ->rows();

        $view = $this->view('default', 'activity')
            ->set('name', $this->_name)
            ->set('group', $this->group)
            ->set('canModerate', $this->canModerate)
            ->set('canStar', $this->canStar)
            ->set('plugin', $this)
            ->set('categories', null)
            ->set('filters', $filters)
            ->set('total', $total)
            ->set('rows', $entries);

        return $view->loadTemplate();
    }

    /**
     * Is this a post (a comment or a reply) written in this feed by the
     * current user? Only those can be edited here.
     *
     * @param   object   $log  \Hubzero\Activity\Log
     * @return  boolean
     */
    public function isOwnPost($log)
    {
        if (!$this->isAuthor($log)) {
            return false;
        }

        return ($log->get('scope') == 'activity.comment' || $log->get('parent'));
    }

    /**
     * Is this something the current user wrote -- a post here, or a forum,
     * blog, wiki, project ... entry relayed to the feed -- rather than an
     * event about the group itself?
     *
     * Group events ("X joined the group", a changed page or setting) carry
     * the id of the user they are about, so authorship alone doesn't make
     * an entry theirs to hide.
     *
     * @param   object   $log  \Hubzero\Activity\Log
     * @return  boolean
     */
    public function isOwnContent($log)
    {
        if ($this->isOwnPost($log)) {
            return true;
        }

        if (!$this->isAuthor($log)) {
            return false;
        }

        $scope = (string) $log->get('scope');

        return !($scope == 'group' || strpos($scope, 'group.') === 0 || substr($scope, -9) == '.settings');
    }

    /**
     * Did the current (logged-in) user create this log entry?
     *
     * @param   object   $log  \Hubzero\Activity\Log
     * @return  boolean
     */
    protected function isAuthor($log)
    {
        return !User::isGuest() && (int) $log->get('created_by') === (int) User::get('id');
    }

    /**
     * Did this group's feed receive the given log entry?
     *
     * @param   integer  $logId
     * @return  boolean
     */
    protected function receivedByGroup($logId)
    {
        return (bool) $this->groupAudience($logId);
    }

    /**
     * Which of this group's feeds received the given log entry: 'group' (all
     * members), 'group_managers' (managers only), or null for neither
     *
     * @param   integer  $logId
     * @return  string|null
     */
    protected function groupAudience($logId)
    {
        $scopes = \Hubzero\Activity\Recipient::all()
            ->select('scope')
            ->whereEquals('log_id', (int) $logId)
            ->whereIn('scope', array('group', 'group_managers'))
            ->whereEquals('scope_id', (int) $this->group->get('gidNumber'))
            ->rows()
            ->fieldsByKey('scope');

        if (in_array('group', $scopes)) {
            return 'group';
        }

        return in_array('group_managers', $scopes) ? 'group_managers' : null;
    }

    /**
     * Load a feed entry, which must belong to this group
     *
     * @param   integer  $id
     * @return  object
     */
    protected function groupEntry($id)
    {
        $entry = \Hubzero\Activity\Recipient::oneOrFail($id);

        if (!in_array($entry->get('scope'), array('group', 'group_managers')) || $entry->get('scope_id') != $this->group->get('gidNumber')) {
            App::abort(403, Lang::txt('You are not authorized to perform this action.'));
        }

        return $entry;
    }

    /**
     * Send a guest to log in, returning to the group's activity feed
     *
     * @return  void
     */
    protected function loginRedirect()
    {
        $url = Route::url($this->base . '&active=' . $this->_name, false, true);

        App::redirect(
            Route::url('index.php?option=com_users&view=login&return=' . base64_encode($url)),
            Lang::txt('GROUPS_PLUGIN_REGISTERED', ucfirst($this->_name)),
            'warning'
        );
    }

    /**
     * Unpublish an entry
     *
     * @return  string
     */
    protected function removeAction()
    {
        if (User::isGuest()) {
            return $this->loginRedirect();
        }

        // Check for request forgeries
        Request::checkToken();

        $id      = Request::getInt('activity', 0);
        $no_html = Request::getInt('no_html', 0);

        $entry = $this->groupEntry($id);

        // Managers may hide any entry; anyone else only what they wrote
        if (!$this->canModerate && !$this->isOwnContent($entry->log)) {
            App::abort(403, Lang::txt('You are not authorized to perform this action.'));
        }

        if (!$entry->markAsUnpublished()) {
            $this->setError($entry->getError());
        }

        $success = Lang::txt('PLG_GROUPS_ACTIVITY_RECORD_REMOVED');

        if ($no_html) {
            $response = new \stdClass();
            $response->success = true;
            $response->message = $success;
            if ($err = $this->getError()) {
                $response->success = false;
                $response->message = $err;
            }

            ob_clean();
            header('Content-type: text/plain');
            echo json_encode($response);
            exit();
        }

        if ($err = $this->getError()) {
            Notify::error($err);
        } else {
            Notify::success($success);
        }

        // Redirect
        App::redirect(
            Route::url($this->base . '&active=' . $this->_name, false)
        );
    }

    /**
     * Star/unstar an entry
     *
     * @return  string
     */
    protected function starAction()
    {
        if (User::isGuest()) {
            return $this->loginRedirect();
        }

        if (!$this->canStar) {
            App::abort(403, Lang::txt('You are not authorized to perform this action.'));
        }

        // Check for request forgeries
        Request::checkToken();

        $id      = Request::getInt('activity', 0);
        $no_html = Request::getInt('no_html', 0);
        $action  = Request::getWord('action', 'star');

        $entry = $this->groupEntry($id);

        // Members star what they can see: top-level entries, and the managers'
        // channel only for managers
        if ($entry->log->get('parent') || ($entry->get('scope') == 'group_managers' && !$this->canModerate)) {
            App::abort(403, Lang::txt('You are not authorized to perform this action.'));
        }
        $entry->set('starred', ($action == 'star' ? 1 : 0));

        if (!$entry->save()) {
            $this->setError($entry->getError());
        }

        $success = $action == 'star'
            ? Lang::txt('PLG_GROUPS_ACTIVITY_RECORD_STARRED')
            : Lang::txt('PLG_GROUPS_ACTIVITY_RECORD_UNSTARRED');

        if ($no_html) {
            $response = new \stdClass();
            $response->success = true;
            $response->message = $success;
            $response->starred = ($action == 'star');
            if ($err = $this->getError()) {
                $response->success = false;
                $response->message = $err;
            }

            ob_clean();
            header('Content-type: text/plain');
            echo json_encode($response);
            exit();
        }

        if ($err = $this->getError()) {
            Notify::error($err);
        } else {
            Notify::success($success);
        }

        // Redirect
        App::redirect(
            Route::url($this->base . '&active=' . $this->_name, false)
        );
    }

    /**
     * Save a comment
     *
     * @return  void
     */
    protected function postAction()
    {
        if (User::isGuest()) {
            return $this->loginRedirect();
        }

        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $comment = Request::getArray('activity', array(), 'post');

        // Instantiate a new object and bind data
        $lid = isset($comment['id']) ? (int) $comment['id'] : 0;
        $row = \Hubzero\Activity\Log::oneOrNew($lid);

        // An id that matches no entry makes a new post, checked as one
        $isEdit = !$row->isNew();

        // Only the author may change a post, and only one this group received
        if ($isEdit) {
            if (!$this->receivedByGroup($row->get('id')) || !$this->isOwnPost($row)) {
                App::abort(403, Lang::txt('You are not authorized to perform this action.'));
            }
        }

        // Only the body and the anonymous flag come from the form. The rest
        // of activity[] (scope, scope_id, created_by, details) is decided
        // here: the log is this group's, by this user, and the body is
        // editor HTML that the feed echoes as-is, so purify it.
        $row->set(array(
            'description' => \Hubzero\Utility\Sanitize::html((string) (isset($comment['description']) ? $comment['description'] : '')),
            'anonymous'   => (isset($comment['anonymous']) ? (int) $comment['anonymous'] : 0)
        ));

        if ($row->isNew()) {
            $scope   = 'activity.comment';
            $scopeId = (int) $this->group->get('gidNumber');
            $parent  = isset($comment['parent']) ? (int) $comment['parent'] : 0;

            // Starting a discussion is for managers, as the form shows; a reply
            // in the managers' channel is for managers too
            if (
                (!$parent && !$this->canModerate)
                || ($parent && $this->groupAudience($parent) == 'group_managers' && !$this->canModerate)
            ) {
                App::abort(403, Lang::txt('You are not authorized to perform this action.'));
            }

            // A reply inherits the scope of the entry it answers, which has to
            // be one this group received
            if ($parent) {
                $parentLog = \Hubzero\Activity\Log::oneOrNew($parent);

                if (!$parentLog->get('id') || !$this->receivedByGroup($parent)) {
                    App::abort(404, Lang::txt('You are not authorized to perform this action.'));
                }

                $scope   = $parentLog->get('scope');
                $scopeId = $parentLog->get('scope_id');
            }

            $row->set('action', 'created');
            $row->set('parent', $parent);
            $row->set('scope', $scope);
            $row->set('scope_id', $scopeId);
            $row->set('created_by', User::get('id'));
        }

        // Process attachment
        $upload = Request::getArray('activity_file', '', 'files');

        if (!empty($upload['name'])) {
            if ($upload['error']) {
                $this->setError(\Lang::txt('PLG_GROUPS_ACTIVITY_ERROR_UPLOADING_FILE'));
            }

            $file = new \Plugins\Groups\Activity\Models\Attachment();
            $file->setUploadDir('/site/groups/' . $this->group->get('gidNumber') . '/uploads');

            if (!$file->upload($upload['name'], $upload['tmp_name'], $upload['size'])) {
                App::redirect(
                    Route::url($this->base . '&active=' . $this->_name),
                    $file->getError(),
                    'error'
                );
            } else {
                $row->details->set('attachments', array(
                    $file->toArray()
                ));
                $row->set('details', $row->details->toString());
            }
        }

        // Store new content
        if (!$row->save()) {
            User::setState(
                'failed_comment',
                $row->get('description')
            );

            App::redirect(
                Route::url($this->base . '&active=' . $this->_name),
                $row->getError(),
                'error'
            );
        }

        // An edit stays where the post was made and a reply goes where the entry
        // it answers went; only a new discussion picks its audience
        if ($isEdit) {
            $audience = $this->groupAudience($row->get('id'));
        } elseif ($row->get('parent')) {
            $audience = $this->groupAudience($row->get('parent'));
        } else {
            $audience = (Request::getWord('activity_recipients', 'all') == 'managers') ? 'group_managers' : 'group';
        }

        // Record the activity
        $recipients = array();
        $recipients[] = [$audience, $this->group->get('gidNumber')];
        $recipients[] = ['user', $row->get('created_by')];
        if ($row->get('parent')) {
            $recipients[] = ['user', $row->parent()->get('created_by')];
        }

        Event::trigger('system.logActivity', [
            'activity' => [
                'id'          => $row->get('id'),
                'action'      => ($isEdit ? 'updated' : 'created'),
                'scope'       => $row->get('scope'),
                'scope_id'    => $row->get('scope_id'),
                'anonymous'   => $row->get('anonymous', 0),
                'description' => $row->get('description'),
                'details'     => array(
                    'url'         => Route::url($this->base . '&active=' . $this->_name . '#activity' . $row->get('id')),
                    'title'       => $this->group->get('description'),
                    'attachments' => $row->details->get('attachments')
                )
            ],
            'recipients' => $recipients
        ]);

        // Redirect
        App::redirect(
            Route::url($this->base . '&active=' . $this->_name),
            Lang::txt('PLG_GROUPS_ACTIVITY_COMMENTS_SAVED')
        );
    }
}
