<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * Description for '_provider'
	 *
	 * @var mixed
	 */
	private $_provider = null;

	/**
	 * Description for '_consumer_data'
	 *
	 * @var unknown
	 */
	private $_consumer_data = null;

	/**
	 * Description for '_token_data'
	 *
	 * @var unknown
	 */
	private $_token_data = null;

	/**
	 * Description for '_request_token_path'
	 *
	 * @var unknown
	 */
	private $_request_token_path = null;

	/**
	 * Description for '_access_token_path'
	 *
	 * @var unknown
	 */
	private $_access_token_path = null;

	/**
	 * Description for '_authorize_path'
	 *
	 * @var unknown
	 */
	private $_authorize_path = null;

	/**
	 * Short description for 'setRequestTokenPath'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $path Parameter description (if any) ...
	 * @return     void
	 */
	function setRequestTokenPath($path)
	{
		$this->_request_token_path = trim($path,'/');
	}

	/**
	 * Short description for 'setAccessTokenPath'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $path Parameter description (if any) ...
	 * @return     void
	 */
	function setAccessTokenPath($path)
	{
		$this->_access_token_path = trim($path,'/');
	}

	/**
	 * Short description for 'setAuthorizePath'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $path Parameter description (if any) ...
	 * @return     void
	 */
	function setAuthorizePath($path)
	{
		$this->_authorize_path = trim($path,'/');
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function __construct($params = array())
	{
		$this->_provider = new OAuthProvider($params);

		$this->_provider->consumerHandler(array($this,'consumerHandler'));
		$this->_provider->timestampNonceHandler(array($this,'timestampNonceHandler'));
		$this->_provider->tokenHandler(array($this, 'tokenHandler'));
	}

	// @FIXME: validateRequest() is still a bit awkward and needs to be refactored

	/**
	 * Short description for 'validateRequest'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $uri Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	function validateRequest($uri = null, $method = null)
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
		$result['status'] = $status;
		$result['reason'] = $reason;

		return $result;
	}

	/**
	 * Short description for 'getToken'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	function getToken()
	{
		return $this->_provider->token;
	}

	/**
	 * Short description for 'getConsumerKey'
	 *
	 * Long description (if any) ...
	 *
	 * @return     mixed Return description (if any) ...
	 */
	function getConsumerKey()
	{
		return $this->_provider->consumer_key;
	}

	/**
	 * Short description for 'getConsumerData'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getConsumerData()
	{
		return $this->_consumer_data;
	}

	/**
	 * Short description for 'getTokenData'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function getTokenData()
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
	function consumerHandler()
	{
		$db = \JFactory::getDBO();

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery("SELECT * FROM #__oauthp_consumers WHERE token=" . $db->Quote($this->_provider->consumer_key) . " LIMIT 1;");

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
	function timestampNonceHandler()
	{
		$timediff = abs(time() - $this->_provider->timestamp);

		if ($timediff > 600)
		{
			return OAUTH_BAD_TIMESTAMP;
		}

		$db = \JFactory::getDBO();

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery("INSERT INTO #__oauthp_nonces (nonce,stamp,created) "
				. " VALUES (" .
				$db->Quote($this->_provider->nonce) .
				"," .
				$db->Quote($this->_provider->timestamp) .
				", UTC_TIMESTAMP());");

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
	function tokenHandler()
	{
		$db = \JFactory::getDBO();

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery("SELECT * FROM #__oauthp_tokens WHERE token="	. $db->Quote($this->_provider->token) . " LIMIT 1;");

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
