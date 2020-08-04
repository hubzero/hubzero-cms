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
use OAuth2\Storage\RefreshTokenInterface;
use OAuth2\ResponseType\AccessTokenInterface;

/**
 * Refesh token grant type
 */
class RefreshToken implements GrantTypeInterface
{
	/**
	 * Store object
	 *
	 * @var  object
	 */
	private $storage;

	/**
	 * Array to hold config
	 *
	 * @var  array
	 */
	private $config = [];

	/**
	 * Array to hold token data
	 *
	 * @var  array
	 */
	private $refreshToken = [];

	/**
	 * Constructor
	 *
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Config array
	 * @return  void
	 */
	public function __construct(RefreshTokenInterface $storage, $config = array())
	{
		$this->storage = $storage;
		$this->config  = array_merge(array(
			'always_issue_new_refresh_token' => false
		), $config);
	}

	/**
	 * Define identifier for this type of grant
	 *
	 * @return  string  identifier
	 */
	public function getQuerystringIdentifier()
	{
		return 'refresh_token';
	}

	/**
  * Exchange refresh token or new access token
  *
  * @param   object  $request   Request object
  * @param   object  $response  Response object
  * @return  bool    Result of auth
  */
	public function validateRequest(RequestInterface $request, ResponseInterface $response)
	{
		// make sure request has a refresh token
		if (!$request->request("refresh_token"))
		{
			$response->setError(400, 'invalid_request', 'Missing parameter: "refresh_token" is required');
			return null;
		}

		// load token details
		if (!$refreshToken = $this->storage->getRefreshToken($request->request("refresh_token")))
		{
			$response->setError(400, 'invalid_grant', 'Invalid refresh token');
			return null;
		}

		// make sure token hasnt expired
		if ($refreshToken['expires'] > 0 && $refreshToken["expires"] < time())
		{
			$response->setError(400, 'invalid_grant', 'Refresh token has expired');
			return null;
		}

		// store the refresh token locally so we can delete it when a new refresh token is generated
		$this->refreshToken = $refreshToken;
		return true;
	}

	/**
	 * Get client id
	 *
	 * @return  null
	 */
	public function getClientId()
	{
		return $this->refreshToken['client_id'];
	}

	/**
	 * Get user id
	 *
	 * @return  int  User identifier
	 */
	public function getUserId()
	{
		return isset($this->refreshToken['uidNumber']) ? $this->refreshToken['uidNumber'] : null;
	}

	/**
	 * Get scope
	 *
	 * @return  string  Scope
	 */
	public function getScope()
	{
		return isset($this->refreshToken['scope']) ? $this->refreshToken['scope'] : null;
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
		// do we want to issue a new refresh token?
		$issueNewRefreshToken = $this->config['always_issue_new_refresh_token'];

		// create access token
		$token = $accessToken->createAccessToken($client_id, $user_id, $scope, $issueNewRefreshToken);

		// if we issued a new refresh token we must delete the old one
		if ($issueNewRefreshToken)
		{
			$this->storage->unsetRefreshToken($this->refreshToken['refresh_token']);
		}

		// return new access token
		return $token;
	}
}
