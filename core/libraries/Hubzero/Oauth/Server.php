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

namespace Hubzero\Oauth;

use JRegistry;
use ApiConfig;
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
	 * @var  null
	 */
	private $server = null;

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
		$config = array_merge(array(
			'enforce_state'                     => true,
			'access_lifetime'                   => 3600,
			'refresh_token_lifetime'            => 7200,
			'require_exact_redirect_uri'        => true,
			'allow_credentials_in_request_body' => true,
			'always_issue_new_refresh_token'    => true
		), $options);

		// available grant types
		$grantTypes = array(
			new UserCredentialsGrantType($storage, $config),
			new RefreshTokenGrantType($storage, $config),
			new AuthorizationCodeGrantType($storage, $config),
			new ClientCredentialsGrantType($storage, $config),
			new SessionTokenGrantType($storage, $config),
			new ToolSessionTokenGrantType($storage, $config)
		);

		// Pass a storage object or array of storage objects to the OAuth2 server class
		$this->server = new OAuth2Server($storage, $config, $grantTypes);
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
}