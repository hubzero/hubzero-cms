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
use Exception;

/**
 * Authentication class, provides an interface for the authentication system
 */
class Guard
{
	/**
	 * The application implementation.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	/**
	 * Finds out if a set of login credentials are valid by asking all observing
	 * objects to run their respective authentication routines.
	 *
	 * @param   array   $credentials  Array holding the user credentials.
	 * @param   array   $options      Array holding user options.
	 * @return  object  Response object with status variable filled in for last plugin or first successful plugin.
	 */
	public function authenticate($params = array())
	{
		$response = array();

		// If request has a Basic Auth header Oauth will throw an exception if the header doesn't
		// conform to the OAuth protocol. We catch that (or any other)  exception and proceed as 
		// if there was no oauth data.
		//
		// @TODO A better approach might be to inspect the Basic Auth header and see if it even
		// looks like OAuth was being attempted and throw an Oauth compliant error if it was.
		try
		{
			$oauthp = new \Hubzero\Oauth\Provider($params);

			$oauthp->setRequestTokenPath('/api/oauth/request_token');
			$oauthp->setAccessTokenPath('/api/oauth/access_token');
			$oauthp->setAuthorizePath('/api/oauth/authorize');

			$result = $oauthp->validateRequest($this->app['request']->current(true), $this->app['request']->method());

			if (is_array($result))
			{
				//$this->response->setResponseProvides('application/x-www-form-urlencoded');
				//$this->response->setMessage($result['message'], $result['status'], $result['reason']);
				$this->app['response']->setContent($result['message']);
				$this->app['response']->setStatusCode($result['status']);
				return false;
			}

			$this->app['provider'] = $oauthp;

			$response['oauth_token']  = $oauthp->getToken();
			$response['consumer_key'] = $oauthp->getConsumerKey();
		}
		catch (Exception $e)
		{
			$result = false;
		}

		$response['user_id'] = null;

		if (isset($response['oauth_token']) && $response['oauth_token'])
		{
			$data = $oauthp->getTokenData();

			if (!empty($data->user_id))
			{
				$response['user_id'] = $data->user_id;
			}

			$response['session_id'] = null;

			$this->app['session']->set('user', new \JUser($data->user_id));
		}
		// Well, let's try to authenticate it with a session instead
		else
		{
			$session_name = md5($this->app->hash('site'));
			$session_id = null;

			if (!empty($_COOKIE[$session_name]))
			{
				$session_id = $_COOKIE[$session_name];
			}

			$response['session_id'] = $session_id;
			$response['user_id'] = null;

			if (!empty($session_id))
			{
				$db = \JFactory::getDBO();
				$timeout = $this->app['config']->get('timeout');
				$query = "SELECT userid FROM `#__session` WHERE session_id=" . $db->Quote($session_id) . "AND time + " . (int) $timeout . " <= NOW() AND client_id = 0;";

				$db->setQuery($query);

				$user_id = $db->loadResult();

				if (!empty($user_id))
				{
					$response['user_id'] = $user_id;
				}
			}

			// tool session authentication
			$toolSessionId    = $this->app['request']->getInt('sessionnum', null, 'POST');
			$toolSessionToken = $this->app['request']->getCmd('sessiontoken', null, 'POST');

			// use request headers as backup method to post vars
			if (!$toolSessionId && !$toolSessionToken)
			{
				$headers          = apache_request_headers();
				$toolSessionId    = (isset($headers['sessionnum']))   ? $headers['sessionnum']   : null;
				$toolSessionToken = (isset($headers['sessiontoken'])) ? $headers['sessiontoken'] : null;
			}

			// if we have a session id & token lets use those to authenticate
			if ($toolSessionId && $toolSessionToken)
			{
				// include neede libs
				require_once PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php';

				// instantiate middleware database
				$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

				// attempt to load session from db
				$query = "SELECT * FROM `session` WHERE `sessnum`= " . $mwdb->quote($toolSessionId) . " AND `sesstoken`=" . $mwdb->quote($toolSessionToken);
				$mwdb->setQuery($query);

				// only continue if a valid session was found
				if ($session = $mwdb->loadObject())
				{
					// check users IP against the session execution host IP
					if ($this->app['request']->ip() == gethostbyname($session->exechost))
					{
						$profile = \Hubzero\User\Profile::getInstance($session->username);
						$response['user_id'] = $profile->get('uidNumber');
					}
				}
			}
		}

		if (!$this->app->has('provider'))
		{
			$this->app['provider'] = null;
		}

		return $response;
	}
}