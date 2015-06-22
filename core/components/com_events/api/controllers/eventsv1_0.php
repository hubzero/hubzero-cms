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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Events\Api\Controllers;

use Hubzero\Component\ApiController;
use stdClass;
use Request;

/**
 * API controller class for events
 */
class Eventsv1_0 extends ApiController
{
	/**
	 * List active events
	 *
	 * @apiMethod GET
	 * @apiUri    /calendar/list
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
	 * @return  void
	 */
	public function listTask()
	{
		// get the request vars
		$limit = Request::getInt('limit', 5);
		$start = Request::getInt('limitstart', 0);

		// load up the events
		$database = \JFactory::getDBO();
		$query = "SELECT * FROM `#__events` as e
					/* WHERE publish_up <= UTC_TIMESTAMP() */
					WHERE publish_down >= UTC_TIMESTAMP()
					AND state=1
					AND approved=1
					AND scope='event'
					LIMIT {$start},{$limit}";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		// return results
		$object = new stdClass();
		$object->events = $rows;

		$this->send($object);
	}

	/**
	 * Get user profile info
	 *
	 * @apiMethod GET
	 * @apiUri    /events/{id}
	 * @apiParameter {
	 * 		"name":        "id",
	 * 		"description": "Event identifier",
	 * 		"type":        "integer",
	 * 		"required":    true,
	 * 		"default":     null
	 * }
	 * @return  void
	 */
	public function readTask()
	{
		$eventID = Request::getInt('id', 0);
		$nicedate = Request::getInt('niceDate', 0);

		// load up the events
		$database = \JFactory::getDBO();
		$query = "SELECT * FROM `#__events` as e
					/* WHERE publish_up <= UTC_TIMESTAMP() */
					WHERE state=1
					AND approved=1
					AND scope='event'
					AND id={$eventID}";

		$database->setQuery($query);
		$row = $database->loadAssoc();

		//format the date
		if ($nicedate)
		{
			$start = strtotime($row['publish_up']);
			$row['publish_up'] = date('M j, Y g:ia T', $start);

			$end = strtotime($row['publish_down']);
			$row['publish_down'] = date('M j, Y g:ia T', $end);
		}

		// return results
		$object = new stdClass();
		$object->event = $row;

		$this->send($object);
	}

}
