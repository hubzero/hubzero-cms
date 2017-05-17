<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

class Gitlab
{
	/**
	 * URL to GitLab
	 * 
	 * @var  string
	 */
	private $url;

	/**
	 * Default Guzzle options/ headers
	 *
	 * @var array
	 */
	private $options;

	/**
	 * GitLab Auth Token
	 * 
	 * @var  string
	 */
	private $token;

	/**
	 * HTTP Client used for API requests
	 *
	 * @var  object
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
		$this->options = array(
			'verify' => false,
			'headers' => array('PRIVATE-TOKEN' => $token)
		);
		$this->client = new \GuzzleHttp\Client;

		// Method removed in Guzzle 6.0
		//$this->client->setDefaultOption('verify', false);
	}

	/**
	 * Get List of groups on Gitlab
	 * 
	 * @return  array
	 */
	public function groups()
	{
		return $this->_getRequest('groups');
	}

	/**
	 * Get a Group by name
	 * 
	 * @param   string   $name
	 * @return  boolean
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
	 * @param   array  $params  Group params
	 * @return  array
	 */
	public function createGroup($params = array())
	{
		return $this->_postRequest('groups', $params);
	}

	/**
	 * Get List of projects on gitlab
	 * 
	 * @return  array
	 */
	public function projects()
	{
		return $this->_getRequest('projects');
	}

	/**
	 * Get Project by Name
	 * 
	 * @param   string   $name
	 * @return  boolean
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
	 * @param   array  $params  Project params
	 * @return  array
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
	 * @param   string  $url
	 * @return  string
	 */
	private function _getRequest($resource)
	{
		// add our auth header
		$headers = array('PRIVATE-TOKEN' => $this->token);

		// init get request
		$response = $this->client->request('GET', $this->url . DS . $resource, $this->options);

		// json() method removed in Guzzle 6.0
		// return $respone->json();

		return json_decode($response->getBody(), true);
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

		$requestOptions = array_merge(array('query' => $params), $this->options);
		$response = $this->client->request('POST', $this->url . DS . $resource, $requestOptions);

		// json() method removed in Guzzle 6.0
		// return $response->json();

		return json_decode($response->getBody(), true);
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
		$requestOptions = array_merge(array('form_params' => $params), $this->options);

		// init post request
		$response = $this->client->request('PUT', $this->url . DS . $resource, $requestOptions);

		// json() method removed in Guzzle 6.0
		// return $response->json();

		return json_decode($response->getBody(), true);
	}
}
