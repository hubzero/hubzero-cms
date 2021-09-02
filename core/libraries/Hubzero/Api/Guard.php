<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api;

use Hubzero\Container\Container;
use Hubzero\Oauth\Server;
use Hubzero\Oauth\Storage\Mysql as MysqlStorage;
use Hubzero\Config\Registry;

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
         * Clear session variables while maintaining some internal
	 * session state.
	 *
	 * @return void
	 */
	private function clear_session()
	{
		// @TODO: Ultimately I don't think any of this is necessary
		// and will likely get removed in a future session refactoring
		// patch. We do this here just in case its needed to maintain
		// session integrity.

		$session_timer_start = Session::get('session.timer.start');
		$session_timer_last = Session::get('session.timer.last');
		$session_timer_now = Session::get('session.timer.now');
		$session_client_address = Session::get('session.client.address');
		$session_client_forwarded = Session::get('session.client.forwarded');
		$session_client_browser = Session::get('session.client.browser');
		$session_token = Session::get('session.token');
		$session_counter = Session::get('session.counter');

		session_unset();

		Session::set('session.timer.start', $session_timer_start);
		Session::set('session.timer.last', $session_timer_last);
		Session::set('session.timer.now', $session_timer_now);
		Session::set('session.client.address', $session_client_address);
		Session::set('session.client.forwarded', $session_client_forwarded);
		Session::set('session.client.browser', $session_client_browser);
		Session::set('session.token', $session_token);
		Session::set('session.counter', $session_counter);
	}


	/**
	 * Validates incoming request
	 *
	 * @param   array  $params   request parameters
	 * @param   array  $options  configuration options
	 * @return  array
	 */
	public function authenticate($params = array(), $options = array())
	{
		// Fire before auth event
		Event::trigger('before_auth');

		// Load oauth server
		$oauthServer   = new Server(new MysqlStorage, $options);
		$oauthRequest  = \OAuth2\Request::createFromGlobals();
		$oauthResponse = new \OAuth2\Response();

		if ( $oauthServer->verifyResourceRequest($oauthRequest, $oauthResponse) )
		{
			// This request was successfully authenticated by OAuth2

			// Store our token locally
			$this->token = $oauthServer->getAccessTokenData($oauthRequest);

			// See if we have a valid user
			if (isset($this->token['uidNumber']))
			{
				$user = User::oneOrNew($this->token['uidNumber']);

				if ($user->get('id'))
				{
					$user->set('guest', false);
				}
				else
				{
					// We got a user_id from the Oauth2 Token
					// But the user does not exist
					// so we have to fail the request

					$oauthResponse->setStatusCode(403);
					$oauthResponse->setParameter('error','invalid_token');
					$oauthResponse->setParameter('error_description','The access token refers to a non-existent user');
					$oauthResponse->setParameter('code', $oauthResponse->getStatusCode());
					$oauthResponse->send();
					exit();
				}
			}
			else
			{
				// Well we may be authenticated by OAuth2 but
				// we are missing TokenData so we have to fail
				// the request here

				$oauthResponse->setStatusCode(403);
				$oauthResponse->setParameter('error','invalid_token');
				$oauthResponse->setParameter('error_description', 'The access token is missing user identification data');
				$oauthResponse->setParameter('code', $oauthResponse->getStatusCode());
				$oauthResponse->send();
				exit();
			}
		}
		else if ($oauthResponse->getParameters('error') != array())
		{
			// This request looked like OAuth2 but failed. We respond
			// immediately with an error here because the client was expecting
			// an authenticated response so returning a guest response would
			// be unexpected.

			$oauthResponse->setParameter('code', $oauthResponse->getStatusCode());
			$oauthResponse->send();
			exit();
		}
		else
		{
			// This request is authenticated by session data
			// If this is a guest session we pass it through as such.
			// It is up to the API call to check authorization.

			$user = User::getInstance();
		}

		$userid = $user->get('id');

		// Clear session data, API calls should not be using any session state data
		$this->clear_session();

		Session::set('registry', new Registry('session'));
		Session::set('user', $user);

		// Fire after auth event
		Event::trigger('after_auth');

		// Return the user_id  (null == guest)
		return array( 'user_id' => $userid ? $userid : null );
	}
}
