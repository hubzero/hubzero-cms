<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * /administrator/components/com_support/controllers/tickets.php
 * 
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

JLoader::import('Hubzero.Api.Controller');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_blog' . DS . 'models' . DS . 'blog.php');

/**
 * API controller class for support tickets
 */
class BlogControllerApi extends Hubzero_Api_Controller
{
	/**
	 * Execute a request
	 *
	 * @return    void
	 */
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		$this->config   = JComponentHelper::getParams('com_blog');
		$this->database = JFactory::getDBO();

		switch ($this->segments[0]) 
		{
			case 'search':  $this->archiveTask();  break;
			case 'archive': $this->archiveTask();  break;

			default:
				$this->errorMessage(
					500, 
					JText::_('Invalid task.'), 
					JRequest::getWord('format', 'json')
				);
			break;
		}
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
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
		$this->setMessageType(JRequest::getWord('format', $format));
		$this->setMessage($object);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function archiveTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$model = new BlogModel('site');

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'sort'       => JRequest::getWord('sort', 'created'),
			'sort_Dir'   => strtoupper(JRequest::getWord('sortDir', 'DESC'))
		);

		$response = new stdClass;
		$response->posts = array();
		$response->total = $model->entries('count', $filters);

		if ($response->total)
		{
			$juri =& JURI::getInstance();

			foreach ($model->entries('list', $filters) as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id        = $entry->get('id');
				$obj->title     = $entry->get('title');
				$obj->alias     = $entry->get('alias');
				$obj->state     = $entry->get('state');
				$obj->published = $entry->get('publish_up');
				$obj->scope     = $entry->get('scope');
				$obj->author    = $entry->creator('name');
				$obj->url       = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(JRoute::_($entry->link()), DS));
				$obj->comments  = $entry->comments('count');

				$response->posts[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}
}
