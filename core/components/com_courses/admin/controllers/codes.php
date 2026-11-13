<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Admin\Controllers;

use Hubzero\Component\AdminController;
use Exception;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Notify;

/**
 * Courses controller class for membership codes
 */
class Codes extends AdminController
{
    /**
     * Displays a list of codes
     *
     * @return  void
     */
    public function displayTask()
    {
        // Incoming
        $this->view->set('filters', [
            'section' => Request::getState(
                $this->option . '.' . $this->controller . '.section',
                'section',
                0,
            ),
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            )),
            'redeemed' => Request::getState(
                $this->option . '.' . $this->controller . '.redeemed',
                'redeemed',
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
            'section',
            \Components\Courses\Models\Section::getInstance($this->view->get('filters')['section']),
        );
        if (!$this->view->get('section')->exists()) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=courses', false),
            );
        }
        $this->view->set('offering', \Components\Courses\Models\Offering::getInstance(
            $this->view->get('section')->get('offering_id'),
        ));
        $this->view->set(
            'course',
            \Components\Courses\Models\Course::getInstance($this->view->get('offering')->get('course_id')),
        );

        // In case limit has been changed, adjust limitstart accordingly
        $viewFilters = $this->view->get('filters');
        $viewFilters['start'] = ($this->view->get('filters')['limit'] != 0 ? (floor(
            $this->view->get('filters')['start'] / $this->view->get('filters')['limit'],
        ) * $this->view->get('filters')['limit']) : 0);
        $this->view->set('filters', $viewFilters);

        $viewFilters = $this->view->get('filters');
        $viewFilters['count'] = true;
        $this->view->set('filters', $viewFilters);

        $this->view->set('total', $this->view->get('section')->codes($this->view->filters));

        $this->view->filters['count'] = false;

        $this->view->set('rows', $this->view->get('section')->codes($this->view->filters));

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * Create a new course
     *
     * @return  void
     */
    public function addTask()
    {
        $this->editTask();
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

            $model = new \Components\Courses\Models\Section\Code($id);
        }

        $this->view->set('row', $model);

        if (!$this->view->get('row')->get('offering_id')) {
            $this->view->get('row')->set('offering_id', Request::getInt('offering', 0));
        }

        $this->view->set(
            'section',
            \Components\Courses\Models\Section::getInstance($this->view->get('row')->get('section_id')),
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
     * Saves changes to a course or saves a new entry if creating
     *
     * @return void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $fields = Request::getArray('fields', [], 'post');

        // Instantiate a Course object
        $model = new \Components\Courses\Models\Section\Code($fields['id']);

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

        // Output messsage and redirect
        App::redirect(
            Route::url(
                'index.php?option='
                    . $this->option
                    . '&controller='
                    . $this->controller
                    . '&section='
                    . $model->get('section_id'),
                false,
            ),
            Lang::txt('COM_COURSES_CODE_SAVED'),
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

        // Get the single ID we're working with
        if (!is_array($ids)) {
            $ids = [];
        }

        $num = 0;

        // Do we have any IDs?
        if (!empty($ids)) {
            foreach ($ids as $id) {
                // Load the code
                $model = new \Components\Courses\Models\Section\Code($id);

                // Ensure we found a record
                if (!$model->exists()) {
                    continue;
                }

                // Delete record
                if (!$model->delete()) {
                    throw new Exception(Lang::txt('COM_COURSES_ERROR_UNABLE_TO_REMOVE_ENTRY'), 500);
                }

                $num++;
            }
        }

        // Redirect back to the courses page
        App::redirect(
            Route::url(
                'index.php?option='
                    . $this->option
                    . '&controller='
                    . $this->controller
                    . '&section='
                    . Request::getInt('section', 0),
                false,
            ),
            Lang::txt('COM_COURSES_ITEMS_REMOVED', $num),
        );
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return  void
     */
    public function generateTask()
    {
        // Check for request forgeries
        Request::checkToken();

        $section = Request::getInt('section', 0);
        $num = Request::getInt('num', 1);

        $expires = Request::getArray('expires', []);
        $expires = implode('-', $expires) . ' 12:00:00';

        if ($num > 0) {
            $codes = [];
            for ($i = 0; $i < $num; $i++) {
                $model = new \Components\Courses\Models\Section\Code(0);
                $model->set('code', $this->generateCode());
                $model->set('section_id', $section);
                $model->set('expires', $expires);
                if (!$model->store(true)) {
                    $this->setError($model->getError());
                }
            }
        }

        if ($this->getError()) {
            throw new Exception(implode('<br />', $this->getErrors()), 500);
        }

        if (!Request::getInt('no_html', 0)) {
            App::redirect(
                Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&section=' . $section,
                    false,
                ),
            );
        }
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return  void
     */
    public function optionsTask()
    {
        $section = Request::getInt('section', 0);

        $this->view->set('section', \Components\Courses\Models\Section::getInstance($section));

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view->display();
    }

    /**
     * Generate a coupon code
     *
     * @return  string
     */
    private function generateCode()
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $res = '';
        for ($i = 0; $i < 10; $i++) {
            $res .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $res;
    }

    /**
     * Cancel a task (redirects to default task)
     *
     * @return  void
     */
    public function cancelTask()
    {
        App::redirect(
            Route::url(
                'index.php?option='
                    . $this->option
                    . '&controller='
                    . $this->controller
                    . '&section='
                    . Request::getInt('section', 0),
                false,
            ),
        );
    }

    /**
     * Quote a value for a CSV file
     *
     * @param   string|int|null  $val
     * @return  string
     */
    public static function quoteCsv($val)
    {
        if (!isset($val)) {
            return '';
        }

        if (strpos($val, "\n") !== false || strpos($val, ',') !== false) {
            return '"' . str_replace(['\\', '"'], ['\\\\', '""'], $val) . '"';
        }

        return $val;
    }

    /**
     * Quote a CSV row
     *
     * @param   array   $vals
     * @return  string
     */
    public function quoteCsvRow($vals)
    {
        return implode(',', array_map([$this, 'quoteCsv'], $vals)) . "\n";
    }

    /**
     * Export codes as a CSV file
     *
     * @return  void
     */
    public function exportTask()
    {
        $fields  = ['id', 'code', 'created', 'expires', 'redeemed', 'redeemed by'];
        $rows    = [];
        $section = Request::getInt('section', 0);

        if (!$section) {
            App::redirect(
                Route::url(
                    'index.php?option='
                        . $this->option
                        . '&controller='
                        . $this->controller
                        . '&section='
                        . Request::getInt('section', 0),
                    false,
                ),
                Lang::txt('No section specified'),
                'warning',
            );
        }

        // Incoming
        $ids = Request::getArray('id', []);
        $ids = (is_array($ids) ? $ids : [$ids]);

        // Do we have any IDs?
        if (empty($ids)) {
            App::redirect(
                Route::url(
                    'index.php?option='
                        . $this->option
                        . '&controller='
                        . $this->controller
                        . '&section='
                        . Request::getInt('section', 0),
                    false,
                ),
                Lang::txt('No codes selected'),
                'warning',
            );
        }

        // Output header
        @ob_end_clean();

        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");

        header("Content-Transfer-Encoding: binary");
        header('Content-type: text/comma-separated-values');
        header('Content-disposition: attachment; filename="section_' . $section . '_codes.csv"');

        echo $this->quoteCsvRow($fields);

        foreach ($ids as $id) {
            // Load the code
            $model = new \Components\Courses\Models\Section\Code($id);

            // Ensure we found a record
            if (!$model->exists()) {
                continue;
            }

            $row = [
                $model->get('id'),
                $model->get('code'),
                $model->get('created'),
                $model->get('expires'),
                $model->get('redeemed'),
                $model->get('redeemed_by'),
            ];

            echo $this->quoteCsvRow($row);
        }

        exit();
    }
}
