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
use OAuth2\Storage\AuthorizationCodeInterface;
use OAuth2\ResponseType\AccessTokenInterface;

/**
 * Authorixation Code Grant Type
 */
class AuthorizationCode implements GrantTypeInterface
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
	 * Array to hold client data
	 *
	 * @var  array
	 */
	private $authorizationCode;

	/**
	 * Constructor
	 *
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Config array
	 * @return  void
	 */
	public function __construct(AuthorizationCodeInterface $storage, array $config = array())
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
		return 'authorization_code';
	}

	/**
	 * Validate request via client
	 *
	 * @param   object  $request   Request object
	 * @param   object  $response  Response object
	 * @return  bool    Result of auth
	 */
	public function validateRequest(RequestInterface $request, ResponseInterface $response)
	{
		// make sure we have a code param
		if (!$code = $request->request('code'))
		{
			$response->setError(400, 'invalid_request', 'Missing parameter: "code" is required');
			return false;
		}

		// verify code param
		if (!$authCode = $this->storage->getAuthorizationCode($code))
		{
			$response->setError(400, 'invalid_grant', 'Authorization code doesn\'t exist or is invalid for the client');
			return false;
		}

		// make sure "redirect_uri" parameter is present if the "redirect_uri" parameter was included in the initial authorization request
		if (isset($authCode['redirect_uri']) && $authCode['redirect_uri'])
		{
			if (!$request->request('redirect_uri') || urldecode($request->request('redirect_uri')) != $authCode['redirect_uri'])
			{
				$response->setError(400, 'redirect_uri_mismatch', "The redirect URI is missing or do not match", "#section-4.1.3");
				return false;
			}
		}

		// must have expiration
		if (!isset($authCode['expires']))
		{
			throw new \Exception('Storage must return authcode with a value for "expires"');
		}

		// checkk code isnt expired
		if ($authCode["expires"] < time())
		{
			$response->setError(400, 'invalid_grant', "The authorization code has expired");
			return false;
		}

		// make sure have the actual auth code
		if (!isset($authCode['code']))
		{
			$authCode['code'] = $code; // used to expire the code after the access token is granted
		}

		// store locally
		$this->authCode = $authCode;
		return true;
	}

	/**
	 * Get client id
	 *
	 * @return  null
	 */
	public function getClientId()
	{
		return $this->authCode['client_id'];
	}

	/**
	 * Get user id
	 *
	 * @return  int  User identifier
	 */
	public function getUserId()
	{
		return isset($this->authCode['uidNumber']) ? $this->authCode['uidNumber'] : null;
	}

	/**
	 * Get scope
	 *
	 * @return  string  Scope
	 */
	public function getScope()
	{
		return isset($this->authCode['scope']) ? $this->authCode['scope'] : null;
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
		// create access token
		$token = $accessToken->createAccessToken($client_id, $user_id, $scope);

		// expire auth code
		$this->storage->expireAuthorizationCode($this->authCode['code']);

		// return token
		return $token;
	}
}
