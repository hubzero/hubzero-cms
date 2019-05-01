<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Api\Controllers;

use Hubzero\Component\ApiController;

use stdClass;
use Date;
use Request;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'job.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'job.php';

/**
 * API controller for the time component
 *
 * @FIXME: break into multiple controllers based on entity type
 */
class Jobsv1_0 extends ApiController
{
	/**
	 * Display a list of jobs
	 *
	 * @apiMethod GET
	 * @apiUri    /jobs/list
	 * @apiParameter {
	 * 		"name":          "limit",
	 * 		"description":   "Number of result to return.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       25
	 * }
	 * @apiParameter {
	 * 		"name":          "start",
	 * 		"description":   "Number of where to start returning results.",
	 * 		"type":          "integer",
	 * 		"required":      false,
	 * 		"default":       0
	 * }
	 * @apiParameter {
	 * 		"name":          "search",
	 * 		"description":   "A word or phrase to search for.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       ""
	 * }
	 * @apiParameter {
	 * 		"name":          "sort",
	 * 		"description":   "Field to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 *      "default":       "name",
	 * 		"allowedValues": "name, id"
	 * }
	 * @apiParameter {
	 * 		"name":          "sort_Dir",
	 * 		"description":   "Direction to sort results by.",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "desc",
	 * 		"allowedValues": "asc, desc"
	 * }
	 * @return  void
	 */
	public function listTask()
	{
		// get POST data
		$filters = array();
		$filters['limit'] = Request::getInt('limit', 25);
		$filters['start'] = Request::getInt('start', 0);
		$filters['search'] = Request::getString('search', '');
		$filters['sort'] = Request::getWord('sort', 'name');
		$filters['sort_dir'] = Request::getCmd('sort_Dir', 'desc');

		$database = App::get('db');

		$obj = new \Components\Jobs\Tables\Job($database);

		$jobs = $obj->get_openings($filters);

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
	 * 		"name":          "jobcode",
	 * 		"description":   "The job code associated with the opening",
	 * 		"type":          "integer",
	 * 		"required":      true,
	 * 		"default":       ""
	 * }
	 *
	 * @return  void
	 */
	public function jobTask()
	{
		$jobCode = Request::getInt('jobcode');

		$database = App::get('db');

		$obj = new \Components\Jobs\Tables\Job($database);
		$filters = array();
		$job = $obj->get_opening($jid = null, $uid = null, $admin = null, $jobcode = $jobCode);

		// Create object with records property
		$response = new stdClass();
		$response->job = $job;

		// Return object
		$this->send($response);
	}


	/**
	 * Checks to ensure appropriate authorization
	 *
	 * @return  bool
	 * @throws  Exception
	 */
	private function authorizeOrFail()
	{
		$permissions = new Permissions('com_jobs');

		// Make sure action can be performed
		if (!$permissions->can('api'))
		{
			App::abort(401, 'Unauthorized');
		}

		return true;
	}
}
