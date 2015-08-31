<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Api;

use Hubzero\Container\Container;
use Hubzero\Oauth\Server;
use Hubzero\Oauth\Storage\Mysql as MysqlStorage;
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
			$this->app['session']->set('user', new \JUser($response['user_id']));
		}

		// Fire after auth event
		Event::trigger('after_auth');

		// Return the response
		return $response;
	}
}