<?php

// phpcs:disable PSR1.Files.SideEffects
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
     * Is GitLab repo managed?
     *
     * @var boolean
     */
    private $active;

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
    public function __construct()
    {
        $config = \Component::params('com_groups');

        $this->active = $config->get('super_gitlab', 0);
        $this->url    = rtrim($config->get('super_gitlab_url', ''), DS);
        $this->token  = $config->get('super_gitlab_key', '');
        $this->options = array(
            'verify' => false,
            'headers' => array('PRIVATE-TOKEN' => $this->token),
            'http_errors' => false
        );
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * Method to get private variable
     *
     * @param   string  $property  Name of variable to retrieve
     * @return  mixed   Value of the variable
     */
    public function get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }
    }

    /**
     * Is GitLab repo management on with API url and token set?
     *
     * @return  boolean
     */
    public function validate()
    {
        // do we have repo management on
        // make sure we have a url and key if repot management is on
        if (!$this->active || ($this->active && ($this->url == '' || $this->token == ''))) {
            Notify::warning(Lang::txt('COM_GROUPS_GITLAB_NOT_SETUP'));
            return false;
        }

        // Check for valid token
        $tokenUrl = $this->url . DS . 'personal_access_tokens' . DS . 'self';
        $response = $this->client->request('GET', $tokenUrl, $this->options);
        $json_response = json_decode($response->getBody(), true);
        if (array_key_exists('message', $json_response)) {
            switch ($json_response['message']) {
                case '401 Unauthorized':
                    Notify::warning(Lang::txt('COM_GROUPS_GITLAB_TOKEN_PROBLEM'));
                    return false;
                break;
            }
        }

        return true;
    }

    /**
     * Search List of groups on Gitlab
     *
     * @return  array
     */
    public function groups($groupName)
    {
        return $this->request('GET', 'groups', $groupName);
    }

    /**
     * Get a Group by name
     *
     * @param   string   $name
     * @return  boolean
     */
    public function group($name)
    {
        foreach ($this->groups($name) as $group) {
            if ($group['name'] == $name) {
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
        return $this->request('POST', 'groups', $params);
    }

    /**
     * Search list of projects on gitlab
     *
     * @return  array
     */
    public function projects($projectName)
    {
        return $this->request('GET', 'projects', $projectName);
    }

    /**
     * Get Project by Name
     *
     * @param   string   $name
     * @return  boolean
     */
    public function project($name)
    {
        foreach ($this->projects($name) as $project) {
            if ($project['name'] == $name) {
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
        return $this->request('POST', 'projects', $params);
    }

    /**
     * Protect Branch
     *
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function protectBranch($params = array())
    {
        $resource = 'projects' . DS . $params['id'] . DS . 'repository' . DS
            . 'branches' . DS . $params['branch'] . DS . 'protect';
        return $this->request('PUT', $resource, array());
    }

    /**
     * Generic Get Request
     *
     * @param   string  $url
     * @return  string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    private function _getRequest($resource, $ResourceName)
    {
        // Get response restricted by maintainer access of the current Gitlab API key bot
        // (MAKE SURE GROUP TOKEN ACCESS IS AT MAINTAINER LEVEL OR HIGHER)
        $requestUrl = $this->url . DS . $resource
            . '?min_access_level=40&search=' . $ResourceName;
        $response = $this->client->request('GET', $requestUrl, $this->options);
        return json_decode($response->getBody(), true);
    }

    /**
     * Generic Post request
     *
     * @param  [type] $resource [description]
     * @param  array  $params   [description]
     * @return [type]           [description]
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function _putRequest($resource, $params = array())
    {
        $requestOptions = array_merge(array('form_params' => $params), $this->options);

        // init post request
        $response = $this->client->request('PUT', $this->url . DS . $resource, $requestOptions);

        return json_decode($response->getBody(), true);
    }

    public function request($method, $resource, $params = array())
    {
        $method = '_' . strtolower($method) . 'Request';
        $response = call_user_func(array(__CLASS__, $method), ...array($resource, $params));
        if (array_key_exists('message', $response)) {
            switch ($response['message']) {
                case '401 Unauthorized':
                    $response['message'] = "Gitlab API token either doesn't exist or has expired";
                    break;
            }
        }
        return $response;
    }
}
