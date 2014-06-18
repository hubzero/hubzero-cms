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

JLoader::import('Hubzero.Component.ApiController');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'author.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'tags.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'sponsor.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'format.php');

/**
 * API controller class for support tickets
 */
class CitationsControllerApi extends \Hubzero\Component\ApiController
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
			case 'list': $this->citationsTask(); break;

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
		$response->component = 'citations';
		$response->tasks = array(
			'list' => array(
				'description' => JText::_('Get a list of citations.'),
				'parameters'  => array(
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
	private function citationsTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$database = JFactory::getDBO();

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'sort'       => JRequest::getVar('sort', 'created'),
			'state'      => 1,
			'sort_Dir'   => strtoupper(JRequest::getWord('sortDir', 'DESC'))
		);

		//get the earliest year we have citations for
		$query = "SELECT c.year FROM `#__citations` as c WHERE c.published=1 AND c.year <> 0 AND c.year IS NOT NULL ORDER BY c.year ASC LIMIT 1";
		$database->setQuery($query);
		$earliest_year = $database->loadResult();
		$earliest_year = ($earliest_year) ? $earliest_year : 1990;

		$filters['id']              = JRequest::getInt('id', 0);
		$filters['tag']             = trim(JRequest::getVar('tag', '', 'request', 'none', 2));
		$filters['type']            = JRequest::getVar('type', '');
		$filters['author']          = JRequest::getVar('author', '');
		$filters['publishedin']     = JRequest::getVar('publishedin', '');
		$filters['year_start']      = JRequest::getInt('year_start', $earliest_year);
		$filters['year_end']        = JRequest::getInt('year_end', date("Y"));
		$filters['filter']          = JRequest::getVar('filter', '');
		$filters['reftype']         = JRequest::getVar('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$filters['geo']             = JRequest::getVar('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$filters['aff']             = JRequest::getVar('aff', array('university' => 1, 'industry' => 1, 'government' => 1));
		$filters['startuploaddate'] = JRequest::getVar('startuploaddate', '0000-00-00');
		$filters['enduploaddate']   = JRequest::getVar('enduploaddate', '0000-00-00');

		$filters['sort'] = $filters['sort'] . ' ' . $filters['sort_Dir'];

		if ($collection = JRequest::getInt('collection', 0))
		{
			$filters['collection_id'] = $collection;
		}

		$response = new stdClass;
		$response->citations = array();

		// Instantiate a new citations object
		$obj = new CitationsCitation($database);

		// Get a record count
		$response->total = $obj->getCount($filters);

		// Get records
		if ($response->total)
		{
			$juri = JURI::getInstance();

			$href = 'index.php?option=com_citations&task=view&id=';
			$base = str_replace('/api', '', rtrim($juri->base(), DS));

			foreach ($obj->getRecords($filters) as $i => $entry)
			{
				$entry->url = $base . DS . ltrim(JRoute::_($href . $entry->id), DS);

				$response->citations[] = $entry;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}
}
