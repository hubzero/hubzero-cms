<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Components\Members\Models\Note;
use Components\Members\Models\Member;
use Hubzero\Component\AdminController;
use Hubzero\Facades\Request;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

/**
 * Manage member notes
 */
class Notes extends AdminController
{
    /**
     * Execute a task
     *
     * @return  void
     */
    public function execute()
    {
        Lang::load($this->option . '.notes', dirname(__DIR__));

        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('save2new', 'save');
        $this->registerTask('save2copy', 'save');
        $this->registerTask('publish', 'state');
        $this->registerTask('unpublish', 'state');
        $this->registerTask('archive', 'state');
        $this->registerTask('trash', 'state');
        $this->registerTask('delete', 'remove');

        parent::execute();
    }

    /**
     * Display notes
     *
     * @return  void
     */
    public function displayTask()
    {
        // Incoming
        $filters = [
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            )),
            'state' => Request::getState(
                $this->option . '.' . $this->controller . '.state',
                'state',
                -1,
                'int',
            ),
            'access' => Request::getState(
                $this->option . '.' . $this->controller . '.access',
                'access',
                0,
                'int',
            ),
            'category_id' => Request::getState(
                $this->option . '.' . $this->controller . '.category_id',
                'category_id',
                0,
                'int',
            ),
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'review_time',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sort_Dir',
                'filter_order_Dir',
                'DESC',
            ),
        ];

        $entries = Note::all()
            ->including(['member', function ($member) {
                $member->select('*');
            }])
            ->including(['category', function ($category) {
                $category
                    ->select('id')
                    ->select('title');
            }]);

        if ($filters['search']) {
            if (substr($filters['search'], 0, strlen('uid:')) == 'uid:') {
                $entries->whereEquals('user_id', (int) substr($filters['search'], strlen('uid:')));
            } else {
                $entries
                    ->whereLike('subject', strtolower((string) $filters['search']), 1)
                    ->orWhereLike('body', strtolower((string) $filters['search']), 1)
                    ->resetDepth();
            }
        }

        if ($filters['state'] >= 0) {
            $entries->whereEquals('state', (int) $filters['state']);
        }

        if ($filters['access']) {
            $entries->whereEquals('access', (int) $filters['access']);
        }

        if ($filters['category_id']) {
            $entries->whereEquals('catid', (int) $filters['category_id']);
        }

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
     * Edit an entry
     *
     * @param   \Components\Members\Models\Note|null  $row
     *
     * @return  void
     */
    public function editTask($row = null)
    {
        Request::setVar('hidemainmenu', 1);

        if (!$row) {
            // Incoming
            $id = Request::getArray('id', []);

            // Get the single ID we're working with
            $id = (!empty($id)) ? $id[0] : 0;

            $row = Note::oneOrNew($id);
        }

        if ($row->isNew()) {
            $row->set('user_id', Request::getInt('user_id', 0));
            $row->set('catid', Request::getInt('category_id', 0));
        }

        // Output the HTML
        $this->view
            ->set('row', $row)
            ->setErrors($this->getErrors())
            ->setLayout('edit')
            ->display();
    }

    /**
     * Save entry
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming password blacklist edits
        $fields = Request::getArray('fields', [], 'post');

        // Save as Copy: a new note with this one's fields
        if ($this->getTask() == 'save2copy') {
            $fields['id'] = 0;
        }

        // Load the record
        $row = Note::oneOrNew($fields['id'] ?? 0)->set($fields);

        // Try to save
        if (!$row->save()) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        Notify::success(Lang::txt('COM_MEMBERS_NOTES_SAVE_SUCCESS'));

        // Fall through to edit form
        if ($this->getTask() == 'apply' || $this->getTask() == 'save2copy') {
            $this->editTask($row);

            return;
        }

        if ($this->getTask() == 'save2new') {
            App::redirect(
                Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=add',
                    false,
                ),
            );
        }

        // Redirect
        $this->cancelTask();
    }

    /**
     * Publish, unpublish, archive or trash one or more notes
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

        $states = [
            'publish'   => [1, 'COM_MEMBERS_NOTES_N_ITEMS_PUBLISHED'],
            'unpublish' => [0, 'COM_MEMBERS_NOTES_N_ITEMS_UNPUBLISHED'],
            'archive'   => [2, 'COM_MEMBERS_NOTES_N_ITEMS_ARCHIVED'],
            'trash'     => [-2, 'COM_MEMBERS_NOTES_N_ITEMS_TRASHED'],
        ];
        [$state, $message] = $states[$this->getTask()] ?? $states['unpublish'];

        $changed = 0;
        foreach (Request::getArray('id', []) as $id) {
            $row = Note::oneOrNew((int) $id);
            if ($row->isNew()) {
                continue;
            }

            $row->set('state', $state);
            if (!$row->save()) {
                Notify::error($row->getError());
                continue;
            }

            $changed++;
        }

        if ($changed) {
            Notify::success(Lang::txt($message, $changed));
        }

        $this->cancelTask();
    }

    /**
     * Check in one or more notes left checked out
     *
     * @return  void
     */
    public function checkinTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        if (!User::authorise('core.edit.state', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $changed = 0;
        foreach (Request::getArray('id', []) as $id) {
            $row = Note::oneOrNew((int) $id);
            if ($row->isNew()) {
                continue;
            }

            // checked_out_time is NOT NULL; checked_out alone says who holds it
            $row->set('checked_out', 0);
            if ($row->save()) {
                $changed++;
            }
        }

        if ($changed) {
            Notify::success(Lang::txt('COM_MEMBERS_NOTES_N_ITEMS_CHECKED_IN', $changed));
        }

        $this->cancelTask();
    }

    /**
     * Removes one or entries
     *
     * @return  void
     */
    public function removeTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (!User::authorise('core.delete', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Incoming
        $ids = Request::getArray('id', []);

        $i = 0;

        // Do we have any IDs?
        if (!empty($ids)) {
            // Loop through each ID and delete the necessary items
            foreach ($ids as $id) {
                $id = intval($id);

                // Remove the record
                $row = Note::oneOrFail($id);

                if (!$row->destroy()) {
                    Notify::error($row->getError());
                    continue;
                }

                $i++;
            }
        } else { // no rows were selected
            Notify::warning(Lang::txt('COM_MEMBERS_NOTES_DELETE_NO_ROW_SELECTED'));
        }

        // Output messsage and redirect
        if ($i) {
            Notify::success(Lang::txt('COM_MEMBERS_NOTES_DELETE_SUCCESS'));
        }

        $this->cancelTask();
    }

    /**
     * Display notes for a user
     *
     * @return  void
     */
    public function modalTask()
    {
        Request::setVar('hidemainmenu', 1);

        $user = Member::oneOrFail(Request::getInt('id', 0));

        $rows = Note::all()
            ->including(['category', function ($category) {
                $category
                    ->select('id')
                    ->select('title');
            }])
            ->whereEquals('user_id', (int) $user->get('id'))
            ->ordered()
            ->rows();

        // Output the HTML
        $this->view
            ->set('user', $user)
            ->set('rows', $rows)
            ->setErrors($this->getErrors())
            ->display();
    }
}
