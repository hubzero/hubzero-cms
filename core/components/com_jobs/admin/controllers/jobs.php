<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Admin\Controllers;

use Components\Jobs\Tables\Job;
use Components\Jobs\Tables\Admin;
use Components\Jobs\Tables\Category;
use Components\Jobs\Tables\Type;
use Components\Jobs\Tables\Employer;
use Hubzero\Component\AdminController;
use Exception;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\Date;
use Hubzero\Facades\App;
use Hubzero\Facades\Event;

/**
 * Controller class for job postings
 */
class Jobs extends AdminController
{
    /**
     * Jobs List
     *
     * @return  void
     */
    public function displayTask()
    {
        $filters = [
            // Get paging variables
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
            // Get sorting variables
            'sortby' => Request::getState(
                $this->option . '.' . $this->controller . '.sortby',
                'filter_order',
                'added',
            ),
            'sortdir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'DESC',
            ),
            // Filters
            'category' => Request::getState(
                $this->option . '.' . $this->controller . 'category',
                'category',
                'all',
            ),
            'filterby' => '',
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . 'search',
                'search',
                '',
            )),
        ];

        // Get data
        $obj = new Job($this->database);

        $this->view->set('rows', $obj->getOpenings($filters, User::get('id'), 1));
        $this->view->set('total', $obj->getOpenings($filters, User::get('id'), 1, '', 1));
        $this->view->set('filters', $filters);

        $this->view->set('config', $this->config);

        // Output the HTML
        $this->view->display();
    }

    /**
     * Create a job posting
     * Displays the edit form
     *
     * @return  void
     */
    public function addTask()
    {
        $this->editTask();
    }

    /**
     * Edit Job Posting
     *
     * @param   integer  $isnew  Is this a new entry?
     * @return  void
     */
    public function editTask($isnew = 0)
    {
        Request::setVar('hidemainmenu', 1);

        $live_site = rtrim(Request::base(), '/');

        // Push some styles to the template
        $this->css();

        // Incoming job ID
        $id = Request::getArray('id', [0]);
        $id = is_array($id) ? $id[0] : $id;

        // Grab some filters for returning to place after editing
        $this->view->set('return', []);
        $this->view->return['sortby'] = Request::getString('sortby', 'added');

        $this->view->set('row', new Job($this->database));

        $this->view->set('jobadmin', new Admin($this->database));
        $this->view->set('employer', new Employer($this->database));

        // Is this a new job?
        if (!$id) {
            $this->view->get('row')->created      = Date::toSql();
            $this->view->get('row')->created_by   = User::get('id');
            $this->view->get('row')->modified_by  = 0;
            $this->view->get('row')->publish_up   = Date::toSql();
            $this->view->get('row')->employerid   = 1; // admin
        } elseif (!$this->view->get('row')->load($id)) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
                Lang::txt('COM_JOBS_ERROR_MISSING_JOB'),
                'error',
            );
        }

        $this->view->set('job', $this->view->get('row')->getOpening($id, User::get('id'), 1));

        // Get employer information
        if ($this->view->get('row')->employerid != 1) {
            if (!$this->view->get('employer')->loadEmployer($this->view->get('row')->employerid)) {
                App::redirect(
                    Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
                    Lang::txt('COM_JOBS_ERROR_MISSING_EMPLOYER_INFO'),
                    'error',
                );
            }
        } else {
            // site admin
            $this->view->get('employer')->uid             = 1;
            $this->view->get('employer')->subscriptionid  = 1;
            $this->view->get('employer')->companyName     = Config::get('sitename');
            $this->view->get('employer')->companyLocation = '';
            $this->view->get('employer')->companyWebsite  = $live_site;
        }

        // Get subscription info

        $subId = $this->view->get('employer')->subscriptionid;
        $this->view->set('subscription', \Components\Services\Models\Subscription::oneOrNew($subId));

        // Get job types and categories
        $jt = new Type($this->database);
        $jc = new Category($this->database);

        // get job types
        $types = $jt->getTypes();
        $types[0] = Lang::txt('COM_JOBS_TYPE_ANY');
        $this->view->set('types', $types);

        // get job categories
        $cats = $jc->getCats();
        $cats[0] = Lang::txt('COM_JOBS_CATEGORY_UNSPECIFIED');
        $this->view->set('cats', $cats);

        $this->view->set('config', $this->config);
        $this->view->set('isnew', $isnew);

        // Set any errors
        foreach ($this->getErrors() as $error) {
            $this->view->setError($error);
        }

        // Output the HTML
        $this->view
            ->setLayout('edit')
            ->display();
    }

    /**
     * Save Job Posting
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $data       = array_map('trim', $_POST);
        $action     = Request::getString('action', '');
        $message    = Request::getString('message', '');
        $id         = Request::getInt('id', 0);
        $employerid = Request::getInt('employerid', 0);
        $emailbody  = '';
        $statusmsg  = '';

        $job = new Job($this->database);
        $employer = new Employer($this->database);

        if ($id) {
            if (!$job->load($id)) {
                App::redirect(
                    Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
                    Lang::txt('COM_JOBS_ERROR_MISSING_JOB'),
                    'error',
                );
            }
        } else { // saving new job
            $subscription = \Components\Services\Models\Subscription::blank();
            $code = $subscription->generateCode(8, 8, 0, 1, 0);
            $job->code = $code;

            $job->added   = Date::toSql();
            $job->addedBy = User::get('id');
        }

        $subject = $id ? Lang::txt('COM_JOBS_MESSAGE_SUBJECT', $job->code) : '';

        $job->bind($_POST);

        // some clean-up
        $job->description     = rtrim(stripslashes($job->description));
        $job->title           = rtrim(stripslashes($job->title));
        $job->companyName     = rtrim(stripslashes($job->companyName));
        $job->companyLocation = rtrim(stripslashes($job->companyLocation));

        // admin actions
        if ($id) {
            switch ($action) {
                case 'publish':
                    // make sure we aren't over quota
                    $allowed_ads = $employerid == 1 ? 1 : $this->checkQuota($job, $employerid, $this->database);

                    if ($allowed_ads <= 0) {
                        $statusmsg .= Lang::txt('COM_JOBS_ERROR_OVER_LIMIT');
                        $action = '';
                    } else {
                        $job->status   = 1;
                        $job->opendate = Date::toSql();
                        $statusmsg .= Lang::txt('COM_JOBS_MESSAGE_JOB_APPROVED');
                    }
                    break;

                case 'unpublish':
                    $job->status = 3;
                    $statusmsg .= Lang::txt('COM_JOBS_MESSAGE_JOB_UNPUBLISHED');
                    break;

                case 'message':
                    break;

                case 'delete':
                    $job->status = 2;
                    $statusmsg .= Lang::txt('COM_JOBS_MESSAGE_JOB_DELETED');
                    break;
            }

            $job->editedBy = User::get('id');
            $job->edited   = Date::toSql();
        }

        if (!$job->store()) {
            throw new Exception($job->getError(), 500);
        }

        if (!$job->id) {
            $job->checkin();
        }

        if (($message && $action == 'message' && $id) || ($action && $action != 'message')) {
            // E-mail "from" info
            $from = [
                'email' => Config::get('mailfrom'),
                'name'  => Config::get('sitename') . ' ' . Lang::txt('COM_JOBS_JOBS'),
            ];

            $base = rtrim(Request::base(), '/');
            if (substr($base, -13) == 'administrator') {
                $base = substr($base, 0, strlen($base) - 13);
            }
            $sef  = 'jobs/job/' . $job->code;
            $link = rtrim($base, '/') . '/' . trim($sef, '/');

            // start email message
            $emailbody .= $subject . ':' . "\r\n";
            $emailbody .= $statusmsg . "\r\n";
            $emailbody .= Lang::txt('COM_JOBS_MESSAGE_JOB') . ': ' . $link . "\r\n";
            if ($message) {
                $emailbody .= "\n";
                $emailbody .= '----------------------------------------------------------' . "\r\n";
                $emailbody .= "\n" . Lang::txt('COM_JOBS_MESSAGE_FROM_ADMIN:') . "\n";
                $emailbody .= $message;
            }

            $messageParams = [
                'jobs_ad_status_changed',
                $subject,
                $emailbody,
                $from,
                [$job->addedBy],
                $this->option,
            ];
            if (!Event::trigger('xmessage.onSendMessage', $messageParams)) {
                Notify::error(Lang::txt('COM_JOBS_ERROR_FAILED_TO_MESSAGE_USERS'));
            }
        }

        // Redirect
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
            Lang::txt('COM_JOBS_ITEM_SAVED') . ($statusmsg ? ' ' . $statusmsg : ''),
        );
    }

    /**
     * Check job ad quota depending on subscription
     *
     * @param    object  $job       Job entry
     * @param   integer  $uid       User ID
     * @param   object   $database  Database
     * @return  integer
     */
    private function checkQuota($job, $uid, $database)
    {
        // make sure we aren't over quota

        $maxads = 3;
        if (
            isset($this->config->parameters['maxads'])
            && intval($this->config->parameters['maxads']) > 0
        ) {
            $maxads = $this->config->parameters['maxads'];
        }
        $service = \Components\Services\Models\Service::getUserService($uid);
        $activejobs = $job->countMyActiveOpenings($uid, 1);

        return ($service == 'employer_basic' ? 1 - $activejobs : $maxads - $activejobs);
    }

    /**
     * Remove Job Posting
     *
     * @return  void
     */
    public function removeTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming (expecting an array)
        $ids = Request::getArray('id', []);

        // Ensure we have an ID to work with
        if (empty($ids)) {
            App::redirect(
                Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
                Lang::txt('COM_JOBS_ERROR_NO_ITEM_SELECTED'),
                'error',
            );
        }

        $row = new Job($this->database);

        foreach ($ids as $id) {
            // Delete the type
            $row->delete($id);
        }

        // Redirect
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller, false),
            Lang::txt('COM_JOBS_ITEMS_REMOVED', count($ids)),
        );
    }
}
