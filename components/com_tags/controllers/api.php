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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php');

/**
 * API controller class for support tickets
 */
class TagsControllerApi extends \Hubzero\Component\ApiController
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

		$this->config   = JComponentHelper::getParams('com_tags');
		$this->database = JFactory::getDBO();

		switch ($this->segments[0])
		{
			case 'view':    $this->tagTask();  break;
			case 'tag':     $this->tagTask();  break;
			case 'list':    $this->tagsTask(); break;
			case 'tags':    $this->tagsTask(); break;
			case 'add':     $this->addTask();   break;
			case 'remove':  $this->removeTask();  break;
			case 'autocomplete':  $this->tagsTask();  break;
			default:
				$this->serviceTask();
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
	 * Documents available API tasks and their options
	 *
	 * @return  void
	 */
	public function serviceTask()
	{
		$response = new stdClass();
		$response->component = 'tags';
		$response->tasks = array(
			'tags' => array(
				'description' => JText::_('Get a list of tags.'),
				'parameters'  => array(
					'search' => array(
						'description' => JText::_('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
					'scope' => array(
						'description' => JText::_('Object type to retrieve tags for (ex: resource, answer, etc).'),
						'type'        => 'string',
						'default'     => 'null',
						'requires'    => 'scope_id'
					),
					'scope_id' => array(
						'description' => JText::_('ID of object to retrieve tags for.'),
						'type'        => 'integer',
						'default'     => 'null',
						'requires'    => 'scope'
					),
					'sort' => array(
						'description' => JText::_('Sorting to be applied to the records.'),
						'type'        => 'string',
						'default'     => 'date',
						'accepts'     => array('raw_tag', 'id')
					),
					'sortDir' => array(
						'description' => JText::_('Direction to sort records by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'limit' => array(
						'description' => JText::_('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '25'
					),
					'limitstart' => array(
						'description' => JText::_('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
				),
			),
			'tag' => array(
				'description' => JText::_('Get information for a tag.'),
				'parameters'  => array(
					'tag' => array(
						'description' => JText::_('The tag to retrieve information for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
				),
			),
		);

		$this->setMessage($response);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function tagsTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$cloud = new TagsModelCloud();

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'tbl'        => JRequest::getword('scope', ''),
			'objectid'   => JRequest::getInt('scope_id', 0),
			'taggerid'   => JRequest::getVar('tagger', ''),
			'sort'       => JRequest::getWord('sort', 'raw_tag'),
			'sort_Dir'   => strtoupper(JRequest::getWord('sortDir', 'ASC'))
		);

		$response = new stdClass;
		$response->tags  = array();
		$response->total = $cloud->tags('count', $filters);

		if ($response->total)
		{
			$juri = JURI::getInstance();

			foreach ($cloud->tags('list', $filters) as $i => $tag)
			{
				$obj = new stdClass;
				$obj->id    = $tag->get('id');
				$obj->tag   = $tag->get('raw_tag');
				$obj->title = $tag->get('tag');
				$obj->url   = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(JRoute::_($tag->link()), DS));

				$obj->substitutes_count = $tag->get('substitutes');
				$obj->objects_count = $tag->get('total');

				$response->tags[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Display info for a tag
	 *
	 * @return    void
	 */
	private function tagTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$response = new stdClass;

		$tag = new TagsModelTag(JRequest::getWord('tag', ''));
		if (!$tag->exists())
		{
			$response->success = false;

			$this->errorMessage(
				404,
				JText::_('Specified tag does not exist.')
			);
			return;
		}

		$juri = JURI::getInstance();

		$response->id    = $tag->get('id');
		$response->tag   = $tag->get('raw_tag');
		$response->title = $tag->get('tag');
		$response->description = $tag->get('description');
		$response->admin = $tag->get('admin');
		$response->url   = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(JRoute::_($tag->link()), DS));

		$response->objects_count = $tag->objects('count');

		$response->substitutes = array();
		foreach ($tag->substitutes('list') as $sub)
		{
			$obj = new stdClass;
			$obj->id    = $sub->get('id');
			$obj->tag   = $sub->get('raw_tag');
			$obj->title = $sub->get('tag');

			$response->substitutes[] = $obj;
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Add a tag to an item
	 *
	 * @return    void
	 */
	private function addTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$response = new stdClass;

		$tag = new TagsModelTag(JRequest::getWord('tag', ''));
		if (!$tag->exists())
		{
			$response->success = false;

			$this->errorMessage(
				404,
				JText::_('Specified tag does not exist.')
			);
			return;
		}

		if (
			!$tag->addTo(
				JRequest::getWord('scope', ''),
				JRequest::getInt('scope_id', 0),
				JRequest::getInt('tagger', 0)
			)
		)
		{
			$response->success = false;

			$this->errorMessage(
				500,
				JText::_('Failed to add tag.')
			);
			return;
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Remove tag from an item
	 *
	 * @return    void
	 */
	private function removeTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$response = new stdClass;

		$tag = new TagsModelTag(JRequest::getWord('tag', ''));
		if (!$tag->exists())
		{
			$response->success = false;

			$this->errorMessage(
				404,
				JText::_('Specified tag does not exist.')
			);
			return;
		}

		if (
			!$tag->removeFrom(
				JRequest::getWord('scope', ''),
				JRequest::getInt('scope_id', 0),
				JRequest::getInt('tagger', 0)
			)
		)
		{
			$response->success = false;

			$this->errorMessage(
				500,
				JText::_('Failed to remove tag.')
			);
			return;
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Create a new entry
	 *
	 * @return  void
	 */
	private function newTask()
	{
		$this->setMessageType(JRequest::getVar('format', 'json'));

		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//make sure we have a user
		if ($result === false)
		{
			return $this->not_found();
		}

		// Create an object for returning messages
		$msg = new stdClass;

		// Any tags?
		$tag = new TagsModelTag(JRequest::getVar('tag', '', 'post'));
		if (!$tag->exists())
		{
			if (!$tag->store(true))
			{
				$msg->success = false;

				$this->errorMessage(
					500,
					$tag->getError()
				);
				return;
			}
		}

		// Set the response
		$msg->success = true;
		$msg->tag     = $tag->get('tag');
		$msg->label   = $tag->get('raw_tag');
		$msg->id      = $tag->get('id');

		$this->setMessage($msg);
	}
}
