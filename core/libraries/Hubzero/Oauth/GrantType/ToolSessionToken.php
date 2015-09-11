<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Oauth\GrantType;

use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\ClientAssertionType\ClientAssertionTypeInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use Hubzero\Oauth\Storage\ToolSessionTokenInterface;

/**
 * Tool session token grant type class
 *
 * By implementing "ClientAssertionTypeInterface" client id/secret
 * enforcement and validation is moved to this class itself and should be
 * done in the "validateRequest" method. For simplicity sake when requesting
 * a token via tool session no client id/secret is required.
 */
class ToolSessionToken implements GrantTypeInterface, ClientAssertionTypeInterface
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
		return 'tool';
	}

	/**
	 * Constructor
	 * 
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Config array
	 * @return  void
	 */
	public function __construct(ToolSessionTokenInterface $storage, $config = array())
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
		// make sure we have tool session data
		if (!$toolData = $this->storage->getToolSessionDataFromRequest($request))
		{
			$response->setError(401, 'tool_session_authentication_invalid', 'Unable to find valid tool session data.');
			return false;
		}

		// validate tool session data
		if (!$userId = $this->storage->validateToolSessionData($toolData['toolSessionId'], $toolData['toolSessionToken']))
		{
			$response->setError(401, 'tool_session_authentication_invalid', 'Unable to find valid tool session data.');
			return false;
		}

		// store user info locally
		$this->userInfo = [
			'user_id' => $userId,
			'scope'   => ''
		];
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