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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

class Hubzero_Api_Controller
{
	private $_response = null;
	private $_request = null;
	private $_provider = null;
	private $_segments = array();
	
	function setRequest($request)
	{
		$this->_request = $request;
	}
	
	function getRequest()
	{
		return $this->_request;
	}
	
	function setResponse($response)
	{
		$this->_response = $response;
	}
	
	function getResponse()
	{
		return $this->_response;
	}
	
	function setProvider($provider)
	{
		$this->_provider = $provider;
	}
	
	function getProvider()
	{
		return $this->_provider;
	}
	
	function setRouteSegments($segments)
	{
		$this->_segments = $segments;
	}
	
	function getRouteSegments()
	{
		return $this->_segments;
	}
}

?>