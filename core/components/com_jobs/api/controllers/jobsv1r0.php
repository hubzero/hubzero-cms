<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Api\Controllers;

use Hubzero\Component\ApiController;
use stdClass;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Exception;

/**
 * API controller for the time component
 *
 * @FIXME: break into multiple controllers based on entity type
 */
class Jobsv1r0 extends ApiController
{
    /**
     * Display a list of jobs
     *
     * @apiMethod GET
     * @apiUri    /jobs/list
     * @apiParameter {
     *      "name":          "limit",
     *      "description":   "Number of result to return.",
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       25
     * }
     * @apiParameter {
     *      "name":          "start",
     *      "description":   "Number of where to start returning results.",
     *      "type":          "integer",
     *      "required":      false,
     *      "default":       0
     * }
     * @apiParameter {
     *      "name":          "search",
     *      "description":   "A word or phrase to search for.",
     *      "type":          "string",
     *      "required":      false,
     *      "default":       ""
     * }
     * @apiParameter {
     *      "name":          "sort",
     *      "description":   "Field to sort results by.",
     *      "type":          "string",
     *      "required":      false,
     *      "default":       "name",
     *      "allowedValues": "name, id"
     * }
     * @apiParameter {
     *      "name":          "sort_Dir",
     *      "description":   "Direction to sort results by.",
     *      "type":          "string",
     *      "required":      false,
     *      "default":       "desc",
     *      "allowedValues": "asc, desc"
     * }
     * @return  void
     */
    public function listTask()
    {
        // get POST data
        $filters = [];
        $filters['limit'] = Request::getInt('limit', 25);
        $filters['start'] = Request::getInt('start', 0);
        $filters['search'] = Request::getString('search', '');
        $filters['sort'] = Request::getWord('sort', 'name');
        $filters['sort_dir'] = Request::getCmd('sort_Dir', 'desc');

        $database = App::get('db');

        $obj = new \Components\Jobs\Tables\Job($database);

        $jobs = $obj->getOpenings($filters);

        // Create object with records property
        $response = new stdClass();
        $response->jobs = $jobs;

        // Return object
        $this->send($response);
    }

    /**
     * Display a job
     *
     * @apiMethod GET
     * @apiUri    /jobs/{jobcode}
     * @apiParameter {
     *      "name":          "jobcode",
     *      "description":   "The job code associated with the opening",
     *      "type":          "integer",
     *      "required":      true,
     *      "default":       ""
     * }
     *
     * @return  void
     */
    public function jobTask()
    {
        $jobCode = Request::getInt('jobcode');

        $database = App::get('db');

        $obj = new \Components\Jobs\Tables\Job($database);
        $filters = [];
        // As on the site: getOpening() shows an opening that is not open (a
        // draft, pending) only to its employer and to a jobs administrator
        $admin = User::authorise('core.manage', 'com_jobs') ? 1 : 0;
        $job = $jobCode ? $obj->getOpening(0, (int) User::get('id'), $admin, $jobCode) : null;
        if (!$job) {
            throw new Exception(Lang::txt('COM_JOBS_ERROR_JOB_NOT_FOUND'), 404);
        }

        // Create object with records property
        $response = new stdClass();
        $response->job = $job;

        // Return object
        $this->send($response);
    }
}
