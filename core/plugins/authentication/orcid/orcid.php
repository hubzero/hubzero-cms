<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Orcid\Profile;
use Orcid\Oauth;

class plgAuthenticationOrcid extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Perform logout
	 *
	 * @return  void
	 */
	public function logout()
	{
		// Not supported by ORCID
	}

	/**
	 * Check login status of current user with regards to ORCID
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		// Not supported by ORCID
	}

	/**
	 * Method to call when redirected back from ORCID after authentication
	 * Grab the return URL if set and handle denial of app privileges from ORCID
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$b64dreturn = '';

		// Check the state for our return variable
		if ($return = Request::getString('state', ''))
		{
			$b64dreturn = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		// If we have a code coming back, the user has authorized our app, and we can authenticate
		if (!Request::getString('code'))
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				Lang::txt('PLG_AUTHENTICATION_ORCID_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Sets up ORCID params and redirects to ORCID authorize URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up the config for the ORCID api instance
		$oauth = new Oauth;
		$oauth->setClientId($this->params->get('client_id'))
		      ->setScope('/authenticate')
		      ->setState($view->return)
		      ->showLogin()
		      ->setRedirectUri(self::getRedirectUri('orcid'));

		// If we're linking an account, set any info that we might already know
		if (!User::isGuest())
		{
			$oauth->setEmail(User::get('email'));
			$oauth->setFamilyNames(User::get('surname'));
			$oauth->setGivenNames(User::get('givenName'));
		}

		// Create and follow the authorization URL
		App::redirect($oauth->getAuthorizationUrl());
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// Set up the config for the ORCID api instance
		$oauth = new Oauth;
		$oauth->setClientId($this->params->get('client_id'))
		      ->setClientSecret($this->params->get('client_secret'))
		      ->setRedirectUri(self::getRedirectUri('orcid'));

		// Authenticate the user
		$oauth->authenticate(Request::getString('code'));

		// Check for successful authentication
		if ($oauth->isAuthenticated())
		{
			$orcid = new Profile($oauth);

			// Set username to ORCID iD
			$username = $orcid->id();

			// Create the hubzero auth link
			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'orcid', null, $username);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_ORCID_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', $orcid->email() ? $orcid->email() : null);

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'orcid';
			$response->status    = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname  = $orcid->fullName();

			if ($hzal->user_id)
			{
				$user = User::getInstance($hzal->user_id);

				$response->username = $user->username;
				$response->email    = $user->email;
				$response->fullname = $user->name;
			}
			else
			{
				$response->username = '-' . $hzal->id;
				$response->email    = $response->username . '@invalid';

				// Also set a suggested username for their hub account
				Session::set('auth_link.tmp_username', str_replace(' ', '', strtolower($response->fullname)));
				Session::set('auth_link.tmp_orcid', $username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => null,
					'authenticator' => 'orcid'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_ORCID_AUTHENTICATION_FAILED');
		}
	}

	/**
	 * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function link($options=array())
	{
		// Set up the config for the ORCID api instance
		$oauth = new Oauth;
		$oauth->setClientId($this->params->get('client_id'))
		      ->setClientSecret($this->params->get('client_secret'))
		      ->setRedirectUri(self::getRedirectUri('orcid'));

		// If we have a code coming back, the user has authorized our app, and we can authenticate
		if (!Request::getString('code'))
		{
			// User didn't authorize our app, or, clicked cancel...
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_ORCID_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
				'error'
			);
		}

		// Authenticate the user
		$oauth->authenticate(Request::getString('code'));

		// Check for successful authentication
		if ($oauth->isAuthenticated())
		{
			$orcid = new Profile($oauth);

			// Set username to ORCID iD
			$username = $orcid->id();

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'orcid', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This orcid account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_ORCID_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				// Create the hubzero auth link
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'orcid', null, $username);
				// if `$hzal` === false, then either:
				//    the authenticator Domain couldn't be found,
				//    no username was provided,
				//    or the Link record failed to be created
				if ($hzal)
				{
					$hzal->set('user_id', User::get('id'));
					$hzal->set('email', $orcid->email());
					$hzal->update();
				}
				else
				{
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "orcid", null, %s) returned false', $username));
				}
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel...
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_ORCID_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Display login button
	 *
	 * @param   string  $return
	 * @return  string
	 */
	public static function onRenderOption($return = null)
	{
		Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/orcid/assets/css/orcid.css');

		$html = '<a class="orcid account" href="' . Route::url('index.php?option=com_users&view=login&authenticator=orcid' . $return) . '">';
			$html .= '<div class="signin">';
				$html .= Lang::txt('PLG_AUTHENTICATION_ORCID_SIGN_IN');
			$html .= '</div>';
		$html .= '</a>';

		return $html;
	}
}
