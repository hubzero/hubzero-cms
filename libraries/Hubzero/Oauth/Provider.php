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

// no direct access
defined('_JEXEC') or die('Restricted access');

class Hubzero_Oauth_Provider
{
	private $_provider = null;
	private $_consumer_data = null;
	private $_token_data = null;
	private $_response = null;
	private $_request_token_path = null;
	private $_access_token_path = null;
	private $_authorize_path = null;

	function setResponse($response)
	{
		$this->_response = $response;
	}

	function setRequestTokenPath($path)
	{
		$this->_request_token_path = trim($path,'/');
	}

	function setAccessTokenPath($path)
	{
		$this->_access_token_path = trim($path,'/');
	}

	function setAuthorizePath($path)
	{
		$this->_authorize_path = trim($path,'/');
	}

	function __construct()
	{
		$this->_provider = new OAuthProvider();
		$this->_provider->consumerHandler(array($this,'lookupConsumer'));
		$this->_provider->timestampNonceHandler(array($this,'timestampNonceChecker'));
		$this->_provider->tokenHandler(array($this, 'tokenHandler'));
	}

	// @FIXME: validateRequest() is still a bit awkward and needs to be refactored

	function validateRequest($uri = null)
	{
		$endpoint = false;

		if (is_null($uri))
		{
			$uri = $_SERVER['SCRIPT_URI'];
		}

		$parts = parse_url($uri);

		$path = trim($parts['path'],'/');

		if ($path == $this->_request_token_path)
		{
			$this->_provider->isRequestTokenEndpoint(true);
			$endpoint = true;
		}
		else if ($path == $this->_access_token_path)
		{
			$header = '';

			if (isset($_SERVER['HTTP_AUTHORIZATION']))
			{
				$header = $_SERVER['HTTP_AUTHORTIZATION'];
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
				|| !strpos($header,'x_auth_mode'))
			{
				$this->_provider->is2LeggedEndpoint(true);
				$this->_provider->addRequiredParameter ('x_auth_mode');
				$this->_provider->addRequiredParameter ('x_auth_username');
				$this->_provider->addRequiredParameter ('x_auth_password');
			}

			$endpoint = true;
		}

		$E = null;

		try
		{
			$this->_provider->checkOAuthRequest($uri);
		}
		catch (OAuthException $E)
		{
			$E;
		}

		if ($E === null)
		{
			return true;
		}

		// unsigned requests pass
		if (!$endpoint
			&& ($this->_provider->consumer_key === null)
			&& ($this->_provider->consumer_secret === null)
			&& ($this->_provider->nonce === null)
			&& ($this->_provider->token === null)
			&& ($this->_provider->token_secret === null)
			&& ($this->_provider->timestamp === null)
			&& ($this->_provider->version === null)
			&& ($this->_provider->signature_method === null)
			&& ($this->_provider->callback === null)
			)
		{
			return true;
		}

		if ($path == $this->_authorize_path)
		{
			// request to authorize path can have token and callback params, but are unsigned
			if (($this->_provider->consumer_key === null)
				&& ($this->_provider->consumer_secret === null)
				&& ($this->_provider->nonce === null)
				&& ($this->_provider->token_secret === null)
				&& ($this->_provider->timestamp === null)
				&& ($this->_provider->version === null)
				&& ($this->_provider->signature_method === null)
				&& ($this->_provider->callback === null)
				)
			{
				return true;
			}
		}

		$status = 401;
		$reason = 'Unauthorized';

		$message = OAuthProvider::reportProblem($E, false);

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

		$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
		$this->_response->setMessage($message,$status,$reason);

		return false;
	}

	function getToken()
	{
		return $this->_provider->token;
	}

	function getConsumerKey()
	{
		return $this->_provider->consumer_key;
	}

	function getConsumerData()
	{
		return $this->_consumer_data;
	}

	function getTokenData()
	{
		return $this->_token_data;
	}

 	function lookupConsumer()
	{
		$db = JFactory::getDBO();

		if (!is_object($db))
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		$db->setQuery("SELECT * FROM #__oauthp_consumers WHERE token="
			. $db->Quote($this->_provider->consumer_key) . " LIMIT 1;");

		$result = $db->loadObject();

		if ($result === false)
		{
			return OAUTH_ERR_INTERNAL_ERROR;
		}

		if (empty($result))
		{
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		if ($result->state != 1)
		{
			return OAUTH_CONSUMER_KEY_REFUSED;
		}

		$this->_consumer_data = $result;
		$this->_provider->consumer_secret = $result->secret;

		return OAUTH_OK;
	}

	function timestampNonceChecker()
	{
		$timediff = abs(time() - $this->_provider->timestamp);

		if ($timediff > 600)
		{
			return OAUTH_BAD_TIMESTAMP;
		}

		$db = JFactory::getDBO();

		if (!is_object($db))
		{
			return 500;
		}

		$db->setQuery("INSERT INTO #__oauthp_nonces (nonce,stamp,created) "
				. " VALUES (" . $db->Quote($this->_provider->nonce) . ","
				. $db->Quote($this->_provider->timestamp) . ", NOW());");

		if (($db->query() === false) && ($db->getErrorNum() != 1062))
		{
			return 550;
		}

		if ($db->getAffectedRows() < 1)
		{
			return OAUTH_BAD_NONCE;
		}

		return OAUTH_OK;
	}

	function tokenHandler()
	{
		$db = JFactory::getDBO();

		if (!is_object($db))
		{
			return 500;
		}

		$db->setQuery("SELECT * FROM #__oauthp_tokens WHERE token="
			. $db->Quote($this->_provider->token) . ";");

		$result = $db->loadObject();

		if ($result === false)
		{
			return 500;
		}

		if (empty($result))
		{
			return OAUTH_TOKEN_REJECTED;
		}

		if ($result->state != '1')
		{
			return OAUTH_TOKEN_REJECTED;
		}

		if ($result->user_id == '0') // check verifier on non-access tokens
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
