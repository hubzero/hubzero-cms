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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

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
		//JLoader::import('joomla.environment.request');
		//JLoader::import('joomla.application.component.helper');

		$this->config   = Component::params('com_tags');
		$this->database = \JFactory::getDBO();

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
	public function serviceTask()
	{
		$response = new stdClass();
		$response->component = 'tags';
		$response->tasks = array(
			'tags' => array(
				'description' => Lang::txt('Get a list of tags.'),
				'parameters'  => array(
					'search' => array(
						'description' => Lang::txt('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
					),
					'scope' => array(
						'description' => Lang::txt('Object type to retrieve tags for (ex: resource, answer, etc).'),
						'type'        => 'string',
						'default'     => 'null',
						'requires'    => 'scope_id'
					),
					'scope_id' => array(
						'description' => Lang::txt('ID of object to retrieve tags for.'),
						'type'        => 'integer',
						'default'     => 'null',
						'requires'    => 'scope'
					),
					'sort' => array(
						'description' => Lang::txt('Sorting to be applied to the records.'),
						'type'        => 'string',
						'default'     => 'date',
						'accepts'     => array('raw_tag', 'id')
					),
					'sortDir' => array(
						'description' => Lang::txt('Direction to sort records by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'limit' => array(
						'description' => Lang::txt('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '25'
					),
					'limitstart' => array(
						'description' => Lang::txt('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
				),
			),
			'tag' => array(
				'description' => Lang::txt('Get information for a tag.'),
				'parameters'  => array(
					'tag' => array(
						'description' => Lang::txt('The tag to retrieve information for.'),
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
		$this->setMessageType(Request::getWord('format', 'json'));

		$cloud = new \Components\Tags\Models\Cloud();

		$filters = array(
			'limit'      => Request::getInt('limit', 25),
			'start'      => Request::getInt('limitstart', 0),
			'search'     => Request::getVar('search', ''),
			'tbl'        => Request::getword('scope', ''),
			'objectid'   => Request::getInt('scope_id', 0),
			'taggerid'   => Request::getVar('tagger', ''),
			'sort'       => Request::getWord('sort', 'raw_tag'),
			'sort_Dir'   => strtoupper(Request::getWord('sortDir', 'ASC'))
		);

		$response = new stdClass;
		$response->tags  = array();
		$response->total = $cloud->tags('count', $filters);

		if ($response->total)
		{
			$juri = \JURI::getInstance();

			foreach ($cloud->tags('list', $filters) as $i => $tag)
			{
				$obj = new stdClass;
				$obj->id    = $tag->get('id');
				$obj->tag   = $tag->get('raw_tag');
				$obj->title = $tag->get('tag');
				$obj->url   = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(Route::url($tag->link()), DS));

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
		$this->setMessageType(Request::getWord('format', 'json'));

		$response = new stdClass;

		$tag = new \Components\Tags\Models\Tag(Request::getWord('tag', ''));
		if (!$tag->exists())
		{
			$response->success = false;

			$this->errorMessage(
				404,
				Lang::txt('Specified tag does not exist.')
			);
			return;
		}

		$juri = \JURI::getInstance();

		$response->id    = $tag->get('id');
		$response->tag   = $tag->get('raw_tag');
		$response->title = $tag->get('tag');
		$response->description = $tag->get('description');
		$response->admin = $tag->get('admin');
		$response->url   = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(Route::url($tag->link()), DS));

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
		$this->setMessageType(Request::getWord('format', 'json'));

		$response = new stdClass;

		$tag = new \Components\Tags\Models\Tag(Request::getWord('tag', ''));
		if (!$tag->exists())
		{
			$response->success = false;

			$this->errorMessage(
				404,
				Lang::txt('Specified tag does not exist.')
			);
			return;
		}

		if (
			!$tag->addTo(
				Request::getWord('scope', ''),
				Request::getInt('scope_id', 0),
				Request::getInt('tagger', 0)
			)
		)
		{
			$response->success = false;

			$this->errorMessage(
				500,
				Lang::txt('Failed to add tag.')
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
		$this->setMessageType(Request::getWord('format', 'json'));

		$response = new stdClass;

		$tag = new \Components\Tags\Models\Tag(Request::getWord('tag', ''));
		if (!$tag->exists())
		{
			$response->success = false;

			$this->errorMessage(
				404,
				Lang::txt('Specified tag does not exist.')
			);
			return;
		}

		if (
			!$tag->removeFrom(
				Request::getWord('scope', ''),
				Request::getInt('scope_id', 0),
				Request::getInt('tagger', 0)
			)
		)
		{
			$response->success = false;

			$this->errorMessage(
				500,
				Lang::txt('Failed to remove tag.')
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
		//get the userid and attempt to load user profile
		$userid = \JFactory::getApplication()->getAuthn('user_id');
		$result = \Hubzero\User\Profile::getInstance($userid);

		//make sure we have a user
		if ($result === false)
		{
			return $this->errorMessage(404, Lang::txt('Not found.'));
		}

		// Create an object for returning messages
		$msg = new stdClass;

		// Any tags?
		$tag = new \Components\Tags\Models\Tag(Request::getVar('tag', '', 'post'));
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

		$this->setMessageType(Request::getVar('format', 'json'));
		$this->setMessage($msg);
	}
}
