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

require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'hub.php';

/*
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'task.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'record.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'contact.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'permissions.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'proxy.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'models' . DS . 'liaison.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_time' . DS . 'helpers' . DS . 'filters.php'; */

/**
 * API controller for the time component
 *
 * @FIXME: break into multiple controllers based on entity type
 */
class Jobsv1_0 extends ApiController
{
	/**
	 * Lists all applicable time records
	 *
	 * @apiMethod GET
	 * @apiUri    /time/indexRecords
	 * @apiParameter {
	 * 		"name":        "tid",
	 * 		"description": "Task id by which to limit records",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "startdate",
	 * 		"description": "Beginning date threshold",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "enddate",
	 * 		"description": "Ending date threshold",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     null
	 * }
	 * @apiParameter {
	 * 		"name":        "limit",
	 * 		"description": "Maximim number of records to return",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     20
	 * }
	 * @apiParameter {
	 * 		"name":        "start",
	 * 		"description": "Record index to start at (for pagination)",
	 * 		"type":        "integer",
	 * 		"required":    false,
	 * 		"default":     0
	 * }
	 * @apiParameter {
	 * 		"name":        "orderby",
	 * 		"description": "Field by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "id"
	 * }
	 * @apiParameter {
	 * 		"name":        "orderdir",
	 * 		"description": "Direction by which to order results",
	 * 		"type":        "string",
	 * 		"required":    false,
	 * 		"default":     "asc"
	 * }
	 * @return  void
	 */
	public function indexJobsTask()
	{
		// Require authentication and authorization
		$this->requiresAuthentication();
		$this->authorizeOrFail();


		// Create object with records property
		$response          = new stdClass();
		$response->records = $record->rows()->toObject();

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
		$permissions = new Permissions('com_time');

		// Make sure action can be performed
		if (!$permissions->can('api'))
		{
			App::abort(401, 'Unauthorized');
		}

		return true;
	}
}