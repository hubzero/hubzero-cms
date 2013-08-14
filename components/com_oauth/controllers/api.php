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

JLoader::import('Hubzero.Api.Controller');

/**
 * Short description for 'OauthApiController'
 * 
 * Long description (if any) ...
 */
class OauthControllerApi extends Hubzero_Api_Controller
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
		switch($this->segments[0])
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
			case 'consumer_request_test':
				$this->consumer_request_test();
				break;
			case 'unsigned_request_test':
				$this->unsigned_request_test();
				break;
			default:
				$this->not_found();
				break;
		}
	}
	
	private function consumer_request_test()
	{
		if (empty($this->_request->validApiKey))
		{
			$this->setMessage('', 401, 'No API Key');
			return;
		}
		
		$this->setMessage('Consumer Request Test OK', 200, 'OK');
	}

	private function unsigned_request_test()
	{
		$this->setMessage('Unsigned Request Test OK', 200, 'OK');
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
		$this->_response->setMessage($this->_provider->getTokenData(),200,'OK');
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
		$this->_response->setErrorMessage(404,'Not Found');
	}

	/**
	 *  Token Request Endpoint
	 * 
	 *  The client obtains a set of token credentials from the server by
	 *  making an authenticated (RFC5849 Section 3) HTTP "POST" request to 
	 *  the Token Request endpoint
	 * 
	 * @return     void
	 */
	private function request_token()
	{
		if (empty($this->_provider))
		{
			$this->setMessage('', 400, 'Bad Request');
			return;
		}
		
		$callback_url = JRequest::getVar('oauth_callback','');
		
        $token = sha1(OAuthProvider::generateToken(20,false));
        $token_secret = sha1(OAuthProvider::generateToken(20,false));
        $verifier = sha1(OAuthProvider::generateToken(20,false));
        
		$db = JFactory::getDBO();
		
		$consumer_data = $this->_provider->getConsumerData();

		if (empty($consumer_data))
		{
			$this->_response->setErrorMessage(500,'Internal Server Error');
			return;
		}
		
		if ((empty($callback_url)) || ($callback_url == 'oob'))
		{
			$callback_url = $consumer_data->callback_url;
		}
		
		$db->setQuery("INSERT INTO #__oauthp_tokens (consumer_id,user_id,state,token,token_secret,callback_url,verifier,created) VALUES (" .
			$db->Quote($consumer_data->id) . 
			", '0', '1', " . 
			$db->Quote($token) . "," . 
			$db->Quote($token_secret) . ", " .
			$db->Quote($callback_url) . ", " .
			$db->Quote($verifier) .
			", NOW());"); 
		
		if (!$db->query())
		{
			$this->_response->setErrorMessage(500,'Internal Server Error');
		}
		else
		{
			$this->setMessageType('application/x-www-form-urlencoded,text/plain;q=0.9');
			$this->setMessage("oauth_token=".$token."&oauth_token_secret=".$token_secret."&oauth_callback_confirmed=true",200,'OK');
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
		jimport('joomla.environment.request');
		
		$oauth_token = JRequest::getVar('oauth_token');
		
		if (empty($oauth_token))
		{
			$this->view->setLayout('notoken');
		}
			
		$db = JFactory::getDBO();
			
		$db->setQuery("SELECT * FROM #__oauthp_tokens WHERE token="	.
				$db->Quote($oauth_token) .
				" AND user_id=0 LIMIT 1;");
			
		$result = $db->loadObject();
			
		if ($result === false)
		{
			$this->view->setLayout('internalerror');
		}
			
		if (empty($result))
		{
			$this->view->setLayout('invalidtoken');
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'GET')
		{
			jimport('joomla.application.component.view');
			
			$this->view = new JView(array(
					'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_oauth',
					'name' => 'authorize',
					'layout' => 'authorize'));
				
			
			$this->view->oauth_token = $oauth_token;
			$this->view->form_action = '/api/oauth/authorize';	
			$this->view->display();
			return;
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$username = JRequest::get('username');
			$password = JRequest::get('password');
				
			if (true)
			{
				// user grants application 'consumer_key' permission to act on their behalf
				// so record: user_id consumer_key accesstoken #acl

				// $db->setQuery("SELECT access_token FROM #__user_accesstokens WHERE user_id=" . $db->Quote($useraccount->getUserId()) . " consumer_key=" . $db->Quote($this->_provider->consumer_key));

				

				if (!empty($result->callback_url))
				{
					$this->_response->setErrorMessage(302,"Redirect"."");
					$this->_response->addHeader('Location: '. $result->callback_url . "?oauth_token=" . $_REQUEST['oauth_token'] . "&oauth_verifier=" . $result->verifier,true);
				}

				return true;
			}

			$this->_response->setErrorMessaege(400, "Invalid Request");
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
		if (empty($this->_provider))
		{
			$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
			$this->_response->setErrorMessage('oauth_problem=bad oauth provider',501,'Internal Server Error');
			return;
		}

		JLoader::import('Hubzero.User.Password');
		
		$xauth_request = false;

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
		//
		if (isset($_GET['x_auth_mode'])
			|| isset($_GET['x_auth_username'])
			|| isset($_GET['x_auth_password'])
			|| isset($_POST['x_auth_mode'])
			|| isset($_POST['x_auth_username'])
			|| isset($_POST['x_auth_password'])
			|| (strpos($header,'x_auth_mode') !== false)
			|| (strpos($header,'x_auth_username') !== false)
			|| (strpos($header,'x_auth_mode') !== false))
		{
			$xauth_request = true;
		}

		if ($xauth_request)
		{
			if ($this->_provider->getConsumerData()->xauth== '0')
			{
				$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$this->_response->setErrorMessage('oauth_problem=permission_denied',401,'Unauthorized0');
				return;
			}

			if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
			{
				$this->_response->setErrorMessage('SSL Required',403,'Forbidden');
				return;
			}

			if (isset($this->_provider->x_auth_mode))
			{
				$x_auth_mode = $this->_provider->x_auth_mode;
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

			if (isset($this->_provider->x_auth_username))
			{
				$x_auth_username = $this->_provider->x_auth_username;
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

			if (isset($this->_provider->x_auth_password))
			{
				$x_auth_password = $this->_provider->x_auth_password;
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
				$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$this->_response->setErrorMessage('oauth_problem=permission_denied',400,'Bad Request');
				return;
			}
			
			$match = Hubzero_User_Password::passwordMatches($x_auth_username, $x_auth_password, true);			
			
			if (!$match)
			{
				$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$this->_response->setErrorMessage('oauth_problem=permission_denied',401,'Unauthorized');
				return;
			}

			$useraccount = JFactory::getUser(JUserHelper::getUserId($x_auth_username));
			
			$db = JFactory::getDBO();

			$db->setQuery("SELECT token,token_secret FROM #__oauthp_tokens WHERE consumer_id="
				. $db->Quote($this->_provider->getConsumerData()->id) . " AND user_id ="
				. $db->Quote($useraccount->get('id')) . " LIMIT 1;");

			$result = $db->loadObject();

			if ($result === false)
			{
				$this->_response->setErrorMessage(500, 'Internal Server Error');
				return;
			}

			if (!is_object($result))
			{
				if ($this->_provider->getConsumerData()->xauth_grant < 1)
				{
					$this->_response->setErrorMessage(501, 'Internal Server Error');
					return;
				}

				$token = sha1(OAuthProvider::generateToken(20,false));
				$token_secret = sha1(OAuthProvider::generateToken(20,false));

				$db = JFactory::getDBO();

				$db->setQuery("INSERT INTO #__oauthp_tokens (consumer_id,user_id,state,token,token_secret,callback_url) VALUE ("
					. $db->Quote($this->_provider->getConsumerData()->id) . ","
					. $db->Quote($useraccount->get('id')) . ","
					. "'1',"
					. $db->Quote($token) . ","
					. $db->Quote($token_secret) . ","
					. $db->Quote($this->_provider->getConsumerData()->callback_url)
					. ");");

				if (!$db->query())
				{
					$this->_response->setErrorMessage(502, 'Internal Server Error');
					return;
				}

				if ($db->getAffectedRows() < 1)
				{
					$this->_response->setErrorMessage(503, 'Internal Server Error');
					return;
				}

				$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$this->_response->setMessage("oauth_token=".$token."&oauth_token_secret=".$token_secret);
			}
			else
			{
				$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
				$this->_response->setMessage("oauth_token=".$result->token."&oauth_token_secret=".$result->token_secret);
			}

			return;
		}
		else
		{
			$this->_response->setErrorMessage(503, 'Internal Server Error');
			return;

			// @FIXME: we don't support 3-legged auth yet
			// lookup request token to access token, give out access token
			// check verifier
			// check used flag
			$this->_response->setResponseProvides('application/x-www-form-urlencoded,text/html;q=0.9');
			$this->_response->setMessage("oauth_token=".$token."&oauth_token_secret=".$token_secret,200,"OK");
			return;
		}
	}
}
