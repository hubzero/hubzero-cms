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

/**
 * Short description for 'OauthApiController'
 * 
 * Long description (if any) ...
 */
class OauthApiController extends Hubzero_Api_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function execute()
	{
		$segments = $this->getRouteSegments();
		$response = $this->getResponse();

		switch($segments[0])
		{
			case 'request_token':
				$this->request_token();
				break;
			case 'authorize':
				$this->authorize();
				break;
			case 'access_token':
				$this->access_token();
				break;
			case 'token_info':
				$this->token_info();
				break;
			default:
				$this->not_found();
				break;
		}
	}

	/**
	 * Short description for 'token_info'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function token_info()
	{
		$provider = $this->getProvider();
		$response = $this->getResponse();

		$response->setMessage($provider->getTokenData(),200,'OK');
	}

	/**
	 * Short description for 'not_found'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function not_found()
	{
		$response = $this->getResponse();

		$response->setErrorMessage(404,'Not Found');
	}

	/**
	 * Short description for 'request_token'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	private function request_token()
	{
		$provider = $this->getProvider();
		$response = $this->getResponse();

        $token = sha1(OAuthProvider::generateToken(20,false));
		$token_secret = sha1(OAuthProvider::generateToken(20,false));

		$db = JFactory::getDBO();

		$db->setQuery("INSERT INTO #__oauthp_tokens (consumer_id,user_id,state,token,token_secret,callback_url) " .
			"SELECT id, '0', '1', " . $db->Quote($token) . "," . $db->Quote($token_secret) . ", callback_url ".
			"FROM #__oauthp_consumers WHERE token=" . $db->Quote($provider->getConsumerKey()) . " LIMIT 1;");

		if (!$db->query())
		{
			$response->setErrorMessage(500,'Internal Server Error');
		}
		else
		{
			$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
			$response->setMessage("oauth_token=".$token."&oauth_token_secret=".$token_secret,200,'OK');
		}
	}

	/**
	 * Short description for 'authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	private function authorize()
	{
		$provider = $this->getProvider();
		$response = $this->getResponse();

		if (!isset($_REQUEST['oauth_token']))
		{
			$response->setErrorMessage(400,'Invalid Request');
			return;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'GET')
		{
			require 'authorize.html';
		}
		else if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$useraccount = Hubzero_User::getInstance($username);

			if ($useraccount->checkPassword($password))
			{
				// user grants application 'consumer_key' permission to act on their behalf
				// so record: user_id consumer_key accesstoken #acl

				$db->setQuery("SELECT access_token FROM #__user_accesstokens WHERE user_id=" . $db->Quote($useraccount->getUserId()) . " consumer_key=" . $db->Quote($this->_provider->consumer_key));

				$verifier = sha1(OAuthProvider::generateToken(20,false));

				$db = JFactory::getDBO();

				$db->setQuery("SELECT callback_url FROM #__oauthp_token WHERE type='2' AND state='1' AND token=" . $db->Quote($_REQUEST['oauth_token']) . ';' );

				$callback_url = $db->loadResult();

				if ($callback_url === false)
				{
					$this->setErrorMessage(500, "Internal Server Error");
					return false;
				}

				$db->setQuery('UPDATE #__oauthp_token SET verifier=' . $db->Quote($verifier) . "WHERE type='2' AND state='1' AND token=" . $db->Quote( $_REQUEST['oauth_token'] ) . ';');

				if (!$db->query())
				{
					$this->setErrorMessage(500, "Internal Server Error");
					return false;
				}

				if (!empty($callback_url))
				{
					$this->setErrorMessage($callback_url . "?oauth_token=" . $_REQUEST['oauth_token'] . "&oauth_verifier=" . $verifier,302, "Redirect");
				}

				return true;
			}

			$response->setErrorMessaege(400, "Invalid Request");
			return;
		}

		$this->setErrorMessage(500, "Internal Server Error");
	}

	/**
	 * Short description for 'access_token'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function access_token()
	{
		$provider = $this->getProvider();
		$response = $this->getResponse();

		$xauth_request = false;

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
		//
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
			$xauth_request = true;
		}

		if ($xauth_request)
		{
			if ($provider->getConsumerData()->xauth== '0')
			{
				$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$response->setErrorMessage('oauth_problem=permission_denied',401,'Unauthorized0');
				return;
			}

			if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
			{
				$response->setErrorMessage('SSL Required',403,'Forbidden');
				return;
			}

			if (isset($provider->x_auth_mode))
			{
				$x_auth_mode = $provider->x_auth_mode;
			}
			else if (isset($_POST['x_auth_mode']))
			{
				$x_auth_mode = $_POST['x_auth_mode'];
			}
			else if (isset($_GET['x_auth_mode']))
			{
				$x_auth_mode = $_GET['x_auth_mode'];
			}
			else
				$x_auth_mode = '';

			if (isset($provider->x_auth_username))
			{
				$x_auth_username = $provider->x_auth_username;
			}
			else if (isset($_POST['x_auth_username']))
			{
				$x_auth_username = $_POST['x_auth_username'];
			}
			else if (isset($_GET['x_auth_username']))
			{
				$x_auth_username = $_GET['x_auth_username'];
			}
			else
				$x_auth_username = '';

			if (isset($provider->x_auth_password))
			{
				$x_auth_password = $provider->x_auth_password;
			}
			else if (isset($_POST['x_auth_password']))
			{
				$x_auth_password = $_POST['x_auth_password'];
			}
			else if (isset($_GET['x_auth_password']))
			{
				$x_auth_password = $_GET['x_auth_password'];
			}
			else
				$x_auth_password = '';

			if ($x_auth_mode != 'client_auth')
			{
				$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$response->setErrorMessage('oauth_problem=permission_denied',401,'Unauthorized2');
				return;
			}

			$useraccount = Hubzero_User::getInstance($x_auth_username);

			if (!is_object($useraccount))
			{
				$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$response->setErrorMessage('oauth_problem=permission_denied',401,'Unauthorized3');
				return;
			}

			if (!$useraccount->comparePassword($x_auth_password))
			{
				$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$response->setErrorMessage('oauth_problem=permission_denied 3',401,'Unauthorized');
				return;
			}

			$db = JFactory::getDBO();

			$db->setQuery("SELECT token,token_secret FROM #__oauthp_tokens WHERE consumer_id="
				. $db->Quote($provider->getConsumerData()->id) . " AND user_id ="
				. $db->Quote($useraccount->getUserId()) . " LIMIT 1;");

			$result = $db->loadObject();

			if ($result === false)
			{
				$response->setErrorMessage(500, 'Internal Server Error');
				return;
			}

			if (!is_object($result))
			{
				if ($provider->getConsumerData()->xauth_grant < 1)
				{
					$response->setErrorMessage(501, 'Internal Server Error');
					return;
				}

        		$token = sha1(OAuthProvider::generateToken(20,false));
				$token_secret = sha1(OAuthProvider::generateToken(20,false));

				$db = JFactory::getDBO();

				$db->setQuery("INSERT INTO #__oauthp_tokens (consumer_id,user_id,state,token,token_secret,callback_url) VALUE ("
					. $db->Quote($provider->getConsumerData()->id) . ","
					. $db->Quote($useraccount->getUserId()) . ","
					. "'1',"
					. $db->Quote($token) . ","
					. $db->Quote($token_secret) . ","
					. $db->Quote($provider->getConsumerData()->callback_url)
					. ");");

				if (!$db->query())
				{
					$response->setErrorMessage(502, 'Internal Server Error');
					return;
				}

				if ($db->getAffectedRows() < 1)
				{
					$response->setErrorMessage(503, 'Internal Server Error');
					return;
				}

				$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$response->setMessage("oauth_token=".$token."&oauth_token_secret=".$token_secret);
			}
			else
			{
				$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$response->setMessage("oauth_token=".$result->token."&oauth_token_secret=".$result->token_secret);
			}

			return;
		}
		else
		{
			$response->setErrorMessage(503, 'Internal Server Error');
			return;

			// @FIXME: we don't support 3-legged auth yet
			// lookup request token to access token, give out access token
			// check verifier
			// check used flag
			$response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
			$response->setMessage("oauth_token=".$token."&oauth_token_secret=".$token_secret,200,"OK");
			return;
		}
	}
}