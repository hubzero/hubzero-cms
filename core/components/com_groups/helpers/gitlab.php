<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

	}

	/**
	 * Search List of groups on Gitlab
	 * 
	 * @return  array
	 */
	public function groups($groupName)
	{
		return $this->_getRequest('groups', $groupName);
	}

	/**
	 * Get a Group by name
	 * 
	 * @param   string   $name
	 * @return  boolean
	 */
	public function group($name)
	{
		foreach ($this->groups($name) as $group)
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
	 * Search list of projects on gitlab
	 * 
	 * @return  array
	 */
	public function projects($projectName)
	{
		return $this->_getRequest('projects', $projectName);
	}

	/**
	 * Get Project by Name
	 * 
	 * @param   string   $name
	 * @return  boolean
	 */
	public function project($name)
	{
		foreach ($this->projects($name) as $project)
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
	private function _getRequest($resource, $ResourceName)
	{
		// Get response restricted by current owned, i.e. the current Gitlab users that owns the API key that is configured on this hub
		$response = $this->client->request('GET', $this->url . DS . $resource . '?owned=true&search=' . $ResourceName, $this->options);
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

		return json_decode($response->getBody(), true);
	}
}
