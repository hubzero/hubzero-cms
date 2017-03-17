<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Api\Controllers;

use Hubzero\Component\ApiController;

use stdClass;
use Date;
use Request;

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
		$filters['search'] = Request::getVar('search', "");
		$filters['sort'] = Request::getVar('sort', "name");
		$filters['sort_dir'] = Request::getCmd('sort_Dir', "desc");

		$database = \App::get('db');

		$obj = new \Components\Jobs\Tables\Job($database);

		$jobs = $obj->get_openings($filters);

		// Create object with records property
		$response          = new stdClass();
		$response->jobs = $jobs;

		// Return object
		$this->send($response);
	}

	/**
	 * Display a list of jobs
	 *
	 * @apiMethod GET
	 * @apiUri    /jobs/job
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

		$database = \App::get('db');

		$obj = new \Components\Jobs\Tables\Job($database);
		$filters = array();
		$job = $obj->get_opening($jid = null, $uid = null, $admin = null, $jobcode = $jobCode);

		// Create object with records property
		$response          = new stdClass();
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
