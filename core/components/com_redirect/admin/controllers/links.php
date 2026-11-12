<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Admin\Controllers;

use Components\Redirect\Helpers\Redirect as Helper;
use Components\Redirect\Models\Link;
use Hubzero\Component\AdminController;
use Exception;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;
use App;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Submenu;

/**
 * Redirect link list controller class.
 */
class Links extends AdminController
{
    /**
     * Determine task and execute it
     *
     * @return  void
     */
    public function execute()
    {
        // Value = 0
        $this->registerTask('unpublish', 'publish');

        // Value = 2
        $this->registerTask('archive', 'publish');

        // Value = -2
        $this->registerTask('trash', 'publish');

        $this->registerTask('apply', 'save');
        $this->registerTask('save2new', 'save');
        $this->registerTask('save2copy', 'save');

        parent::execute();
    }

    /**
     * Display
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
            'state' => Request::getState(
                $this->option . '.' . $this->controller . '.state',
                'state',
                '*',
            ),
            'type' => Request::getState(
                $this->option . '.' . $this->controller . '.type',
                'type',
                'redirect',
            ),
            // Get sorting variables
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'created_date',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'desc',
            ),
        ];

        $entries = Link::all();

        if ($filters['state'] != '*') {
            $entries->whereEquals('published', (int) $filters['state']);
        }

        if ($filters['type'] != '404') {
            $entries->where('new_url', '!=', '');
        } else {
            $entries->whereEquals('new_url', '');
        }

        if ($filters['search']) {
            $filters['search'] = strtolower((string) $filters['search']);

            $entries
                ->whereLike('old_url', $filters['search'], 1)
                ->orWhereLike('new_url', $filters['search'], 1)
                ->orWhereLike('comment', $filters['search'], 1)
                ->orWhereLike('referer', $filters['search'], 1)
                ->resetDepth();
        }

        // Get records
        $rows = $entries
            ->order($filters['sort'], $filters['sort_Dir'])
            ->paginated('limitstart', 'limit')
            ->rows();

        Submenu::addEntry(
            Lang::txt('COM_REDIRECT_REDIRECTS'),
            Route::url('index.php?option=' . $this->option . '&type=redirect'),
            ($filters['type'] != '404'),
        );
        Submenu::addEntry(
            Lang::txt('COM_REDIRECT_NOTFOUND'),
            Route::url('index.php?option=' . $this->option . '&type=404'),
            ($filters['type'] == '404'),
        );

        $this->view
            ->set('rows', $rows)
            ->set('filters', $filters)
            ->set('enabled', Helper::isEnabled())
            ->display();
    }

    /**
     * Method to add a new record.
     *
     * @return  void
     */
    public function addTask()
    {
        // Access check.
        if (!(User::authorise('core.create', $this->option))) {
            Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
            $this->cancelTask();

            return;
        }

        $this->editTask();
    }

    /**
     * Display edit form
     *
     */
    public function editTask($row = null)
    {
        // Access check.
        if (!User::authorise('core.edit', $this->option)) {
            Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->cancelTask();

            return;
        }

        if (!is_object($row)) {
            // Incoming
            $id = Request::getArray('id', [0]);
            if (is_array($id) && !empty($id)) {
                $id = $id[0];
            }

            // Load the article
            $row = Link::oneOrNew($id);
        }

        $this->view
            ->set('row', $row)
            ->setLayout('edit')
            ->setError($this->getErrors())
            ->display();
    }

    /**
     * Method to save a record.
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries.
        Request::checkToken();

        // Access check.
        if (!User::authorise('core.edit', $this->option) && !User::authorise('core.create', $this->option)) {
            Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
            $this->cancelTask();

            return;
        }

        // Initialise variables.
        $fields = Request::getArray('fields', [], 'post');

        // The save2copy task needs to be handled slightly differently.
        if ($this->getTask() == 'save2copy') {
            // Reset the ID and then treat the request as for Apply.
            $fields['id'] = 0;
            $this->task = 'apply';
        }

        // The form posts hits as '' and carries no referer; both columns are
        // NOT NULL integers/strings without a usable default under strict SQL,
        // so a new redirect could not be saved
        if (isset($fields['hits']) && trim((string) $fields['hits']) === '') {
            unset($fields['hits']);
        }

        $row = Link::oneOrNew($fields['id'])->set($fields);

        if ($row->get('referer') === null) {
            $row->set('referer', '');
        }

        // Attempt to save the data.
        if (!$row->save()) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        Notify::success(Lang::txt('COM_REDIRECT_SAVE_SUCCESS'));

        if ($this->getTask() == 'apply') {
            $this->editTask($row);

            return;
        }

        // Redirect the user
        $this->cancelTask();
    }

    /**
     * Method to update a record.
     *
     * @return  void
     */
    public function activateTask()
    {
        // Check for request forgeries.
        Request::checkToken(['get', 'post']);

        // Access check.
        if (!User::authorise('core.edit', $this->option)) {
            Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->cancelTask();

            return;
        }

        // Initialise variables.
        $ids     = Request::getArray('id', [], '');
        $newUrl  = Request::getString('new_url');
        $comment = Request::getString('comment');

        if (empty($ids)) {
            Notify::error(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'));
        }

        $updated = 0;

        // Loop through all the IDs
        foreach ($ids as $id) {
            $entry = Link::oneOrFail(intval($id));
            $entry->set('new_url', $newUrl);
            $entry->set('comment', $comment);
            $entry->set('published', 1);

            // Remove the items.
            if (!$entry->save()) {
                Notify::error($entry->getError());
                continue;
            }

            $updated++;
        }

        if ($updated) {
            Notify::success(Lang::txt('COM_REDIRECT_N_LINKS_UPDATED', $updated));
        }

        $this->cancelTask();
    }

    /**
     * Method to publish a list of items
     *
     * @return  void
     */
    public function publishTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        // Access check.
        if (!User::authorise('core.edit', $this->option)) {
            Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->cancelTask();

            return;
        }

        // Get items to publish from the request.
        $ids = Request::getArray('id', [], '');
        $data = [
            'publish'   => 1,
            'unpublish' => 0,
            'archive'   => 2,
            'trash'     => -2,
            'report'    => -3,
        ];

        $value = \Hubzero\Utility\Arr::getValue($data, $this->task, 0, 'int');

        if (empty($ids)) {
            Notify::error(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'));
        }

        $updated = 0;

        // Loop through all the IDs
        foreach ($ids as $id) {
            $entry = Link::oneOrFail(intval($id));
            $entry->set('published', $value);

            // Remove the items.
            if (!$entry->save()) {
                Notify::error($entry->getError());
                continue;
            }

            $updated++;
        }

        if ($value == 1) {
            $ntext = 'COM_REDIRECT_N_ITEMS_PUBLISHED';
        } elseif ($value == 0) {
            $ntext = 'COM_REDIRECT_N_ITEMS_UNPUBLISHED';
        } elseif ($value == 2) {
            $ntext = 'COM_REDIRECT_N_ITEMS_ARCHIVED';
        } else {
            $ntext = 'COM_REDIRECT_N_ITEMS_TRASHED';
        }
        Notify::success(Lang::txts($ntext, $updated));

        $this->cancelTask();
    }

    /**
     * Remove one or more entries
     *
     * @return  void
     */
    public function removeTask()
    {
        // Access check.
        if (!User::authorise('core.delete', $this->option)) {
            Notify::warning(Lang::txt('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
            $this->cancelTask();

            return;
        }

        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        $ids = Request::getArray('id', [], '');

        if (empty($ids)) {
            Notify::error(Lang::txt('COM_REDIRECT_NO_ITEM_SELECTED'));
        }

        $deleted = 0;
        foreach ($ids as $id) {
            $entry = Link::oneOrFail(intval($id));

            if (!$entry->destroy()) {
                Notify::error($entry->getError());
                continue;
            }

            $deleted++;
        }

        if ($deleted) {
            Notify::success(Lang::txts('COM_REDIRECT_N_ITEMS_DELETED', $deleted));
        }

        $this->cancelTask();
    }
}
