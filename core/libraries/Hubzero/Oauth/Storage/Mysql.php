<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Oauth\Storage;

use Hubzero\User\User;
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
require_once PATH_CORE . DS . 'components' . DS . 'com_developer' . DS . 'models' . DS . 'application.php';

/**
 * Custom Hubzero OAuth2 Storage Class
 */
class Mysql
	implements AccessTokenInterface,
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
		$token = \Components\Developer\Models\Accesstoken::oneByToken($access_token);

		// make sure we have a token
		if (!$token->get('id'))
		{
			return false;
		}

		// make sure its a published token
		if (!$token->isPublished())
		{
			return false;
		}

		// get the application's client id
		$application = \Components\Developer\Models\Application::oneOrFail($token->get('application_id'));
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
		$model = new \Components\Developer\Models\Accesstoken();
		$model->set('application_id', $client['id']);
		$model->set('access_token', $access_token);
		$model->set('uidNumber', $user_id);
		$model->set('expires', $expires);
		$model->set('created', $created);
		return $model->save();
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
		$application = \Components\Developer\Models\Application::oneByClientid($clientId);

		// load application by client id
		if (!$application->get('id'))
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
	 * @return  array
	 */
	public function getClientDetailsById($id)
	{
		$database = \App::get('db');

		$sql = "SELECT * FROM `#__developer_applications`
				WHERE `id`=" . $database->quote($id);
		$database->setQuery($sql);
		return $database->loadAssoc();
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
		$authorizationCode = \Components\Developer\Models\Authorizationcode::oneByCode($code);

		// fetch by code
		if (!$authorizationCode->get('id'))
		{
			return false;
		}

		// get the application's client id
		$application = \Components\Developer\Models\Application::oneOrFail($authorizationCode->get('application_id'));
		$authorizationCode->set('client_id', $application->get('client_id'));

		// format expires to unix timestamp for authorization code grant type
		$authorizationCode->set('expires', with(new Date($authorizationCode->get('expires')))->toUnix());

		// return code
		return $authorizationCode->toArray();
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
		$model = new \Components\Developer\Models\Authorizationcode();
		$model->set('application_id', $client['id']);
		$model->set('authorization_code', $code);
		$model->set('uidNumber', $user_id);
		$model->set('redirect_uri', $redirect_uri);
		$model->set('expires', $expires);
		return $model->save();
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
		$authorizationCode = \Components\Developer\Models\Authorizationcode::oneByCode($code);

		// fetch by code
		if (!$authorizationCode->get('id'))
		{
			return false;
		}

		return $authorizationCode->destroy();
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
		$profile = \Hubzero\User\User::oneByUsername($username);

		if (!$profile->get('id'))
		{
			return false;
		}

		// return details as associative array
		return ['user_id' => $profile->get('id'), 'scope' => null];
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
		$result = \Hubzero\User\User::oneByEmail($enteredUsername);

		// no results or too many
		if (!$result || !$result->get('id'))
		{
			return null;
		}

		return $result->get('username');
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
		$token = \Components\Developer\Models\Refreshtoken::oneByToken($refresh_token);

		// make sure we have a token
		if (!$token->get('id'))
		{
			return false;
		}

		// make sure its a published token
		if (!$token->isPublished())
		{
			return false;
		}

		// get the application's client id
		$application = \Components\Developer\Models\Application::oneOrFail($token->get('application_id'));
		$token->set('client_id', $application->get('client_id'));

		// format expires to unix timestamp
		$token->set('expires', with(new Date($token->get('expires')))->toUnix());

		// return token
		return $token->toArray();
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
		$model = new \Components\Developer\Models\Refreshtoken();
		$model->set('application_id', $client['id']);
		$model->set('refresh_token', $refresh_token);
		$model->set('uidNumber', $user_id);
		$model->set('expires', $expires);
		$model->set('created', $created);
		return $model->save();
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
		$token = \Components\Developer\Models\RefreshToken::oneByToken($refresh_token);

		// make sure we have a token
		if (!$token->get('id'))
		{
			return false;
		}

		// delete token
		return $token->destroy();
	}

	/**
	 * Get session id from cookie
	 *
	 * [!] This will determine if the user has an active session via browser
	 *
	 * @return  mixed  Result of test
	 */
	public function getSessionIdFromCookie()
	{
		// get session id key name
		$client = 'site';
		if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'])
		{
			$referrer = $_SERVER['HTTP_REFERER'];
			if (\Hubzero\Utility\Uri::isInternal($referrer))
			{
				if (substr($referrer, 0, strlen('http')) == 'http')
				{
					$referrer = parse_url($referrer, PHP_URL_PATH);
				}
				$referrer = trim($referrer, '/');
				$parts = explode('/', $referrer);
				$referrer = array_shift($parts);
				if ($referrer == 'administrator')
				{
					$client = $referrer;
				}
			}
		}
		$sessionName = md5(\App::hash($client));

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
		$database = \App::get('db');

		// get session timeout period
		$timeout = \App::get('config')->get('timeout');

		// load user from session table
		$sql = "SELECT userid
				  FROM `#__session`
				  WHERE `session_id`=" . $database->quote($sessionId) . "
				  AND time + " . (int) $timeout . " <= NOW();";
				//  AND client_id = 0;";
		$database->setQuery($sql);
		return $database->loadResult();
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

		// return user id
		$profile = \Hubzero\User\User::oneByUsername($session->username);
		return $profile->get('id');
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
		$application = \Components\Developer\Models\Application::all()
			->whereEquals('hub_account', 1)
			->row();

		// make sure we have at least one
		// although it should always only be one
		if (!$application->get('id'))
		{
			return false;
		}

		// return first as an array
		return $application->toArray();
	}

	/**
	 * Create internal client
	 *
	 * @return  bool
	 */
	public function createInternalRequestClient()
	{
		// client id/secret
		$clientId     = md5(uniqid(\User::get('id'), true));
		$clientSecret = sha1($clientId);

		// application model
		$application = new \Components\Developer\Models\Application();
		$application->set('name', 'Hub Account');
		$application->set('description', 'Hub account for internal requests. DO NOT DELETE.');
		$application->set('redirect_uri', 'https://' . $_SERVER['HTTP_HOST']);
		$application->set('client_id', $clientId);
		$application->set('client_secret', $clientSecret);
		$application->set('grant_types', 'client_credentials session tool');
		$application->set('created', with(new Date('now'))->toSql());
		$application->set('created_by', \User::get('id'));
		$application->set('state', 1);
		$application->set('hub_account', 1);
		$application->save();

		return true;
	}
}
