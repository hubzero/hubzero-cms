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

/**
 * API controller class for events
 */
class EventsControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		//JLoader::import('joomla.environment.request');
		//JLoader::import('joomla.application.component.helper');

		switch ($this->segments[0])
		{
			case 'index': $this->indexTask(); break;
			default:      $this->errorMessage(404, Lang::txt('Not found.'));
		}
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param   integer  $code     Error Code
	 * @param   string   $message  Error Message
	 * @param   string   $format   Error Response Format
	 * @return  void
	 */
	private function errorMessage($code, $message, $format = 'json')
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code    = $code;
		$object->error->message = $message;

		//set http status code and reason
		$this->getResponse()
		     ->setErrorMessage($object->error->code, $object->error->message);

		//add error to message body
		$this->setMessageType(Request::getWord('format', $format));
		$this->setMessage($object);
	}

	/**
	 * Documents available API tasks and their options
	 *
	 * @return  void
	 */
	public function indexTask()
	{
		// get the userid
		$userid = \JFactory::getApplication()->getAuthn('user_id');

		// if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->errorMessage(404, Lang::txt('Not found.'));
		}

		// get the request vars
		$limit  = Request::getInt('limit', 5);
		$format = Request::getVar('format', 'json');

		// load up the events
		$database = \JFactory::getDBO();
		$query = "SELECT * FROM #__events as e
					/* WHERE publish_up <= UTC_TIMESTAMP() */
					WHERE publish_down >= UTC_TIMESTAMP()
					AND state=1
					AND approved=1
					AND scope='event'
					LIMIT {$limit}";

		$database->setQuery($query);
		$rows = $database->loadObjectList();

		// return results
		$object = new stdClass();
		$object->events = $rows;

		$this->setMessageType($format);
		$this->setMessage($object);
	}
}
