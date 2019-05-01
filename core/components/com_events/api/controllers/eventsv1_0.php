<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$database = \App::get('db');
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
		$database = \App::get('db');
		$query = "SELECT * FROM `#__events` as e
					/* WHERE publish_up <= UTC_TIMESTAMP() */
					WHERE state=1
					AND approved=1
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
