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
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth\GrantType;

use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\ClientAssertionType\ClientAssertionTypeInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use Hubzero\Oauth\Storage\SessionTokenInterface;

/**
 * Session token grant type class
 *
 * By implementing "ClientAssertionTypeInterface" client id/secret
 * enforcement and validation is moved to this class itself and should be
 * done in the "validateRequest" method. For simplicity sake when requesting
 * a token via session (through ajax) no client id/secret is required.
 */
class SessionToken implements GrantTypeInterface, ClientAssertionTypeInterface
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
	private $userInfo = array();

	/**
	 * Define identifier for this type of grant
	 * 
	 * @return  string  identifier
	 */
	public function getQuerystringIdentifier()
	{
		return 'session';
	}

	/**
	 * Constructor
	 * 
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Config array
	 * @return  void
	 */
	public function __construct(SessionTokenInterface $storage, $config = array())
	{
		$this->storage = $storage;
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
		// check for session id
		if (!$sessionId = $this->storage->getSessionIdFromCookie())
		{
			$response->setError(401, 'session_authentication_invalid', 'Unable to find a valid session id.');
			return false;
		}

		// get user for session id
		if (!$userId = $this->storage->getUserIdFromSessionId($sessionId))
		{
			$response->setError(401, 'session_authentication_invalid', 'Unable to authenticate via active session.');
			return false;
		}

		// store our session & user id
		$this->userInfo = array(
			'user_id'    => $userId,
			'session_id' => $sessionId
		);
		return true;
	}

	/**
	 * Get client id
	 * 
	 * @return  null
	 */
	public function getClientId()
	{
		// load internal request client
		$client = $this->storage->getInternalRequestClient();

		// return client id
		return isset($client['client_id']) ? $client['client_id'] : null;
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
		return $accessToken->createAccessToken($client_id, $user_id, $scope, false);
	}
}