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

use Hubzero\Http\Request;
use Hubzero\Http\Response;

/**
 * Base API controller for components to extend.
 */
class ApiController implements ControllerInterface
{
	/**
	 * Methods needing Auth
	 * 
	 * @var  array
	 */
	public $authenticated   = array('all');

	/**
	 * Methods skipping Auth
	 * 
	 * @var  array
	 */
	public $unauthenticated = array();

	/**
	 * Methods needing rate limiting
	 * 
	 * @var  array
	 */
	public $rateLimited     = array();

	/**
	 * Methods skipping rate limiting
	 * 
	 * @var  array
	 */
	public $notRateLimited  = array('all');

	/**
	 * Description for '_response'
	 *
	 * @var  object
	 */
	public $_response = null;

	/**
	 * Description for '_request'
	 *
	 * @var  object
	 */
	public $_request = null;

	/**
	 * Description for '_provider'
	 *
	 * @var  object
	 */
	public $_provider = null;

	/**
	 * Description for '_segments'
	 *
	 * @var  array
	 */
	public $segments = array();

	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return	void
	 */
	public function execute()
	{
		$response = new \stdClass();
		$response->error->code    = 500;
		$response->error->message = \Lang::txt('Component must implement the execute() method.');

		$this->getResponse()
		     ->setErrorMessage($response->error->code, $response->error->message);

		$this->setMessageType(\Request::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Set the request object
	 *
	 * @param   object  $request
	 * @return  void
	 */
	function setRequest(Request $request)
	{
		$this->_request = $request;
	}

	/**
	 * Get the request object
	 *
	 * @return  object
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Set the response object
	 *
	 * @param   objet  $response
	 * @return  void
	 */
	public function setResponse(Response $response)
	{
		$this->_response = $response;
	}

	/**
	 * Get the response object
	 *
	 * @return  object
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Set the provider
	 *
	 * @param   object  $provider
	 * @return  void
	 */
	public function setProvider($provider)
	{
		$this->_provider = $provider;
	}

	/**
	 * Get provider
	 *
	 * @return  object
	 */
	public function getProvider()
	{
		return $this->_provider;
	}

	/**
	 * Set the list of route segments
	 *
	 * @param   array  $segments
	 * @return  void
	 */
	public function setRouteSegments($segments)
	{
		$this->segments = $segments;
	}

	/**
	 * Get the list of route segments
	 *
	 * @return  array
	 */
	public function getRouteSegments()
	{
		return $this->segments;
	}

	/**
	 * Set response content
	 *
	 * @param   string   $message
	 * @param   integer  $status
	 * @param   string   $reason
	 * @return  void
	 */
	public function setMessage($message = null, $status = null, $reason = null)
	{
		//$this->_response->setMessage($message, $status, $reason);
		//$this->_response->setContent($this->finalizeContent($message));
		$this->_response->setContent($message);
		$this->_response->setStatusCode($status ? $status : 200);
	}

	/**
	 * Set response format
	 *
	 * @param   string   $format
	 * @return  void
	 */
	public function setMessageType($format)
	{
		//$this->_response->setResponseProvides($format);
		static $types = array(
			'xml'   => 'application/xml',
			'html'  => 'text/html',
			'xhtml' => 'application/xhtml+xml',
			'json'  => 'application/json',
			'text'  => 'text/plain',
			'txt'   => 'text/plain',
			'plain' => 'text/plain',
			'php'   => 'application/php',
			'php_serialized' => 'application/vnd.php.serialized',
		);

		$this->_response->headers->set('Content-Type', $types[$format]);
	}
}

