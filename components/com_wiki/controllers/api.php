<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

JLoader::import('Hubzero.Component.ApiController');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'book.php');

/**
 * API controller class for support tickets
 */
class WikiControllerApi extends \Hubzero\Component\ApiController
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

		$this->config   = JComponentHelper::getParams('com_wiki');
		$this->database = JFactory::getDBO();

		switch ($this->segments[0])
		{
			case 'search':    $this->pagesTask();     break;
			case 'pages':     $this->pagesTask();     break;
			case 'page':      $this->pageTask();      break;
			case 'revisions': $this->revisionsTask(); break;

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
	 * Displays a available options and parameters the API
	 * for this comonent offers.
	 *
	 * @return  void
	 */
	private function serviceTask()
	{
		$response = new stdClass();
		$response->component = 'wiki';
		$response->tasks = array(
			'pages' => array(
				'description' => JText::_('Get a list of pages.'),
				'parameters'  => array(
					'sort' => array(
						'description' => JText::_('Field to sort results by.'),
						'type'        => 'string',
						'default'     => 'created',
						'accepts'     => array('created', 'title', 'alias', 'id', 'publish_up', 'publish_down', 'state')
					),
					'sort_Dir' => array(
						'description' => JText::_('Direction to sort results by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'search' => array(
						'description' => JText::_('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
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
			'page' => array(
				'description' => JText::_('Get the contents of a page. If no revision number is provided, the page will default to the most current revision.'),
				'parameters'  => array(
					'pagename' => array(
						'description' => JText::_('Page name to retrieve data for.'),
						'type'        => 'string',
						'default'     => ''
					),
					'scope' => array(
						'description' => JText::_('Page scope.'),
						'type'        => 'string',
						'default'     => ''
					),
					'revision' => array(
						'description' => JText::_('Revision number of page.'),
						'type'        => 'integer',
						'default'     => '0'
					),
				),
			),
			'revisions' => array(
				'description' => JText::_('Get a list of revisions for a page.'),
				'parameters'  => array(
					'pageid' => array(
						'description' => JText::_('The ID of the page to return revisions for.'),
						'type'        => 'integer',
						'default'     => '0',
						'required'    => 'true'
					),
					'sort' => array(
						'description' => JText::_('Field to sort results by.'),
						'type'        => 'string',
						'default'     => 'created',
						'accepts'     => array('created', 'title', 'alias', 'id', 'publish_up', 'publish_down', 'state')
					),
					'sort_Dir' => array(
						'description' => JText::_('Direction to sort results by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'search' => array(
						'description' => JText::_('A word or phrase to search for.'),
						'type'        => 'string',
						'default'     => 'null'
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
		);

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function pagesTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$group = JRequest::getVar('group', '');

		$book = new WikiModelBook(($group ? $group : '__site__'));

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'sort'       => JRequest::getWord('sort', 'title'),
			'sort_Dir'   => strtoupper(JRequest::getWord('sortDir', 'ASC')),
			'group'      => JRequest::getWord('sort', 'title')
		);
		$filters['state'] = array(0, 1);
		$filters['sortby'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		$response = new stdClass;
		$response->pages = array();
		$response->total = $book->pages('count', $filters);

		if ($response->total)
		{
			$juri = JURI::getInstance();

			foreach ($book->pages('list', $filters) as $i => $entry)
			{
				$obj = new stdClass;
				$obj->id        = $entry->get('id');
				$obj->title     = $entry->get('title');
				$obj->name      = $entry->get('name');
				$obj->scope     = $entry->get('scope');
				$obj->url       = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(JRoute::_($entry->link()), DS));
				$obj->revisions = $entry->revisions('count');

				$response->pages[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function pageTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$group = JRequest::getVar('group', '');

		$book = new WikiModelBook(($group ? $group : '__site__'));
		$page = $book->page();

		$revision = $page->revision(JRequest::getInt('revision', 0));

		$response = new stdClass;
		$response->page = new stdClass;
		$response->page->id = $page->get('id');
		$response->page->name = $page->get('pagename');
		$response->page->title = $page->get('title');
		$response->page->scope = $page->get('scope');
		$response->page->content = $revision->content('raw');
		$response->page->scope = $page->get('scope');
		$response->page->revision_id = $page->get('version_id');
		$response->revisions = 0;

		$response->page->url = str_replace('/api', '', rtrim($juri->base(), DS) . DS . ltrim(JRoute::_($page->link()), DS));

		$response->success = true;

		$this->setMessage($response);
	}
}
