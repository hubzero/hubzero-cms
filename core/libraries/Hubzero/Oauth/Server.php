<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth;

use OAuth2\Server as OAuth2Server;
use Hubzero\Oauth\Storage\Mysql as MysqlStorage;
use Hubzero\Oauth\GrantType\RefreshToken as RefreshTokenGrantType;
use Hubzero\Oauth\GrantType\SessionToken as SessionTokenGrantType;
use Hubzero\Oauth\GrantType\ToolSessionToken as ToolSessionTokenGrantType;
use Hubzero\Oauth\GrantType\UserCredentials as UserCredentialsGrantType;
use Hubzero\Oauth\GrantType\ClientCredentials as ClientCredentialsGrantType;
use Hubzero\Oauth\GrantType\AuthorizationCode as AuthorizationCodeGrantType;

/**
 * Hubzero OAuth2 Server
 */
class Server
{
	/**
	 * Internal var to hold true Oauth Server
	 *
	 * @var  object
	 */
	private $server = null;

	/**
	 * Config values
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Constructor to setup setup server
	 *
	 * @param   object  $storage
	 * @param   array   $config
	 * @return  void
	 */
	public function __construct($storage, $options = array())
	{
		// create config with defaults allowing overriding via API config
		$this->config = array_merge(array(
			'enforce_state'                     => true,
			'access_lifetime'                   => 3600,
			'refresh_token_lifetime'            => 7200,
			'require_exact_redirect_uri'        => true,
			'allow_credentials_in_request_body' => true,
			'always_issue_new_refresh_token'    => true
		), $options);

		// available grant types
		$grantTypes = array(
			new UserCredentialsGrantType($storage, $this->config),
			new RefreshTokenGrantType($storage, $this->config),
			new AuthorizationCodeGrantType($storage, $this->config),
			new ClientCredentialsGrantType($storage, $this->config),
			new SessionTokenGrantType($storage, $this->config),
			new ToolSessionTokenGrantType($storage, $this->config)
		);

		// Pass a storage object or array of storage objects to the OAuth2 server class
		$this->server = new OAuth2Server($storage, $this->config, $grantTypes);
	}

	/**
	 * Call All methods on OAuth2 Server
	 *
	 * @param   string  $name  Method Name
	 * @param   mixed   $args  Method Args
	 * @return  mixed          Result of calling method of server
	 */
	public function __call($name, $args)
	{
		// call method on OAuth2 Server
		$response = call_user_func_array(array($this->server, $name), $args);

		// If its an OAuth2\Response object that means it was used for token fetching or autorization
		// and we can modify it if its an error, otherwise we want to leave the result alone.
		// Also check to see if the response code is a error (4xx or 5xx)
		if ($response instanceof \OAuth2\Response
			&& ($response->isClientError() || $response->isServerError()))
		{
			// rewrite parameters (response body) to a
			// standard error format used throughout the api
			$response->setParameters(array(
				'message' => $response->getStatusText(),
				'code'    => $response->getStatusCode(),
				'errors'  => array(
					$response->getParameters()
				)
			));
		}

		// return response
		return $response;
	}

	/**
	 * Accessor for the server's config
	 *
	 * @return  array          The server instances config
	 */
	public function getConfig()
	{
		return $this->config;
	}
}
