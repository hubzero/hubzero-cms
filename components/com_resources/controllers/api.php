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

JLoader::import('Hubzero.Api.Controller');

/**
 * API controller class for resources
 */
class ResourcesControllerApi extends \Hubzero\Component\ApiController
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

		switch ($this->segments[0])
		{
			case 'whatsnew': $this->whatsnewTask(); break;
			case 'service':
			default:
				$this->serviceTask();
			break;
		}
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param   integer $code    Error Code
	 * @param   string  $message Error Message
	 * @param   string  $format  Error Response Format
	 *
	 * @return  void
	 */
	private function errorMessage($code, $message, $format = 'json')
	{
		// build error code and message
		$object = new stdClass();
		$object->error->code    = $code;
		$object->error->message = $message;

		//set http status code and reason
		$this->getResponse()
		     ->setErrorMessage($object->error->code, $object->error->message);

		// add error to message body
		$this->setMessageType(JRequest::getWord('format', $format));
		$this->setMessage($object);
	}

	/**
	 * Displays a available options and parameters the API
	 * for this comonent offers.
	 *
	 * @return  void
	 */
	protected function serviceTask()
	{
		$response = new stdClass();
		$response->component = 'resources';
		$response->tasks = array(
			'whatsnew' => array(
				'description' => JText::_('Get a list of new content for a given time period.'),
				'parameters'  => array(
					'category' => array(
						'description' => JText::_('Resource type to filter by.'),
						'type'        => 'string',
						'default'     => 'null'
					),
					'period' => array(
						'description' => JText::_('Time period to return results for.'),
						'type'        => 'string',
						'default'     => 'month',
						'accepts'     => array('week', 'month', 'quarter', 'year')
					),
					'limit' => array(
						'description' => JText::_('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '25'
					),
				),
			),
		);

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Get a list of new content for a given time period
	 *
	 * @return    void
	 */
	public function whatsnewTask()
	{
		// get request vars
		$limit    = JRequest::getVar('limit', 25);
		$period   = JRequest::getVar('period', 'month');
		$category = JRequest::getVar('category', 'resources');

		JLoader::import('joomla.plugin.helper');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_whatsnew' . DS . 'helpers' . DS . 'finder.php');

		$whatsnew = WhatsnewHelperFinder::getWhatsNewBasedOnPeriodAndCategory($period, $category, $limit);

		// encode results and return response
		$object = new stdClass();
		$object->whatsnew = $whatsnew;

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($object);
	}
}
