<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api;

use Hubzero\Container\Container;
use Hubzero\Oauth\Server;
use Hubzero\Oauth\Storage\Mysql as MysqlStorage;
use Hubzero\User\User;
use Exception;
use Event;

/**
 * Authentication class, provides an interface for the authentication system
 */
class Guard
{
	/**
	 * The oauth token data
	 *
	 * @var  array
	 */
	private $token = null;

	/**
	 * The application implementation
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * Constructor
	 *
	 * @param   object  $app  Container
	 * @return  void
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Grabs and returns the oauth token data
	 *
	 * @return  array
	 */
	public function token()
	{
		return $this->token;
	}

	/**
	 * Validates incoming request via OAuth2 specification
	 *
	 * @param   array  $params   Oauth server request parameters
	 * @param   array  $options  OAuth server configuration options
	 * @return  array
	 */
	public function authenticate($params = array(), $options = array())
	{
		// Placeholder response
		$response = ['user_id' => null];

		// Fire before auth event
		Event::trigger('before_auth');

		// Load oauth server
		$oauthServer   = new Server(new MysqlStorage, $options);
		$oauthRequest  = \OAuth2\Request::createFromGlobals();
		$oauthResponse = new \OAuth2\Response();

		// Validate request via oauth
		$oauthServer->verifyResourceRequest($oauthRequest, $oauthResponse);

		// Store our token locally
		$this->token = $oauthServer->getAccessTokenData($oauthRequest);

		// See if we have a valid user
		if (isset($this->token['uidNumber']))
		{
			$response['user_id'] = $this->token['uidNumber'];
			$user = User::oneOrNew($response['user_id']);
			if ($user->get('id'))
			{
				$user->set('guest', false);
			}
			$this->app['session']->set('user', $user);
		}

		// Fire after auth event
		Event::trigger('after_auth');

		// Return the response
		return $response;
	}
}
