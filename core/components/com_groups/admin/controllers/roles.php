<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Components\Groups\Models\Role;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\App;
use Hubzero\Facades\Notify;

/**
 * Groups controller class for managing membership roles
 */
class Roles extends AdminController
{
    /**
     * Determine task and execute it
     *
     * @return  void
     */
    public function execute()
    {
        if (!User::authorize('core.manage', $this->option)) {
            App::redirect(
                Route::url('index.php?option=' . $this->option, false),
            );
        }

        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');

        parent::execute();
    }

    /**
     * Displays a list of groups
     *
     * @return  void
     */
    public function displayTask()
    {
        // Incoming
        $filters = [
            'gid' => Request::getState(
                $this->option . '.' . $this->controller . '.gid',
                'gid',
                '',
            ),
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            )),
            // Sorting options
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'name',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'ASC',
            ),
            // Filters for returning results
            'limit' => Request::getState(
                $this->option . '.' . $this->controller . '.limit',
                'limit',
                Config::get('list_limit'),
                'int',
            ),
            'start' => Request::getState(
                $this->option . '.' . $this->controller . '.limitstart',
                'limitstart',
                0,
                'int',
            ),
        ];

        // Ensure we have a group ID
        if (!$filters['gid']) {
            App::redirect(
                Route::url('index.php?option=' . $this->option, false),
                Lang::txt('COM_GROUPS_MISSING_ID'),
                'error',
            );
        }

        // Load the group page
        $group = new Group();
        $group->read($filters['gid']);

        $filters['gidNumber'] = $group->get('gidNumber');

        $rows = Role::all()
            ->whereEquals('gidNumber', $filters['gidNumber'])
            ->ordered()
            ->paginated()
            ->rows();

        // Output the HTML
        $this->view
            ->set('filters', $filters)
            ->set('group', $group)
            ->set('rows', $rows)
            ->display();
    }

    /**
     * Edit an entry
     *
     * @param   object  $model
     */
    public function editTask($model = null)
    {
        Request::setVar('hidemainmenu', 1);

        // Load a tag object if one doesn't already exist
        if (!is_object($model)) {
            // Incoming
            $id = Request::getArray('id', [0]);
            if (is_array($id) && !empty($id)) {
                $id = $id[0];
            }

            $model = Role::oneOrNew(intval($id));
        }

        $gid = Request::getString('gid', '');

        $group = new Group();
        $group->read($gid);

        // Output the HTML
        $this->view
            ->set('model', $model)
            ->set('group', $group)
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

        $fields = Request::getArray('fields', [], 'post');

        $row = Role::oneOrNew(intval($fields['id']))->set($fields);

        if (!isset($fields['permissions'])) {
            $fields['permissions'] = [];
        }

        $permissions = new \Hubzero\Config\Registry($fields['permissions']);

        $row->set('permissions', $permissions->toString());

        // Store new content
        if (!$row->save()) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        Notify::success(Lang::txt('COM_GROUPS_ROLE_SAVED'));

        // Redirect to main listing
        if ($this->getTask() == 'apply') {
            $this->editTask($row);

            return;
        }

        $this->cancelTask();
    }

    /**
     * Remove one or more entries
     *
     * @return  void
     */
    public function removeTask()
    {
        // Check for request forgeries
        Request::checkToken();

        $ids = Request::getArray('id', []);
        $ids = (!is_array($ids) ? [$ids] : $ids);

        // Make sure we have an ID
        if (empty($ids)) {
            Notify::warning(Lang::txt('COM_GROUPS_ERROR_NO_ITEMS_SELECTED'));
            $this->cancelTask();

            return;
        }

        $i = 0;

        foreach ($ids as $id) {
            // Remove the entry
            $model = Role::oneOrFail(intval($id));

            if (!$model->destroy()) {
                Notify::error($model->getError());
                continue;
            }

            $i++;
        }

        if ($i) {
            Notify::success(Lang::txt('COM_GROUPS_ROLE_REMOVED'));
        }

        $this->cancelTask();
    }

    /**
     * Assign members to a role
     *
     * @return  void
     */
    public function unassignTask()
    {
        Request::setVar('hidemainmenu', 1);

        $ids = Request::getArray('id', []);
        $ids = (!is_array($ids) ? [$ids] : $ids);
        $gid = Request::getString('gid', '');
        $roleid = Request::getInt('roleid', 0);

        if (!$gid) {
            App::redirect(
                Route::url('index.php?option=' . $this->option, false),
                Lang::txt('COM_GROUPS_MISSING_ID'),
                'error',
            );
        }

        foreach ($ids as $id) {
            $model = \Components\Groups\Models\Member\Role::oneByUserAndRole((int) $id, $roleid);

            if ($model->get('id')) {
                $model->destroy();
            }
        }

        if ($rtrn = Request::getString('return')) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $rtrn . '&gid=' . $gid, false),
            );
        }

        // Output the HTML
        $this->cancelTask();
    }

    /**
     * Assign members to a role
     *
     * @return  void
     */
    public function assignTask()
    {
        Request::setVar('hidemainmenu', 1);

        $ids = Request::getArray('id', []);
        $ids = (!is_array($ids) ? [$ids] : $ids);
        $gid = Request::getString('gid', '');

        if (!$gid) {
            App::redirect(
                Route::url('index.php?option=' . $this->option, false),
                Lang::txt('COM_GROUPS_MISSING_ID'),
                'error',
            );
        }

        $group = new Group();
        $group->read($gid);

        $rows = Role::all()
            ->whereEquals('gidNumber', $group->get('gidNumber'))
            ->rows();

        // Output the HTML
        $this->view
            ->set('rows', $rows)
            ->set('group', $group)
            ->set('ids', $ids)
            ->setLayout('assign')
            ->display();
    }

    /**
     * Assign members to a role
     *
     * @return  void
     */
    public function delegateTask()
    {
        Request::setVar('hidemainmenu', 1);

        $ids = Request::getArray('id', []);
        $ids = (!is_array($ids) ? [$ids] : $ids);
        $gid = Request::getString('gid', '');
        $roleid = Request::getInt('roleid', 0);

        if (!$gid) {
            App::redirect(
                Route::url('index.php?option=' . $this->option, false),
                Lang::txt('COM_GROUPS_MISSING_ID'),
                'error',
            );
        }

        $group = new Group();
        $group->read($gid);

        foreach ($ids as $id) {
            $model = \Components\Groups\Models\Member\Role::oneByUserAndRole((int) $id, $roleid);
            if (!$model->get('id')) {
                $model->set('roleid', $roleid);
                $model->set('uidNumber', (int) $id);
                $model->save();
            }
        }

        $this->cancelTask();
    }

    /**
     * Cancel task
     */
    public function cancelTask()
    {
        $gid  = Request::getString('gid', '');
        $tmpl = Request::getString('tmpl', '');
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&gid=' . $gid
            . ($tmpl ? '&tmpl=' . $tmpl : '');
        App::redirect(
            Route::url($url, false),
        );
    }
}
