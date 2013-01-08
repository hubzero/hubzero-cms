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

/**
 * API controller class for support tickets
 */
class SupportApiController extends Hubzero_Api_Controller
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

		$this->segments = $this->getRouteSegments();
		$this->response = $this->getResponse();

		$this->acl = SupportACL::getACL();

		switch ($this->segments[0]) 
		{
			case 'ticket':  $this->ticket();  break;
			case 'tickets': $this->tickets(); break;
			default:        $this->error();   break;
		}
	}

	/**
	 * Short description for 'not_found'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function error()
	{
		$this->getResponse()->setErrorMessage(404, 'Not Found');
	}

	/**
	 * Displays a list of tickets
	 *
	 * @return    void
	 */
	public function tickets()
	{
		//get request vars
		$format = JRequest::getVar('format', 'json');

		$limit = JRequest::getInt('limit', 25);
		$start = JRequest::getInt('limitstart', 0);
		//$period = JRequest::getVar('period', 'month');
		//$category = JRequest::getVar('category', '');

		/*if ($this->acl->check('read', 'tickets')) 
		{
		}

		ximport('Hubzero_Whatsnew');
		JLoader::import('joomla.plugin.helper');

		//encode results and return response
		$object = new stdClass();
		$object->tickets = Hubzero_Whatsnew::getWhatsNewBasedOnPeriodAndCategory( $period, $category, $limit );*/

		$obj = new SupportTicket($this->database);
		$obj->tickets = null;

		//$this->response->setResponseProvides($format);
		//$this->response->setMessage($object);
		$this->setMessageType($format);
		$this->setMessage($obj);
	}

	/**
	 * Create a new ticket
	 *
	 * @return     void
	 */
	private function create()
	{
		$this->getResponse()->setErrorMessage(404, 'Not Found');
	}
}
