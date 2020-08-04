<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth\GrantType;

use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\ResponseType\AccessTokenInterface;

/**
 * User credentials grant type
 */
class UserCredentials implements GrantTypeInterface
{
	/**
	 * Store object
	 *
	 * @var  object
	 */
	private $storage;

	/**
	 * Array to hold authenticated user data
	 *
	 * @var  array
	 */
	private $userInfo = [];

	/**
	 * Constructor
	 *
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Config array
	 * @return  void
	 */
	public function __construct(UserCredentialsInterface $storage, $config = array())
	{
		$this->storage = $storage;
	}

	/**
	 * Define identifier for this type of grant
	 *
	 * @return  string  identifier
	 */
	public function getQuerystringIdentifier()
	{
		return 'password';
	}

	/**
  * Validate request via session data
  *
  * This is used for internal requests via ajax
  *
  * @param   object  $request   Request object
  * @param   object  $response  Response object
  * @return  bool    Result of auth
  */
	public function validateRequest(RequestInterface $request, ResponseInterface $response)
	{
		// ensure we have needed params
		if (!$request->request("password") || !$request->request("username"))
		{
			$response->setError(400, 'invalid_request', 'Missing parameters: "username" and "password" required');
			return null;
		}

		// check username/password
		if (!$this->storage->checkUserCredentials($request->request("username"), $request->request("password")))
		{
			$response->setError(401, 'invalid_grant', 'Invalid username and password combination');
			return null;
		}

		// get user details by username
		$userInfo = $this->storage->getUserDetails($request->request("username"));

		// make sure we got an array of user details
		if (empty($userInfo))
		{
			$response->setError(400, 'invalid_grant', 'Unable to retrieve user information');
			return null;
		}

		// if not set, something went wrong
		if (!isset($userInfo['user_id']))
		{
			throw new \LogicException("you must set the user_id on the array returned by getUserDetails");
		}

		// set our userinfo for later use
		$this->userInfo = $userInfo;

		// return sucess
		return true;
	}

	/**
	 * Get client id
	 *
	 * @return  null
	 */
	public function getClientId()
	{
		return null;
	}

	/**
	 * Get user id
	 *
	 * @return  int  User identifier
	 */
	public function getUserId()
	{
		return $this->userInfo['user_id'];
	}

	/**
	 * Get scope
	 *
	 * @return  string  Scope
	 */
	public function getScope()
	{
		return isset($this->userInfo['scope']) ? $this->userInfo['scope'] : null;
	}

	/**
	 * Create access token
	 *
	 * @param   object  $accessToken  Access token object
	 * @param   string  $client_id    Authorized client
	 * @param   string  $user_id      User identifier
	 * @param   string  $scope        Client application scope
	 * @return  string  Access token
	 */
	public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
	{
		return $accessToken->createAccessToken($client_id, $user_id, $scope);
	}
}
