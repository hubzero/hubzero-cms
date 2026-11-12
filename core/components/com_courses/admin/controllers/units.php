<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin\Controllers;

use Components\Courses\Tables;
use Hubzero\Component\AdminController;
use Exception;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Notify;

/**
 * Courses controller class for managing membership and course info
 */
class Units extends AdminController
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
        $this->registerTask('orderup', 'order');
        $this->registerTask('orderdown', 'order');

        parent::execute();
    }

    /**
     * Displays a list of courses
     *
     * @return  void
     */
    public function displayTask()
    {
        // Incoming
        $this->view->set('filters', [
            'offering' => Request::getState(
                $this->option . '.' . $this->controller . '.offering',
                'offering',
                0,
            ),
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            )),
            'state' => Request::getState(
                $this->option . '.' . $this->controller . '.state',
                'state',
                '-1',
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
        ]);

        $this->view->set(
            'offering',
            \Components\Courses\Models\Offering::getInstance($this->view->get('filters')['offering']),
        );
        if (!$this->view->get('offering')->exists()) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=courses', false),
            );
        }
        $this->view->set(
            'course',
            \Components\Courses\Models\Course::getInstance($this->view->get('offering')->get('course_id')),
        );

        // In case limit has been changed, adjust limitstart accordingly
        $limit = $this->view->get('filters')['limit'];
        $start = $this->view->get('filters')['start'];
        $this->view->filters['start'] = ($limit != 0 ? (floor($start / $limit) * $limit) : 0);

        $this->view->filters['count'] = true;

        $this->view->set('total', $this->view->get('offering')->units($this->view->filters));

        $this->view->filters['count'] = false;

        $this->view->set('rows', $this->view->get('offering')->units($this->view->filters));

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * Displays an edit form
     *
     * @return  void
     */
    public function editTask($model = null)
    {
        Request::setVar('hidemainmenu', 1);

        if (!is_object($model)) {
            // Incoming
            $id = Request::getArray('id', [0]);

            // Get the single ID we're working with
            if (is_array($id)) {
                $id = (!empty($id)) ? $id[0] : 0;
            }

            $model = \Components\Courses\Models\Unit::getInstance($id);
        }

        $this->view->set('row', $model);

        if (!$this->view->get('row')->get('offering_id')) {
            $this->view->get('row')->set('offering_id', Request::getInt('offering', 0));
        }

        $this->view->set(
            'offering',
            \Components\Courses\Models\Offering::getInstance($this->view->get('row')->get('offering_id')),
        );

        // Set any errors
        foreach ($this->getErrors() as $error) {
            Notify::error($error);
        }

        // Output the HTML
        $this->view
            ->setLayout('edit')
            ->display();
    }

    /**
     * Saves data to the database
     *
     * @param     $redirect boolean Redirect after saving?
     * @return    void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $fields = Request::getArray('fields', [], 'post');

        // Instantiate a Course object
        $model = \Components\Courses\Models\Unit::getInstance($fields['id']);

        if (!$model->bind($fields)) {
            $this->setError($model->getError());
            $this->editTask($model);
            return;
        }

        if (!$model->store(true)) {
            $this->setError($model->getError());
            $this->editTask($model);
            return;
        }

        if ($model->get('id') && $model->assetgroups()->total() <= 0) {
            $asset_groups = explode(',', $this->config->get('default_asset_groups', 'Lectures, Homework, Exam'));
            $asset_groups = array_map('trim', $asset_groups);

            foreach ($asset_groups as $key) {
                // Get our asset group object
                $assetGroup = new \Components\Courses\Models\Assetgroup(null);
                $assetGroup->set('title', $key);
                $assetGroup->set('unit_id', $model->get('id'));
                $assetGroup->set('parent', 0);

                // Save the asset group
                if (!$assetGroup->store(true)) {
                    $this->setError($model->getError());
                }
            }
        }

        if ($this->task == 'apply') {
            $this->editTask($model);

            return;
        }

        // Output messsage and redirect
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&offering=' . Request::getInt('offering', 0);
        App::redirect(
            Route::url($url, false),
            Lang::txt('COM_COURSES_ITEM_SAVED'),
        );
    }

    /**
     * Copy an entry and all associated data
     *
     * @return  void
     */
    public function copyTask()
    {
        // Incoming
        $id = Request::getInt('id', 0);

        if (!$id) {
            $url = 'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&offering=' . Request::getInt('offering', 0);
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_COURSES_ERROR_NO_ID'),
                'error',
            );
        }

        $unit = \Components\Courses\Models\Unit::getInstance($id);
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&offering=' . $unit->get('offering_id');
        if (!$unit->copy()) {
            // Redirect back to the courses page
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $unit->getError(),
                'error',
            );
        }

        // Redirect back to the courses page
        App::redirect(
            Route::url($url, false),
            Lang::txt('COM_COURSES_ITEM_COPIED'),
        );
    }

    /**
     * Removes a course and all associated information
     *
     * @return  void
     */
    public function deleteTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $ids = Request::getArray('id', []);

        $num = 0;

        // Do we have any IDs?
        if (!empty($ids)) {
            foreach ($ids as $id) {
                // Load the course page
                $model = \Components\Courses\Models\Unit::getInstance($id);

                // Ensure we found the course info
                if (!$model->exists()) {
                    continue;
                }

                // Delete course
                if (!$model->delete()) {
                    throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
                }

                // Log the course approval
                $log = new Tables\Log($this->database);
                $log->scope_id  = $id;
                $log->scope     = 'course_unit';
                $log->user_id   = User::get('id');
                $log->timestamp = Date::toSql();
                $log->action    = 'unit_deleted';
                $log->actor_id  = User::get('id');
                if (!$log->store()) {
                    $this->setError($log->getError());
                }

                $num++;
            }
        }

        // Redirect back to the courses page
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&offering=' . Request::getInt('offering', 0);
        App::redirect(
            Route::url($url, false),
            Lang::txt('COM_COURSES_ITEMS_REMOVED', $num),
        );
    }

    /**
     * Set the state of an entry
     *
     * @return  void
     */
    public function stateTask()
    {
        $state = $this->task == 'publish' ? 1 : 0;

        // Incoming
        $ids = Request::getArray('id', []);

        // Check for an ID
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&offering=' . Request::getInt('offering', 0);
        if (count($ids) < 1) {
            $msg = $state == 1
                ? Lang::txt('COM_COURSES_SELECT_PUBLISH')
                : Lang::txt('COM_COURSES_SELECT_UNPUBLISH');
            App::redirect(
                Route::url($url, false),
                $msg,
                'error',
            );
        }

        // Update record(s)
        foreach ($ids as $id) {
            // Updating a category
            $row = new \Components\Courses\Models\Unit($id);
            $row->set('state', $state);
            $row->store();
        }

        // Set message
        if ($state == 1) {
            $message = Lang::txt('COM_COURSES_PUBLISHED', count($ids));
        } else {
            $message = Lang::txt('COM_COURSES_UNPUBLISHED', count($ids));
        }

        // Set the redirect
        App::redirect(
            Route::url($url, false),
            $message,
        );
    }

    /**
     * Reorder entries
     *
     * @return  void
     */
    public function orderTask()
    {
        // Check for request forgeries
        Request::checkToken();

        $id = Request::getArray('id', [0], 'post');
        \Hubzero\Utility\Arr::toInteger($id, [0]);

        $uid = $id[0];
        $inc = ($this->task == 'orderup' ? -1 : 1);

        $row = new Tables\Unit($this->database);
        $row->load($uid);
        $row->move($inc, 'offering_id=' . $this->database->Quote($row->offering_id));

        $offering = \Components\Courses\Models\Offering::getInstance(Request::getInt('offering', 0));
        foreach ($offering->units() as $unit) {
            $unit->store();
        }

        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&offering=' . Request::getInt('offering', 0);
        App::redirect(
            Route::url($url, false),
        );
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return  void
     */
    public function cancelTask()
    {
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&offering=' . Request::getInt('offering', 0);
        App::redirect(
            Route::url($url, false),
        );
    }
}
