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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class GroupsHelperGitlab
{
	/**
	 * URL to GitLab
	 * 
	 * @var [type]
	 */
	private $url;

	/**
	 * GitLab Auth Token
	 * 
	 * @var [type]
	 */
	private $token;

	/**
	 * HTTP Client used for API requests
	 * @var [type]
	 */
	private $client;

	/**
	 * Create new instance of Gitlab helper
	 *
	 * @return void
	 */
	public function __construct($url, $token)
	{
		$this->url    = rtrim($url, DS);
		$this->token  = $token;
		$this->client = new Guzzle\Http\Client();
		$this->client->setSslVerification(false, false);
	}

	/**
	 * Get List of groups on Gitlab
	 * 
	 * @return array
	 */
	public function groups()
	{
		return $this->_getRequest('groups');
	}

	/**
	 * Get a Group by name
	 * 
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function group($name)
	{
		foreach ($this->groups() as $group)
		{
			if ($group['name'] == $name)
			{
				return $group;
			}
		}
		return false;
	}

	/**
	 * Create group based on params
	 * 
	 * @param  array  $params Group params
	 * @return array
	 */
	public function createGroup($params = array())
	{
		return $this->_postRequest('groups', $params);
	}

	/**
	 * Get List of projects on gitlab
	 * 
	 * @return array
	 */
	public function projects()
	{
		return $this->_getRequest('projects');
	}

	/**
	 * Get Project by Name
	 * 
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function project($name)
	{
		foreach ($this->projects() as $project)
		{
			if ($project['name'] == $name)
			{
				return $project;
			}
		}
		return false;
	}

	/**
	 * Create project based on params
	 * 
	 * @param  array  $params Project params
	 * @return array
	 */
	public function createProject($params = array())
	{
		return $this->_postRequest('projects', $params);
	}

	/**
	 * Protect Branch
	 * 
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function protectBranch($params = array())
	{
		$resource = 'projects' . DS . $params['id'] . DS . 'repository' . DS . 'branches' . DS . $params['branch'] . DS . 'protect';
		return $this->_putRequest($resource, array());
	}

	/**
	 * Generic Get Request
	 * 
	 * @param  [type] $url [description]
	 * @return [type]      [description]
	 */
	private function _getRequest($resource)
	{
		// init get request
		$request = $this->client->get($this->url . DS . $resource);

		// add our auth header
		$request->addHeader('PRIVATE-TOKEN', $this->token);

		// send and return response
		$response = $request->send($request);
		return $response->json();
	}

	/**
	 * Generic Post request
	 * 
	 * @param  [type] $resource [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	private function _postRequest($resource, $params = array())
	{
		// init post request
		$request = $this->client->post($this->url . DS . $resource);

		// set post fields
		foreach ($params as $key => $value)
		{
			$request->setPostField($key, $value);
		}

		// add our auth header
		$request->addHeader('PRIVATE-TOKEN', $this->token);

		// send and return response
		$response = $request->send($request);
		return $response->json();
	}

	/**
	 * Generic Put Request
	 * 
	 * @param  [type] $resource [description]
	 * @param  array  $params   [description]
	 * @return [type]           [description]
	 */
	public function _putRequest($resource, $params = array())
	{
		// init post request
		$request = $this->client->put($this->url . DS . $resource);

		// set post fields
		foreach ($params as $key => $value)
		{
			$request->setPostField($key, $value);
		}

		// add our auth header
		$request->addHeader('PRIVATE-TOKEN', $this->token);

		// send and return response
		$response = $request->send($request);
		return $response->json();
	}
}