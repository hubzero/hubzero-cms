<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Oauth\GrantType;

use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\ClientAssertionType\ClientAssertionTypeInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\ResponseType\AccessTokenInterface;

/**
 * Client Credentials Grant Type
 */
class ClientCredentials implements GrantTypeInterface, ClientAssertionTypeInterface
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
	private $clientData;

	/**
	 * Constructor
	 * 
	 * @param   object  $storage  Storage object
	 * @param   array   $config   Config array
	 * @return  void
	 */
	public function __construct(ClientCredentialsInterface $storage, array $config = array())
	{
		$this->storage = $storage;
		$this->config  = array_merge(array(
			'allow_credentials_in_request_body' => true,
		), $config);

		// force public clients off
		$config['allow_public_clients'] = false;
	}

	/**
	 * Define identifier for this type of grant
	 * 
	 * @return  string  identifier
	 */
	public function getQuerystringIdentifier()
	{
		return 'client_credentials';
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
		// check HTTP basic auth headers for client id/secret
		if (!is_null($request->headers('PHP_AUTH_USER')) && !is_null($request->headers('PHP_AUTH_PW')))
		{
			$clientData = array(
				'client_id'     => $request->headers('PHP_AUTH_USER'),
				'client_secret' => $request->headers('PHP_AUTH_PW')
			);
		}

		// if we allow credentials via request body look there
		if ($this->config['allow_credentials_in_request_body'])
		{
			// check for client id in request
			if (!is_null($request->request('client_id')))
			{
				$clientData = array(
					'client_id'     => $request->request('client_id'),
					'client_secret' => $request->request('client_secret')
				);
			}
		}

		// must have client id
		if (!isset($clientData['client_id']) || $clientData['client_id'] == '')
		{
			$message = $this->config['allow_credentials_in_request_body'] ? ' or body' : '';
			$response->setError(400, 'invalid_client', 'Client credentials were not found in the headers'.$message);
			return false;
		}

		// check to see if we have client secret
		if (!isset($clientData['client_secret']) || $clientData['client_secret'] == '')
		{
			// invalid if we dont have client secret and public clients are off
			if (!$this->config['allow_public_clients'])
			{
				$response->setError(400, 'invalid_client', 'client credentials are required');
				return false;
			}

			// check storage if client is public client
			if (!$this->storage->isPublicClient($clientData['client_id']))
			{
				$response->setError(400, 'invalid_client', 'This client is invalid or must authenticate using a client secret');
				return false;
			}
		}
		// if we do have a secret lets verify them
		elseif ($this->storage->checkClientCredentials($clientData['client_id'], $clientData['client_secret']) === false)
		{
			$response->setError(400, 'invalid_client', 'The client credentials are invalid');
			return false;
		}

		// store data locally
		$this->clientData = $clientData;
		return true;
	}

	/**
	 * Get client id
	 * 
	 * @return  null
	 */
	public function getClientId()
	{
		return $this->clientData['client_id'];
	}

	/**
	 * Get user id
	 * 
	 * @return  int  User identifier
	 */
	public function getUserId()
	{
		return isset($this->clientData['user_id']) ? $this->clientData['user_id'] : null;
	}

	/**
	 * Get scope
	 * 
	 * @return  string  Scope
	 */
	public function getScope()
	{
		return isset($this->clientData['scope']) ? $this->clientData['scope'] : null;
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
		// DONT CREATE REFRESH TOKEN
		return $accessToken->createAccessToken($client_id, $user_id, $scope, false);
	}
}