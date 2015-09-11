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
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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