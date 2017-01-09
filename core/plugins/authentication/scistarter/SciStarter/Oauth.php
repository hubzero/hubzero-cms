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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace SciStarter;

use SciStarter\Http\Curl;
use Exception;

/**
 * SciStarter API Oauth class
 */
class Oauth
{
	/**
	 * API endpoint constants
	 **/
	const HOSTNAME  = 'scistarter.com';
	const AUTHORIZE = 'authorize';
	const TOKEN     = 'token';

	/**
	 * The http tranport object
	 *
	 * @var  object
	 **/
	private $http = null;

	/**
	 * The API environment
	 *
	 * @var  string
	 **/
	private $environment = '';

	/**
	 * The oauth client ID
	 *
	 * @var  string
	 **/
	private $clientId = null;

	/**
	 * The oauth client secret
	 *
	 * @var  string
	 **/
	private $clientSecret = null;

	/**
	 * The oauth request scope
	 *
	 * @var  string
	 **/
	private $scope = 'login';

	/**
	 * The oauth request state
	 *
	 * @var  string
	 **/
	private $state = null;

	/**
	 * The oauth redirect URI
	 *
	 * @var  string
	 **/
	private $redirectUri = null;

	/**
	 * The oauth access token
	 *
	 * @var  string
	 **/
	private $accessToken = null;

	/**
	 * Constructs a new instance
	 *
	 * @param   object  $http  a request tranport object to inject
	 * @return  void
	 * @uses    SciStarter\Http\Curl
	 **/
	public function __construct($http = null)
	{
		$this->http = $http ?: new Curl;
	}

	/**
	 * Sets the oauth instance to use the production environment
	 *
	 * @return  $this
	 **/
	public function useProductionEnvironment()
	{
		$this->environment = '';

		return $this;
	}

	/**
	 * Sets the oauth instance to use the sandbox environment
	 *
	 * @return  $this
	 **/
	public function useSandboxEnvironment()
	{
		$this->environment = 'sandbox';

		return $this;
	}

	/**
	 * Sets the client ID for future use
	 *
	 * @param   string  $clientId  the client id
	 * @return  $this
	 **/
	public function setClientId($clientId)
	{
		$this->clientId = $clientId;

		return $this;
	}

	/**
	 * Sets the client secret for future use
	 *
	 * @param   string  $clientSecret  the client secret
	 * @return  $this
	 **/
	public function setClientSecret($clientSecret)
	{
		$this->clientSecret = $clientSecret;

		return $this;
	}

	/**
	 * Sets the oauth scope
	 *
	 * This is the scope of the permissions you'll be requesting from the user.
	 * See SciStarter documentation for options and more details. Most likely
	 * this won't be any more than 'login'.
	 *
	 * @param   string  $scope  the request scope
	 * @return  $this
	 **/
	public function setScope($scope)
	{
		$this->scope = $scope;

		return $this;
	}

	/**
	 * Sets the oauth redirect URI
	 *
	 * This is where the user will come back to after their interaction
	 * with the SciStarter login/registration page
	 *
	 * @param   string  $redirectUri  the redirect uri
	 * @return  $this
	 **/
	public function setRedirectUri($redirectUri)
	{
		$this->redirectUri = $redirectUri;

		return $this;
	}

	/**
	 * Sets the oauth access token
	 *
	 * @param   string  $token  the access token to set
	 * @return  $this
	 **/
	public function setAccessToken($token)
	{
		$this->accessToken = $token;

		return $this;
	}

	/**
	 * Grabs the oauth access token
	 *
	 * @return  string
	 **/
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	/**
	 * Gets the authorization URL based on the instance parameters
	 *
	 * @return  string
	 **/
	public function getAuthorizationUrl()
	{
		if (!$this->redirectUri)
		{
			throw new Exception('Redirect URI is not set');
		}
		if (!$this->scope)
		{
			throw new Exception('Scope is required');
		}
		if (!$this->clientId)
		{
			throw new Exception('Client ID is not set');
		}

		$url  = 'https://';
		$url .= (!empty($this->environment)) ? $this->environment . '.' : '';
		$url .= self::HOSTNAME . '/' . self::AUTHORIZE;
		$url .= '?client_id='    . $this->clientId;
		$url .= '&scope='        . $this->scope;
		$url .= '&redirect_uri=' . urlencode($this->redirectUri);
		$url .= '&response_type=code';

		return $url;
	}

	/**
	 * Takes the given code and requests an auth token
	 *
	 * @param   string  $code  the oauth code needed to request the access token
	 * @return  $this
	 * @throws  Exception
	 **/
	public function authenticate($code)
	{
		// Check for required items
		if (!$this->clientId)
		{
			throw new Exception('Client ID is required');
		}
		if (!$this->clientSecret)
		{
			throw new Exception('Client secret is required');
		}
		if (!$this->redirectUri)
		{
			throw new Exception('Redirect URI is required');
		}

		$url  = 'https://';
		$url .= (!empty($this->environment)) ? $this->environment . '.' : '';
		$url .= self::HOSTNAME . '/' . self::TOKEN . '?key=' . $this->clientSecret;

		$fields = [
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'code'          => $code,
			'redirect_uri'  => urlencode($this->redirectUri),
			'grant_type'    => 'authorization_code'
		];

		$this->http->setUrl($url)
				   ->setPostFields($fields)
				   ->setHeader(['Accept' => 'application/json']);

		$data = json_decode($this->http->execute());

		if (isset($data->access_token))
		{
			$this->setAccessToken($data->access_token);
		}
		else
		{
			// Seems like the response format changes on occasion... not sure what's going on there?
			$error = (isset($data->error)) ? $data->error : 'unknown error';

			throw new Exception($error);
		}

		return $this;
	}

	/**
	 * Checks for access token to indicate authentication
	 *
	 * @return  bool
	 **/
	public function isAuthenticated()
	{
		return ($this->getAccessToken()) ? true : false;
	}

	/**
	 * Grabs the user's data
	 *
	 * You'll call this method after completing the proper oauth exchange.
	 *
	 * @return  object
	 * @throws  Exception
	 **/
	public function getUserData()
	{
		$url  = 'https://';
		$url .= (!empty($this->environment)) ? $this->environment . '.' : '';
		$url .= self::HOSTNAME;

		$this->http->setUrl($url . '/api/user_data?access_token=' . $this->getAccessToken());

		// If using the members api, we have to have an access token set
		if (!$this->getAccessToken())
		{
			throw new Exception('You must first set an access token or authenticate');
		}

		$this->http->setHeader([
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $this->getAccessToken()
		]);

		$account = json_decode($this->http->execute());
		return $account->data;
	}
}
