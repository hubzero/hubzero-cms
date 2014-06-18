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
 */

namespace Hubzero\Component;

use Hubzero\Api\Request;
use Hubzero\Api\Response;

/**
 * Base API controller for components to extend.
 */
class ApiController implements ControllerInterface
{
	/**
	 * Description for '_response'
	 *
	 * @var unknown
	 */
	protected $_response = null;

	/**
	 * Description for '_request'
	 *
	 * @var unknown
	 */
	protected $_request = null;

	/**
	 * Description for '_provider'
	 *
	 * @var unknown
	 */
	protected $_provider = null;

	/**
	 * Description for '_segments'
	 *
	 * @var array
	 */
	protected $segments = array();

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$response = new \stdClass();
		$response->error->code    = 500;
		$response->error->message = \JText::_('Component must implement the execute() method.');

		$this->getResponse()
		     ->setErrorMessage($response->error->code, $response->error->message);

		$this->setMessageType(\JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Short description for 'setRequest'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $request Parameter description (if any) ...
	 * @return     void
	 */
	function setRequest(Request $request)
	{
		$this->_request = $request;
	}

	/**
	 * Short description for 'getRequest'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Short description for 'setResponse'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $response Parameter description (if any) ...
	 * @return     void
	 */
	function setResponse(Response $response)
	{
		$this->_response = $response;
	}

	/**
	 * Short description for 'getResponse'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Short description for 'setProvider'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $provider Parameter description (if any) ...
	 * @return     void
	 */
	function setProvider($provider)
	{
		$this->_provider = $provider;
	}

	/**
	 * Short description for 'getProvider'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getProvider()
	{
		return $this->_provider;
	}

	/**
	 * Short description for 'setRouteSegments'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $segments Parameter description (if any) ...
	 * @return     void
	 */
	function setRouteSegments($segments)
	{
		$this->segments = $segments;
	}

	/**
	 * Short description for 'getRouteSegments'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	function getRouteSegments()
	{
		return $this->segments;
	}

	function setMessage($message = null, $status = null, $reason = null)
	{
		$this->_response->setMessage($message, $status, $reason);
	}

	function setMessageType($format)
	{
		$this->_response->setResponseProvides($format);
	}
}

