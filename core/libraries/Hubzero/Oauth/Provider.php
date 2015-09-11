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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth;

use OAuthProvider;
use OAuthException;

/**
 * OAuth provider class
 */
class Provider
{
	/**
	 * Provider
	 *
	 * @var  mixed
	 */
	private $_provider = null;

	/**
	 * Consumer data
	 *
	 * @var  string
	 */
	private $_consumer_data = null;

	/**
	 * Token data
	 *
	 * @var  string
	 */
	private $_token_data = null;

	/**
	 * Request token path
	 *
	 * @var  string
	 */
	private $_request_token_path = null;

	/**
	 * Access token path
	 *
	 * @var  string
	 */
	private $_access_token_path = null;

	/**
	 * Authorize path
	 *
	 * @var  string
	 */
	private $_authorize_path = null;

	/**
	 * Set request token path
	 *
	 * @param   string  $path
	 * @return  void
	 */
	public function setRequestTokenPath($path)
	{
		$this->_request_token_path = trim($path,'/');
	}

	/**
	 * Set access token path
	 *
	 * @param   string  $path
	 * @return  void
	 */
	public function setAccessTokenPath($path)
	{
		$this->_access_token_path = trim($path,'/');
	}

	/**
	 * Set authorize path
	 *
	 * @param   string  $path
	 * @return  void
	 */
	public function setAuthorizePath($path)
	{
		$this->_authorize_path = trim($path,'/');
	}

	/**
	 * Constructor
	 *
	 * @param   array  $params
	 * @return  void
	 */
	public function __construct($params = array())
	{
		if (!class_exists('OAuthProvider'))
		{
			throw new \Exception('OAuthProvider class not found.', 500);
		}

		$this->_provider = new OAuthProvider($params);

		$this->_provider->consumerHandler(array($this,'consumerHandler'));
		$this->_provider->timestampNonceHandler(array($this,'timestampNonceHandler'));
		$this->_provider->tokenHandler(array($this, 'tokenHandler'));
	}

	/**
	 * Validate a request
	 *
	 * @param   string   $uri
	 * @param   string   $method
	 * @return  boolean
	 */
	public function validateRequest($uri = null, $method = null)
	{
		$endpoint = false;

		if (is_null($uri))
		{
			$uri = "";
		}

		if (is_null($method))
		{
			$method = $_SERVER['REQUEST_METHOD'];
		}

		$parts = parse_url($uri);

		$path = trim($parts['path'],'/');

		if ($path == $this->_request_token_path)
		{
			$this->_provider->isRequestTokenEndpoint(true);
		}
		else if ($path == $this->_access_token_path)
		{
			$header = '';

			if (isset($_SERVER['HTTP_AUTHORIZATION']))
			{
				$header = $_SERVER['HTTP_AUTHORIZATION'];
			}

			// @FIXME: header check is inexact and could give false positives
			// @FIXME: pecl oauth provider doesn't handle x_auth in header
			// @FIXME: api application should convert xauth variables in
			//         header to form/query data as workaround
			// @FIXME: this code is here for future use if/when pecl oauth
			//         provider is fixed

			if (isset($_GET['x_auth_mode'])
				|| isset($_GET['x_auth_username'])
				|| isset($_GET['x_auth_password'])
				|| isset($_POST['x_auth_mode'])
				|| isset($_POST['x_auth_username'])
				|| isset($_POST['x_auth_password'])
				|| !strpos($header,'x_auth_mode')
				|| !strpos($header,'x_auth_username')
				|| !strpos($header,'x_auth_password'))
			{
				$this->_provider->is2LeggedEndpoint(true);
				//$this->_provider->addRequiredParameter ('x_auth_mode');
				//$this->_provider->addRequiredParameter ('x_auth_username');
				//$this->_provider->addRequiredParameter ('x_auth_password');
			}
		}

		try
		{
			$this->_provider->checkOAuthRequest($uri,$method);

			return true;
		}
		catch (OAuthException $E)
		{
		}

		// No attempt was made to sign this, let it pass as such
		if ( ($this->_provider->consumer_key === null)
			&& ($this->_provider->consumer_secret === null)
			&& ($this->_provider->nonce === null)
			&& ($this->_provider->token === null)
			&& ($this->_provider->token_secret === null)
			&& ($this->_provider->timestamp === null)
			&& ($this->_provider->version === null)
			&& ($this->_provider->signature_method === null)
			&& ($this->_provider->callback === null)
			&& (empty($this->_provider->signature))
		)
		{
			return true;
		}

		// request to authorize path can have token and callback params, but are unsigned
		if ($path == $this->_authorize_path)
		{
			if (($this->_provider->consumer_key === null)
				&& ($this->_provider->consumer_secret === null)
				&& ($this->_provider->nonce === null)
				&& ($this->_provider->token_secret === null)
				&& ($this->_provider->timestamp === null)
				&& ($this->_provider->version === null)
				&& ($this->_provider->signature_method === null)
				&& (empty($this->_provider->signature))
				)
			{
				return true;
			}
		}

		$message = OAuthProvider::reportProblem($E, false);

		// request signed without token is allowed to pass
		if ($message == "oauth_problem=token_rejected")
		{
			if ( ($this->_provider->consumer_key !== null)
				&& ($this->_provider->consumer_secret !== null)
				&& ($this->_provider->nonce !== null)
				&& (empty($this->_provider->token))
				&& (empty($this->_provider->token_secret))
				&& ($this->_provider->timestamp !== null)
				&& ($this->_provider->version !== null)
				&& ($this->_provider->signature_method !== null)
				&& (!empty($this->_provider->signature))
			)
			{
				return true;
			}
		}

		$status = 401;
		$reason = 'Unauthorized';

		if ($message == "oauth_problem=signature_method_rejected")
		{
			$reason = 'Bad Request';
			$status = 400;
		}
		else if (strpos($message,"oauth_problem=parameter_absent") !== false)
		{
			$reason = 'Bad Request';
			$status = 400;
		}
		else if ($message == "oauth_problem=unknown_problem&code=503")
		{
			$reason = 'Bad Request';
			$status = 400;
		}

		$result['message'] = $message;
		$result['status']  = $status;
		$result['reason']  = $reason;

		return $result;
	}

	/**
	 * Get token
	 *
	 * @return  string
	 */
	public function getToken()
	{
		return $this->_provider->token;
	}

	/**
	 * Get consumer key
	 *
	 * @return  string
	 */
	public function getConsumerKey()
	{
		return $this->_provider->consumer_key;
	}

	/**
	 * Get consumer data
	 *
	 * @return  string
	 */
	public function getConsumerData()
	{
		return $this->_consumer_data;
	}

	/**
	 * Get token data
	 *
	 * @return  string
	 */
	public function getTokenData()
	{
		return $this->_token_data;
	}

	/**
	 * OAuthProvider consumerHandler Callback
	 *
	 * Lookup requested consumer key secret
	 *
	 * Result is stored in OAuthProvider instance's consumer_secret property
	 * Consumer data record is stored in _consumer_data property
	 *
	 * @return  OAUTH_OK on success
	 * 		If consumer_key doesn't exist returns OAUTH_CONSUMER_KEY_UNKNOWN
	 * 		If consumer_key is expired or otherwise invalid returns OAUTH_CONSUMER_KEY_REFUSED
	 * 		If lookup process failed for some reason returns OAUTH_ERR_INTERNAL_ERROR
	 */
	public function consumerHandler()
	{
		$db = \App::get('db');

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery("SELECT * FROM `#__oauthp_consumers` WHERE token=" . $db->quote($this->_provider->consumer_key) . " LIMIT 1;");

		$result = $db->loadObject();

		if ($result === false)	// query failed
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		if (empty($result)) // key not found
		{
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		if ($result->state != 1) // key not in a valid state
		{
			return OAUTH_CONSUMER_KEY_REFUSED;
		}

		$this->_consumer_data = $result;
		$this->_provider->consumer_secret = $result->secret;

		return OAUTH_OK;
	}

	/**
	 * OAuthProvider timestampNonceHandler Callback
	 *
	 * Validate timestamp and nonce assocaited with OAuthProvider instance
	 *
	 * @return  OAUTH_OK on success
	 * 		If timestamp is invalid (expired) returns OAUTH_BAD_TIMESTAMP
	 * 		If nonce has been seen before returns OAUTH_BAD_NONCE
	 * 		If lookup process failed for some reason returns OAUTH_ERR_INTERNAL_ERROR
	 */
	public function timestampNonceHandler()
	{
		$timediff = abs(time() - $this->_provider->timestamp);

		if ($timediff > 600)
		{
			return OAUTH_BAD_TIMESTAMP;
		}

		$db = \App::get('db');

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery(
			"INSERT INTO `#__oauthp_nonces` (nonce,stamp,created) "
				. " VALUES (" .
				$db->quote($this->_provider->nonce) .
				"," .
				$db->quote($this->_provider->timestamp) .
				", UTC_TIMESTAMP());"
		);

		if (($db->query() === false) && ($db->getErrorNum() != 1062)) // duplicate row error ok (well expected anyway)
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		if ($db->getAffectedRows() < 1) // duplicate row error throws this error instead
		{
			return OAUTH_BAD_NONCE;
		}

		return OAUTH_OK;
	}

	/**
	 * OAuthProvider tokenHandler Callback
	 *
	 * Lookup token data associated with OAuthProvider instance
	 *
	 * If token is valid stores full token record in _token_data property
	 *
	 * @return  OAUTH_OK on success
	 * 		If token not found returns OAUTH_TOKEN_REJECTED
	 * 		If token has expired or is otherwise unusable returns OAUTH_TOKEN_REJECTED
	 * 		If request verifier doesn't match token's verifier returns OAUTH_VERIFIER_INVALID
	 * 		If lookup process failed for some reason returns OAUTH_ERR_INTERNAL_ERROR
	 */
	public function tokenHandler()
	{
		$db = \App::get('db');

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery("SELECT * FROM `#__oauthp_tokens` WHERE token=" . $db->quote($this->_provider->token) . " LIMIT 1;");

		$result = $db->loadObject();

		if ($result === false) // query failed
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		if (empty($result)) // token not found
		{
			return OAUTH_TOKEN_REJECTED;
		}

		if ($result->state != '1') // token not in a valid state
		{
			return OAUTH_TOKEN_REJECTED;
		}

		if ($result->user_id == '0') // check verifier on request tokens
		{
			if ($result->verifier != $this->_provider->verifier)
			{
				return OAUTH_VERIFIER_INVALID;
			}
		}

		$this->_token_data = $result;
		$this->_provider->token_secret = $result->token_secret;

		return OAUTH_OK;
	}
}
