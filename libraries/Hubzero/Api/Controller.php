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

/**
 * Short description for 'Hubzero_Api_Controller'
 * 
 * Long description (if any) ...
 */
class Hubzero_Api_Controller
{

	/**
	 * Description for '_response'
	 * 
	 * @var unknown
	 */
	private $_response = null;

	/**
	 * Description for '_request'
	 * 
	 * @var unknown
	 */
	private $_request = null;

	/**
	 * Description for '_provider'
	 * 
	 * @var unknown
	 */
	private $_provider = null;

	/**
	 * Description for '_segments'
	 * 
	 * @var array
	 */
	private $_segments = array();

	/**
	 * Short description for 'setRequest'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $request Parameter description (if any) ...
	 * @return     void
	 */
	function setRequest($request)
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
	function setResponse($response)
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
		$this->_segments = $segments;
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
		return $this->_segments;
	}
}

?>