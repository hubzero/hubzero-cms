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
class TagsControllerApi extends Hubzero_Api_Controller
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
			$juri =& JURI::getInstance();

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

		$juri =& JURI::getInstance();

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
	 * Create a new ticket
	 *
	 * @return     void
	 */
	private function newTask()
	{
		$this->setMessageType(JRequest::getVar('format', 'json'));

		//get the userid and attempt to load user profile
		$userid = JFactory::getApplication()->getAuthn('user_id');
		$result = Hubzero_User_Profile::getInstance($userid);

		//make sure we have a user
		if ($result === false)
		{
			return $this->not_found();
		}

		/*// Create an object for returning messages
		$msg = new stdClass;

		// Initiate class and bind data to database fields
		$ticket = new SupportTicket($this->database);

		// Set the created date
		$ticket->created   = $msg->submitted = date("Y-m-d H:i:s");

		// Incoming
		$ticket->report   = JRequest::getVar('report', '', 'post', 'none', 2);
		if (!$ticket->report)
		{
			$this->errorMessage(500, JText::_('Error: Report contains no text.'));
			return;
		}
		$ticket->os        = JRequest::getVar('os', 'unknown', 'post');
		$ticket->browser   = JRequest::getVar('browser', 'unknown', 'post');
		$ticket->severity  = JRequest::getVar('severity', 'normal', 'post');

		// Cut suggestion at 70 characters
		$ticket->summary   = substr($ticket->report, 0, 70);
		if (strlen($ticket->summary) >= 70) 
		{
			$ticket->summary .= '...';
		}

		// Get user data
		//$juser = JFactory::getUser();
		$ticket->name      = $result->get('name');
		$ticket->email     = $result->get('email');
		$ticket->login     = $result->get('username');

		// Set some helpful info
		$ticket->instances = 1;
		$ticket->section   = 1;
		$ticket->open      = 1;
		$ticket->status    = 0;

		ximport('Hubzero_Environment');
		$ticket->ip        = Hubzero_Environment::ipAddress();
		$ticket->hostname  = gethostbyaddr(JRequest::getVar('REMOTE_ADDR','','server'));

		// Check the data
		if (!$ticket->check()) 
		{
			$this->errorMessage(500, $ticket->getErrors());
			return;
		}

		// Save the data
		if (!$ticket->store()) 
		{
			$this->errorMessage(500, $ticket->getErrors());
			return;
		}

		// Any tags?
		$tags = trim(JRequest::getVar('tags', '', 'post'));
		if ($tags)
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'tags.php');

			$st = new SupportTags($this->database);
			$st->tag_object($result->get('uidNumber'), $ticket->id, $tags, 0, true);
		}

		// Set the response
		$msg->success = true;
		$msg->ticket  = $ticket->id;*/

		$this->setMessage($msg);
	}
}
