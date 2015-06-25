<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Jobs\Api\Controllers;

use Hubzero\Component\ApiController;
//use Components\Time\Models\Hub;

use stdClass;
use Date;
use Request;

require_once PATH_CORE . DS . 'components' . DS . 'com_jobs' . DS . 'models' . DS . 'job.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_jobs' . DS . 'tables' . DS . 'job.php';


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

		$database = \JFactory::getDbo();

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

		$database = \JFactory::getDbo();

		$obj = new \Components\Jobs\Tables\Job($database);
		$filters = array();
		$job = $obj->get_opening($jid = NULL, $uid = NULL, $admin = NULL, $jobcode = $jobCode);

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