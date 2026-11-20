<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Messages\Models\Message;
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

/**
 * Messages list controller class.
 */
class Messages extends AdminController
{
    /**
     * Execute a task
     *
     * @return  void
     */
    public function execute()
    {
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('publish', 'state');
        $this->registerTask('unpublish', 'state');
        $this->registerTask('trash', 'state');

        parent::execute();
    }

    /**
     * Display a list of blog entries
     *
     * @return  void
     */
    public function displayTask()
    {
        $filters = [
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            )),
            // -1: read and unread; 1 read, 0 unread, -2 trashed
            'state' => Request::getState(
                $this->option . '.' . $this->controller . '.state',
                'state',
                -1,
                'int',
            ),
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'date_time',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'desc',
            ),
        ];

        $entries = Message::all()
            ->including(['from', function ($from) {
                $from
                    ->select('id')
                    ->select('name');
            }])
            ->whereEquals('user_id_to', (int) User::get('id'));

        if ($filters['search']) {
            $entries
                ->whereLike('subject', strtolower((string) $filters['search']), 1)
                ->orWhereLike('message', strtolower((string) $filters['search']), 1)
                ->resetDepth();
        }

        if (in_array($filters['state'], [0, 1, -2], true)) {
            $entries->whereEquals('state', (int) $filters['state']);
        } else {
            $entries->whereIn('state', [0, 1]);
        }

        // Get records
        $rows = $entries
            ->order($filters['sort'], $filters['sort_Dir'])
            ->paginated('limitstart', 'limit')
            ->rows();

        // Output the HTML
        $this->view
            ->set('rows', $rows)
            ->set('filters', $filters)
            ->display();
    }

    /**
     * View an entry
     *
     * @return  void
     */
    public function viewTask()
    {
        Request::setVar('hidemainmenu', 1);

        // Incoming
        $id = Request::getInt('message_id', 0);

        // Load the article
        $row = Message::oneOrNew($id);

        if ($row->get('user_id_to') != User::get('id')) {
            Notify::warning(Lang::txt('JERROR_ALERTNOAUTHOR'));
            $this->cancelTask();

            return;
        }

        if (!$row->get('state')) {
            $row->set('state', 1);
            $row->save();
        }

        // Output the HTML
        $this->view
            ->set('item', $row)
            ->display();
    }

    /**
     * Show a form for editing an entry
     *
     * @param   \Components\Messages\Models\Message|null  $row
     *
     * @return  void
     */
    public function editTask($row = null)
    {
        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        Request::setVar('hidemainmenu', 1);

        if (!is_object($row)) {
            // Incoming
            $id = Request::getArray('id', [0]);
            if (!empty($id)) {
                $id = $id[0];
            }

            // Load the article
            $row = Message::oneOrNew($id);
        }

        if ($row->isNew()) {
            $row->set('user_id_from', User::get('id'));
            $row->set('date_time', Date::toSql());

            // A reply goes back to the sender of a message sent to this user
            $replyId = Request::getInt('reply_id');
            if ($replyId && !$row->get('user_id_to')) {
                $original = Message::oneOrNew($replyId);
                if (!$original->isNew() && (int) $original->get('user_id_to') === (int) User::get('id')) {
                    $row->set('user_id_to', $original->get('user_id_from'));
                    $row->set('subject', Lang::txt('COM_MESSAGES_RE') . ' ' . $original->get('subject'));
                }
            }
        }

        // Output the HTML
        $this->view
            ->set('item', $row)
            ->setLayout('edit')
            ->display();
    }

    /**
     * Save an entry
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Incoming
        $fields = Request::getArray('fields', [], 'post');

        // The form composes a new message; its sender is whoever sends it
        $row = Message::blank()->set([
            'user_id_from' => (int) User::get('id'),
            'user_id_to'   => (int) ($fields['user_id_to'] ?? 0),
            'subject'      => (string) ($fields['subject'] ?? ''),
            'message'      => (string) ($fields['message'] ?? ''),
        ]);

        // Store new content
        if (!$row->save()) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        $this->mail($row);

        Notify::success(Lang::txt('JLIB_APPLICATION_SAVE_SUCCESS'));

        if ($this->task == 'apply') {
            $this->editTask($row);

            return;
        }

        // Set the redirect
        $this->cancelTask();
    }

    /**
     * Sets the state of one or more entries
     *
     * @return  void
     */
    public function stateTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        if (!User::authorise('core.edit.state', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Read (published), unread (unpublished) or trashed (-2, as the state filter has it)
        $state = match ($this->task) {
            'publish' => Message::STATE_PUBLISHED,
            'trash'   => -2,
            default   => Message::STATE_UNPUBLISHED,
        };

        // Incoming
        $ids = Request::getArray('id', [0]);

        // Loop through all the IDs
        $success = 0;
        foreach ($ids as $id) {
            // Load the article
            $message = Message::oneOrNew(intval($id));
            $message->set('state', $state);

            if ((int) $message->get('user_id_to') !== (int) User::get('id')) {
                Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
                continue;
            }

            // Store new content
            if (!$message->save()) {
                Notify::error($message->getError());
                continue;
            }

            $success++;
        }

        if ($success) {
            // Anything but publish unpublished them (see $state above)
            if ($this->task == 'publish') {
                $text = Lang::txt('COM_MESSAGES_N_ITEMS_PUBLISHED', $success);
            } elseif ($this->task == 'trash') {
                $text = Lang::txt('COM_MESSAGES_N_ITEMS_TRASHED', $success);
            } else {
                $text = Lang::txt('COM_MESSAGES_N_ITEMS_UNPUBLISHED', $success);
            }

            Notify::success($text);
        }

        // Set the redirect
        $this->cancelTask();
    }

    /**
     * Delete one or more entries
     *
     * @return  void
     */
    public function deleteTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (!User::authorise('core.delete', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Incoming
        $ids = Request::getArray('id', []);

        $removed = 0;

        if (count($ids) > 0) {
            // Loop through all the IDs
            foreach ($ids as $id) {
                $message = Message::oneOrFail(intval($id));

                if ((int) $message->get('user_id_to') !== (int) User::get('id')) {
                    Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
                    continue;
                }

                // Delete the entry
                if (!$message->destroy()) {
                    Notify::error($message->getError());
                    continue;
                }

                $removed++;
            }
        }

        if ($removed) {
            Notify::success(Lang::txt('COM_MESSAGES_N_ITEMS_DELETED', $removed));
        }

        // Set the redirect
        $this->cancelTask();
    }

    /**
     * Reply to an existing message.
     *
     * This is a simple redirect to the compose form.
     *
     * @return  void
     */
    public function replyTask()
    {
        if ($replyId = Request::getInt('reply_id')) {
            App::redirect(Route::url('index.php?option=' . $this->option . '&task=edit&reply_id=' . $replyId));
        } else {
            Notify::warning(Lang::txt('COM_MESSAGES_INVALID_REPLY_ID'));
            $this->cancelTask();
        }
    }

    /**
     * Delete one or more entries
     *
     * @param   \Components\Messages\Models\Message  $message
     * @return  void
     */
    protected function mail($message)
    {
        if ($this->config->get('mail_on_new', true)) {
            // Load the user details (already valid from table check).
            $fromUser = $message->from;
            $toUser   = $message->to;

            $debug = Config::get('debug_lang');
            $default_language = Component::params('com_languages')->get('administrator');

            /*$lang = Lang::getInstance($toUser->getParam('admin_language', $default_language), $debug);
            $lang->load('com_messages', PATH_APP) ||
            $lang->load('com_messages', PATH_CORE . '/components/com_messages/admin');*/

            $siteURL  = Request::root()
                . 'administrator/index.php?option=com_messages&view=message&message_id=' . $message->id;
            $sitename = Config::get('sitename');

            $subject = Lang::txt('COM_MESSAGES_NEW_MESSAGE_ARRIVED', $sitename);
            $msg     = Lang::txt('COM_MESSAGES_PLEASE_LOGIN', $siteURL);

            $mailer = new \Hubzero\Mail\Message();
            $return = $mailer
                ->addFrom((string) $fromUser->email, (string) $fromUser->name)
                ->addTo((string) $toUser->email)
                ->setSubject($subject)
                ->setBody($msg)
                ->send();
        }
    }
}
