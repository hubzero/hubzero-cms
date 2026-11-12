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
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Log;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Route;

/**
 * Courses controller class for managing membership and course info
 */
class Assetgroups extends AdminController
{
    /**
     * Determines task being called and attempts to execute it
     *
     * @return  void
     */
    public function execute()
    {
        $task   = Request::getCmd('task', '');
        $plugin = Request::getString('plugin', '');
        if ($plugin && $task && $task != 'manage') { //!isset($this->_taskMap[$task]))
            Request::setVar('action', $task);
            Request::setVar('task', 'manage');
        }

        $this->registerTask('add', 'edit');
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
            'unit' => Request::getState(
                $this->option . '.' . $this->controller . '.unit',
                'unit',
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

        $this->view->set('unit', \Components\Courses\Models\Unit::getInstance($this->view->get('filters')['unit']));
        if (!$this->view->get('unit')->exists()) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=courses', false),
            );
        }
        $this->view->set(
            'offering',
            \Components\Courses\Models\Offering::getInstance($this->view->get('unit')->get('offering_id')),
        );
        $this->view->set(
            'course',
            \Components\Courses\Models\Course::getInstance($this->view->get('offering')->get('course_id')),
        );

        $rows = $this->view->get('unit')->assetgroups(null, $this->view->filters);

        // establish the hierarchy of the menu
        $children = [
            0 => [],
        ];

        $levellimit = ($this->view->get('filters')['limit'] == 0) ? 500 : $this->view->get('filters')['limit'];

        // first pass - collect children
        foreach ($rows as $v) {
            $children[0][] = $v;
            $children[$v->get('id')] = $v->children();
        }

        // second pass - get an indent list of the items
        $list = $this->treeRecurse(0, '', [], $children, max(0, $levellimit - 1));

        $this->view->set('total', count($list));

        $this->view->set(
            'rows',
            array_slice($list, $this->view->get('filters')['start'], $this->view->get('filters')['limit']),
        );

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * Recursive function to build tree
     *
     * @param      integer $id       Parent ID
     * @param      string  $indent   Indent text
     * @param      array   $list     List of records
     * @param      array   $children Container for parent/children mapping
     * @param      integer $maxlevel Maximum levels to descend
     * @param      integer $level    Indention level
     * @param      integer $type     Indention type
     * @return     array
     */
    public function treeRecurse($id, $indent, $list, $children, $maxlevel = 9999, $level = 0, $type = 1)
    {
        if (isset($children[$id]) && @$children[$id] && $level <= $maxlevel) {
            foreach ($children[$id] as $v) {
                $id = $v->get('id');

                if ($type) {
                    $pre    = '<span class="treenode">&#8970;</span>&nbsp;';
                    $spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                } else {
                    $pre    = '- ';
                    $spacer = '&nbsp;&nbsp;';
                }

                if ($v->get('parent') == 0) {
                    $txt = '';
                } else {
                    $txt = $pre;
                }
                $pt = $v->get('parent');

                $list[$id] = $v;
                $list[$id]->treename = "$indent$txt";
                $list[$id]->children = (isset($children[$id]) && @$children[$id]) ? count(@$children[$id]) : 0;
                $list = $this->treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type);
            }
        }
        return $list;
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
                $id = (!empty($id)) ? $id[0] : '';
            }

            $model = new \Components\Courses\Models\Assetgroup($id);
        }

        $this->view->set('row', $model);

        if (!$this->view->get('row')->get('unit_id')) {
            $this->view->get('row')->set('unit_id', Request::getInt('unit', 0));
        }

        $this->view->set('unit', \Components\Courses\Models\Unit::getInstance($this->view->get('row')->get('unit_id')));

        $this->view->set(
            'offering',
            \Components\Courses\Models\Offering::getInstance($this->view->get('unit')->get('offering_id')),
        );

        $rows = $this->view->get('unit')->assetgroups();

        // establish the hierarchy of the menu
        $children = [
            0 => [],
        ];

        $levellimit = 500;

        // first pass - collect children
        foreach ($rows as $v) {
            $children[0][] = $v;
            $children[$v->get('id')] = $v->children();
        }

        // second pass - get an indent list of the items
        $this->view->set('assetgroups', $this->treeRecurse(0, '', [], $children, max(0, $levellimit - 1)));

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
     * Saves changes to a course or saves a new entry if creating
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $fields = Request::getArray('fields', [], 'post');
        $task = Request::getArray('task', [], 'post');

        // Instantiate a Course object
        $model = new \Components\Courses\Models\Assetgroup($fields['id']);

        if (!$model->bind($fields)) {
            $this->setError($model->getError());
            $this->editTask($model);
            return;
        }

        $p = new \Hubzero\Config\Registry(Request::getArray('params', [], 'post'));

        $model->set('params', $p->toString());

        if (!$model->store(true)) {
            $this->setError($model->getError());
            $this->editTask($model);
            return;
        }

        // Output messsage and redirect
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&unit=' . $model->get('unit_id');
        App::redirect(
            Route::url($url, false),
            Lang::txt('COM_COURSES_ASSETGROUP_SAVED'),
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
        $id = Request::getArray('id', []);

        // Get the single ID we're working with
        if (is_array($id)) {
            $id = (!empty($id)) ? $id[0] : 0;
        }

        $baseUrl = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

        if (!$id) {
            $url = $baseUrl . '&unit=' . Request::getInt('unit', 0);
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_COURSES_ERROR_NO_ID'),
                'error',
            );
        }

        $assetgroup = new \Components\Courses\Models\Assetgroup($id);
        $url = $baseUrl . '&unit=' . $assetgroup->get('unit_id');
        if (!$assetgroup->copy()) {
            // Redirect back to the courses page
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $assetgroup->getError(),
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
    public function removeTask()
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
                $model = new \Components\Courses\Models\Assetgroup($id);

                // Ensure we found the course info
                if (!$model->exists()) {
                    continue;
                }

                // Delete course
                if (!$model->delete()) {
                    throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
                }

                $num++;
            }
        }

        // Redirect back to the courses page
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&unit=' . Request::getInt('unit', 0);
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
        $ids = Request::getArray('id', [0]);

        // Check for an ID
        if (count($ids) < 1) {
            $url = 'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&unit=' . Request::getInt('unit', 0);
            $msg = ($state == 1 ? Lang::txt('COM_COURSES_SELECT_PUBLISH') : Lang::txt('COM_COURSES_SELECT_UNPUBLISH'));
            App::redirect(
                Route::url($url, false),
                $msg,
                'error',
            );
        }

        // Update record(s)
        foreach ($ids as $id) {
            // Updating a category
            $row = new \Components\Courses\Models\Assetgroup($id);
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
        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&unit=' . Request::getInt('unit', 0);
        App::redirect(
            Route::url($url, false),
            $message,
        );
    }

    /**
     * Reorder a plugin
     *
     * @return     void
     */
    public function orderTask()
    {
        // Check for request forgeries
        Request::checkToken();

        $id = Request::getArray('id', [0], 'post');
        \Hubzero\Utility\Arr::toInteger($id, [0]);

        $uid = $id[0];
        $inc = ($this->task == 'orderup' ? -1 : 1);

        $row = new Tables\AssetGroup($this->database);
        $row->load($uid);
        $where = 'unit_id=' . $this->database->Quote($row->unit_id)
            . ' AND parent=' . $this->database->Quote($row->parent);
        $row->move($inc, $where);
        $row->reorder($where);

        //$unit = \Components\Courses\Models\Unit::getInstance(Request::getInt('unit', 0));
        //$ags = $unit->assetgroups(null, array('parent' => $row->parent));

        if (($ags = $row->find(['w' => ['parent' => $row->parent, 'unit_id' => $row->unit_id]]))) {
            foreach ($ags as $ag) {
                $a = new \Components\Courses\Models\Assetgroup($ag);
                $a->store();
            }
        }

        $url = 'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&unit=' . Request::getInt('unit', 0);
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
            . '&unit=' . Request::getInt('unit', 0);
        App::redirect(
            Route::url($url, false),
        );
    }



    /**
     * Duplicate Asset data / files
     *
     * @return  void
     */
    public function assetdupTask()
    {

        Request::setVar('hidemainmenu', 1);

        // Incoming Id
        $id = Request::getArray('id', []);

        // Get the single ID we're working with
        if (is_array($id)) {
            $id = (!empty($id)) ? $id[0] : 0;
        }

        if (!$id) {
            $url = 'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&unit=' . Request::getInt('unit', 0);
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_COURSES_ERROR_NO_ID'),
                'error',
            );
        }

        // ------------- ASSET GROUPS BASED ON ID -------------
        $assetGroup = new \Components\Courses\Models\Assetgroup($id);
        $this->view->set('row', $assetGroup);

        if (!$this->view->get('row')->get('unit_id')) {
            $this->view->get('row')->set('unit_id', Request::getInt('unit', 0));
        }

        $this->view->set('unit', \Components\Courses\Models\Unit::getInstance($this->view->get('row')->get('unit_id')));
        $this->view->set(
            'offering',
            \Components\Courses\Models\Offering::getInstance($this->view->get('unit')->get('offering_id')),
        );

        $scopeId = $this->view->get('row')->get('id');
        // $parentId = $this->view->row->parent;
        $courseId = $this->view->get('offering')->get('course_id');
        $this->view->set('course', \Components\Courses\Models\Course::getInstance($courseId));

        // ------------- ASSETS -------------
        $filters = [
            'tmpl' => 'component',
            'asset_scope' => 'asset_group',
            'asset_scope_id' => $scopeId,
            'course_id' => $courseId,
            'limit' => Config::get('list_limit'),
            'start' => 0,
        ];

        $tbl = new Tables\Asset($this->database);
        $assetRows = $tbl->find([
            'w' => $filters,
        ]);
        $this->view->set('filters', $filters);

        $this->view->set('assetrows', $assetRows);
        $this->view->set('assetsCount', count($assetRows));

        // NEED TO GET HIERARCHY OF ALL ASSET GROUPS ---------------
        // RECURSION FROM THE TOP OF THE CHAIN - all asset groups

        $rows = $this->view->get('unit')->assetgroups();

        // Establish the hierarchy of the menu
        $children = [
            0 => [],
        ];

        $levellimit = 500;

        // 1st pass - collect children
        foreach ($rows as $v) {
            $children[0][] = $v;
            $children[$v->get('id')] = $v->children();
        }

        // 2nd pass - get an indent list of the items
        $this->view->set('assetgroups', $this->treeRecurse(0, '', [], $children, max(0, $levellimit - 1)));

        // ------------- OUTPUT TO HTML -------------
        $this->view
            ->setLayout('duplicateassets')
            ->display();
    }

    /**
     * Upon saving the duplicate asset form
     * Task is ran through the toolbar
     *
     * @return  void
     */
    public function duplicateassetsTask()
    {
        Log::debug(var_export("---- duplicateassetsTask -----", true));
        Notify::success("duplicate asset now");
    }

    /**
     * Upon saving the duplicate asset form with button underneath the form
     *
     * @return  void
     */
    public function dupassetsTask()
    {
        // (START) Ids of asset groups that will be duplicated
        $courseIdStart = Request::getString('courseIdToDuplicate');
        $offeringIdStart = Request::getString('offeringIdToDuplicate');
        $unitIdStart = Request::getString('unitIdToDuplicate');
        $assetGroupIdStart = Request::getString('assetGroupIdToDuplicate');
        $assetGroupParentIdStart = Request::getString('assetGroupParentIdToDuplicate');

        // (END) Where the asset group will end up
        $courseId = Request::getString('filterCourses');
        $offeringId = Request::getString('filterOfferings');
        $unitId = Request::getString('filterUnits');
        $assetGroupParent = Request::getString('filterAssetGroups');

        $assetgroup = new \Components\Courses\Models\Assetgroup($assetGroupIdStart);
        if (!$assetgroup->copyToSelectedAssetGroup($courseId, $offeringId, $unitId, $assetGroupParent)) {
            // Redirect back to the courses page
            $url = 'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&unit=' . $assetgroup->get('unit_id');
            App::redirect(
                Route::url($url, false),
                Lang::txt('COM_COURSES_ERROR_COPY_FAILED') . ': ' . $assetgroup->getError(),
                'error',
            );
        }

        $message = "Duplicated Asset from Asset Group " . $assetGroupIdStart
            . " to Asset Group Parent" . $assetGroupParent;

        // Redirect to the asset groups
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
            $message,
        );
    }
}
