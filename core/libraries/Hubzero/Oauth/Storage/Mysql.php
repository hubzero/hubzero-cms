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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth\Storage;

use Hubzero\User\Profile;
use Hubzero\User\Password;
use Hubzero\Utility\Date;
use OAuth2\RequestInterface;
use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\Storage\AuthorizationCodeInterface;
use OAuth2\Storage\RefreshTokenInterface;
use Hubzero\Oauth\Storage\SessionTokenInterface;
use Hubzero\Oauth\Storage\ToolSessionTokenInterface;

// include developer model
require_once PATH_CORE . DS . 'components' . DS . 'com_developer' . DS . 'models' . DS . 'developer.php';

/**
 * Custom Hubzero OAuth2 Storage Class
 */
class Mysql implements AccessTokenInterface,
			   ClientCredentialsInterface,
			   UserCredentialsInterface,
			   AuthorizationCodeInterface,
			   RefreshTokenInterface,
			   SessionTokenInterface,
			   ToolSessionTokenInterface
{
	/**
	 * Get Access token data
	 * 
	 * @param   string  $access_token  Access token
	 * @return  array   Access token data
	 */
	public function getAccessToken($access_token)
	{
		// create access token
		$model = new \Components\Developer\Models\Api\AccessToken();

		// make sure we have a token
		if (!$token = $model->loadByToken($access_token))
		{
			return false;
		}

		// make sure its a published token
		if (!$token->isPublished())
		{
			return false;
		}

		// get the application's client id
		$application = new \Components\Developer\Models\Api\Application($token->get('application_id'));
		$token->set('client_id', $application->get('client_id'));

		// format expires to unix timestamp
		$token->set('expires', with(new Date($token->get('expires')))->toUnix());

		// return token
		return $token->toArray(true);
	}

	/**
	 * Store access token data
	 * 
	 * @param   string  $access_token  Access token
	 * @param   string  $client_id     Client Id
	 * @param   string  $user_id       User identifier
	 * @param   string  $expires       Access token expiration date/time
	 * @param   string  $scope         Access token granted scope
	 * @return  void
	 */
	public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
	{
		// format date like HUBzero does
		$expires = with(new Date($expires))->toSql();
		$created = with(new Date('now'))->toSql();

		// get the id for th client
		$client = $this->getClientDetails($client_id);

		// create access token
		$model = new \Components\Developer\Models\Api\AccessToken();
		$model->set('application_id', $client['id']);
		$model->set('access_token', $access_token);
		$model->set('uidNumber', $user_id);
		$model->set('expires', $expires);
		$model->set('created', $created);
		return $model->store();
	}

	/**
	 * Get client details by client id
	 * 
	 * @param   string  $client_id  Load client details via client id.
	 * @return  void
	 */
	public function getClientDetails($clientId)
	{
		// create model
		$model = new \Components\Developer\Models\Api\Application();

		// load application by client id
		if (!$application = $model->loadByClientid($clientId))
		{
			return false;
		}

		// make sure its published
		if (!$application->isPublished())
		{
			return false;
		}

		// return as array
		return $application->toArray();
	}

	/**
	 * Get client details by id
	 * 
	 * @param   int   $id  Client mysql row auto-incrementing id
	 * @return  void
	 */
	public function getClientDetailsById($id)
	{
		die('client by id');

		$sql = "SELECT * FROM `#__developer_applications`
				WHERE `id`=" . $this->database->quote($id);
		$this->database->setQuery($sql);
		return $this->database->loadAssoc();
	}

	/**
	 * Get client scope
	 * 
	 * @param   string  $client_id  Client id
	 * @return  null
	 */
	public function getClientScope($client_id)
	{
		return null;
	}

	/**
	 * Check grant type against client id
	 * 
	 * @param   string  $client_id   Client id
	 * @param   string  $grant_type  Grant type
	 * @return  bool    Result of test
	 */
	public function checkRestrictedGrantType($client_id, $grant_type)
	{
		// get client details
		$client = $this->getClientDetails($client_id);

		// check to make sure grant type is acceptable for client
		if (isset($client['grant_types']))
		{
			$grant_types = explode(' ', $client['grant_types']);
			return in_array($grant_type, (array) $grant_types);
		}

		return true;
	}

	/**
	 * Verify client credentials
	 * 
	 * @param   string  $client_id      Client id
	 * @param   string  $client_secret  Client secret
	 * @return  bool    Result of test
	 */
	public function checkClientCredentials($client_id, $client_secret = null)
	{
		// load client
		if (!$client = $this->getClientDetails($client_id))
		{
			return false;
		}

		// make sure stored secret matches incoming
		if ($client['client_secret'] != $client_secret)
		{
			return false;
		}

		//passed
		return true;
	}

	/**
	 * Is client public
	 * 
	 * @param   integer  $client_id
	 * @return  boolean
	 */
	public function isPublicClient($client_id)
	{
		// get client details
		$client = $this->getClientDetails($client_id);

		// make sure its an available client (aka not deleted)
		return $client && $client['state'] != 2 ? true : false;
	}

	/**
	 * Get authorization code details by code
	 * 
	 * @param   string  $code  Authorization code
	 * @return  array   Code details
	 */
	public function getAuthorizationCode($code)
	{
		// auth model
		$model = new \Components\Developer\Models\Api\AuthorizationCode();

		// fetch by code
		if (!$authorizationCode = $model->loadByCode($code))
		{
			return false;
		}

		// get the application's client id
		$application = new \Components\Developer\Models\Api\Application($authorizationCode->get('application_id'));
		$authorizationCode->set('client_id', $application->get('client_id'));

		// format expires to unix timestamp for authorization code grant type
		$authorizationCode->set('expires', with(new Date($authorizationCode->get('expires')))->toUnix());

		// return code
		return $authorizationCode->toArray(true);
	}

	/**
	 * Create new authorization code
	 * 
	 * @param   string  $code          Authorization code
	 * @param   string  $client_id     Client id
	 * @param   int     $user_id       User identifier
	 * @param   string  $redirect_uri  Redirect URI after authorization
	 * @param   string  $expires       Code expiration
	 * @param   string  $scope         Code scope
	 * @return  bool
	 */
	public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null)
	{
		// format date like HUBzero does
		$expires = with(new Date($expires))->toSql();

		// get the id for th client
		$client = $this->getClientDetails($client_id);

		// create authorization code 
		$model = new \Components\Developer\Models\Api\AuthorizationCode();
		$model->set('application_id', $client['id']);
		$model->set('authorization_code', $code);
		$model->set('uidNumber', $user_id);
		$model->set('redirect_uri', $redirect_uri);
		$model->set('expires', $expires);
		return $model->store();
	}

	/**
	 * Remove invalid authorization code
	 * 
	 * @param   string  $code  Authorization code
	 * @return  void
	 */
	public function expireAuthorizationCode($code)
	{
		// auth model
		$model = new \Components\Developer\Models\Api\AuthorizationCode();

		// fetch by code
		if (!$authorizationCode = $model->loadByCode($code))
		{
			return false;
		}

		return $authorizationCode->delete();
	}

	/**
	 * Check user credentials
	 * 
	 * @param   string  $username  User's username
	 * @param   string  $password  User's password
	 * @return  bool    Result of username/password check
	 */
	public function checkUserCredentials($username, $password)
	{
		// allow authentication via email, just like in the hub
		if (strpos($username, '@'))
		{
			$username = $this->getUsernameFromEmail($username);
		}

		// use hubzero password library to compare stored password with sent password
		$match = Password::passwordMatches($username, $password, true);

		// return if match was found
		return (bool) $match;
	}

	/**
	 * Get user information
	 * 
	 * @param   string  $username  User's username
	 * @return  array   User info
	 */
	public function getUserDetails($username)
	{
		// load username from email
		if (strpos($username, '@'))
		{
			$username = $this->getUsernameFromEmail($username);
		}

		// load profile object, make sure its valid
		if (!$profile = Profile::getInstance($username))
		{
			return false;
		}

		// return details as associative array
		return ['user_id' => $profile->get('uidNumber'), 'scope' => NULL];
	}

	/**
	 * Get username from email address
	 * 
	 * @param   string  $enteredUsername  Email address
	 * @return  string  User's username
	 */
	private function getUsernameFromEmail($enteredUsername)
	{
		// get username from email
		$db = \App::get('db');
		$sql = "SELECT `id`, `username`, `password`
				FROM `#__users`
				WHERE `email`=" . $db->quote($enteredUsername);
		$db->setQuery($sql);
		$result = $db->loadObjectList();

		// no results or too many
		if (!$result || count($result) > 1)
		{
			return null;
		}

		return $result[0]->username;
	}

	/**
	 * Load refresh token details by token
	 * 
	 * @param   string  $refresh_token  Refresh token
	 * @return  array   Refresh token details
	 */
	public function getRefreshToken($refresh_token)
	{
		// create refresh token
		$model = new \Components\Developer\Models\Api\RefreshToken();

		// make sure we have a token
		if (!$token = $model->loadByToken($refresh_token))
		{
			return false;
		}

		// make sure its a published token
		if (!$token->isPublished())
		{
			return false;
		}

		// get the application's client id
		$application = new \Components\Developer\Models\Api\Application($token->get('application_id'));
		$token->set('client_id', $application->get('client_id'));

		// format expires to unix timestamp
		$token->set('expires', with(new Date($token->get('expires')))->toUnix());

		// return token
		return $token->toArray(true);
	}

	/**
	 * Create a refresh token
	 * 
	 * @param   string  $refresh_token  Refresh Token
	 * @param   string  $client_id      Client id
	 * @param   int     $user_id        User identifier
	 * @param   string  $expires        Expires timestamp
	 * @param   string  $scope          Token scope
	 * @return  void
	 */
	public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
	{
		// format date like HUBzero does
		$expires = with(new Date($expires))->toSql();
		$created = with(new Date('now'))->toSql();

		// get the id for th client
		$client = $this->getClientDetails($client_id);

		// create refresh token
		$model = new \Components\Developer\Models\Api\RefreshToken();
		$model->set('application_id', $client['id']);
		$model->set('refresh_token', $refresh_token);
		$model->set('uidNumber', $user_id);
		$model->set('expires', $expires);
		$model->set('created', $created);
		return $model->store();
	}

	/**
	 * Remove refresh token
	 * 
	 * @param   string  $refresh_token  Refresh Token
	 * @return  void
	 */
	public function unsetRefreshToken($refresh_token)
	{
		// create refresh token
		$model = new \Components\Developer\Models\Api\RefreshToken();

		// make sure we have a token
		if (!$token = $model->loadByToken($refresh_token))
		{
			return false;
		}

		// delete token
		return $token->delete();
	}

	/**
	 * Get session id from cookie
	 *
	 * [!] This will determine if the user has an active session via browser
	 * 
	 * @return  bool  Result of test
	 */
	public function getSessionIdFromCookie()
	{
		// get session id key name
		$sessionName = md5(\App::hash('site'));

		// return session id stored in cookie
		return (!empty($_COOKIE[$sessionName])) ? $_COOKIE[$sessionName] : null;
	}

	/**
	 * Get user id via session id
	 * 
	 * @param   string  $sessionId  Session identifier
	 * @return  int     User identifier
	 */
	public function getUserIdFromSessionId($sessionId)
	{
		$this->database = \App::get('db');

		// get session timeout period
		$timeout = \App::get('config')->get('timeout');

		// load user from session table
		$sql = "SELECT userid 
				  FROM `#__session`
				  WHERE `session_id`=" . $this->database->quote($sessionId) . "
				  AND time + " . (int) $timeout . " <= NOW()
				  AND client_id = 0;";
		$this->database->setQuery($sql);
		return $this->database->loadResult();
	}

	/**
	 * Get tool data from request
	 * 
	 * @return  bool  Result of test
	 */
	public function getToolSessionDataFromRequest(RequestInterface $request)
	{
		// get params via post vars
		$toolSessionId    = $request->request('sessionnum');
		$toolSessionToken = $request->request('sessiontoken');

		// use headers as backup method to post vars
		if (!$toolSessionId && !$toolSessionToken)
		{
			$toolSessionId    = $request->headers('sessionnum');
			$toolSessionToken = $request->headers('sessiontoken');
		}

		// return id & token
		return compact('toolSessionId', 'toolSessionToken');
	}

	/**
	 * Validate tool session data
	 * 
	 * @param   string  $toolSessionId     Tool session id
	 * @param   string  $toolSessionToken  Tool session token
	 * @return  bool    Result of test
	 */
	public function validateToolSessionData($toolSessionId, $toolSessionToken)
	{
		// include neede libs
		require_once PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php';

		// instantiate middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// attempt to load session from db
		$query = "SELECT * 
				  FROM `session`
				  WHERE `sessnum`= " . $mwdb->quote($toolSessionId) . " 
				  AND `sesstoken`=" . $mwdb->quote($toolSessionToken);
		$mwdb->setQuery($query);

		// only continue if a valid session was found
		if (!$session = $mwdb->loadObject())
		{
			return false;
		}

		$ip = \Hubzero\Utility\Ip(\App::get('request')->ip());
		// ip should be coming from a private address
		if (!$ip->isPrivate())
		{
			return false;
		}

		// return user id
		$profile = \Hubzero\User\Profile::getInstance($session->username);
		return $profile->get('uidNumber');
	}

	/**
	 * Get internal client
	 * 
	 * @return  mixed
	 */
	public function getInternalRequestClient()
	{
		// if we didnt find one lets make one
		if (!$client = $this->findInternalClient())
		{
			if ($this->createInternalRequestClient())
			{
				$client = $this->findInternalClient();
			}
		}

		// return client
		return ($client) ? $client : null;
	}

	/**
	 * Find Internal Client
	 *
	 * In separate function so we can call it multiple times.
	 * 
	 * @return  array  Client detials
	 */
	private function findInternalClient()
	{
		// create model and fetch applications matching fitlers
		$model = new \Components\Developer\Models\Developer();
		$applications = $model->applications('list', array(
			'hub_account' => 1,
			'limit'       => 1
		));

		// make sure we have at least one 
		// although it should always only be one
		if ($applications->count() < 1)
		{
			return false;
		}

		// return first as an array
		return $applications->first()->toArray();
	}

	/**
	 * Create internal client
	 * 
	 * @return  bool
	 */
	public function createInternalRequestClient()
	{
		// client id/secret
		$clientId     = md5(uniqid(\User::get('uidNumber'), true));
		$clientSecret = sha1($clientId);

		// application model
		$application = new \Components\Developer\Models\Api\Application();
		$application->set('name', 'Hub Account');
		$application->set('description', 'Hub account for internal requests. DO NOT DELETE.');
		$application->set('redirect_uri', 'https://' . $_SERVER['HTTP_HOST']);
		$application->set('client_id', $clientId);
		$application->set('client_secret', $clientSecret);
		$application->set('grant_types', 'client_credentials session tool');
		$application->set('created', with(new Date('now'))->toSql());
		$application->set('created_by', \User::get('uidNumber'));
		$application->set('state', 1);
		$application->set('hub_account', 1);
		$application->store();

		return true;
	}
}