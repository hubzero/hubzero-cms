<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once('Provider/Globus.php');
require_once('Provider/GlobusResourceOwner.php');

class plgAuthenticationGlobus extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Stores the initialized Globus object.
	 *
	 * @var  object  Globus
	 */
	private $globus = null;

	/**
	 * Get the Globus object, instantiating it if need be
	 *
	 * @return  object
	 */
	protected function globus($redirect = null)
	{
		if (!$this->globus)
		{
			if (!$redirect)
			{
				$redirect = self::getRedirectUri('globus');
			}

			$this->globus = new \Globus\OAuth2\Client\Provider\Globus([
				'clientId' => $this->params->get('app_id'),
				'clientSecret' => $this->params->get('app_secret'),
				'redirectUri' => $redirect
			]);
		}

		return $this->globus;
	}

	/**
	 * Perform logout (not currently used)
	 *
	 * @return  void
	 */
	public function logout()
	{
		// This is handled by the JS API, and cannot be done server side
		// (at least, it cannot be done server side, given our authentication workflow
		// and the current limitations of the PHP SDK).
	}

	/**
	 * Method to call when redirected back from gc after authentication
	 * Grab the return URL if set and handle denial of app privileges from gc
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$b64dreturn = '';
		$return = '';
		$return = Session::get('returnUrl', null, 'globus');
		if (!empty($return))
		{
			$b64dreturn = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}
		Session::clear('state', 'returnUrl');
		$options['return'] = $b64dreturn;

		// Check to make sure they didn't deny our application permissions
		if (Request::getVar('error', null))
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				Lang::txt('PLG_AUTHENTICATION_GLOBUS_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}
	}

	public function status()
	{
		// Do nothing as of now
	}

	/**
	 * Method to setup Globus params and redirect to Globus auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		$returnUrl = Request::getString('return', '');
		$provider = $this->globus();
		$loginUrl = $provider->getAuthorizationUrl(array('scope' => ['openid', 'email', 'profile']));
		Session::set('state', $provider->getState(), 'globus');
		Session::set('returnUrl', $returnUrl, 'globus');
		// Redirect to the login URL
		App::redirect($loginUrl);
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
		try
		{
			$storedState = Session::get('state', null, 'globus');
			$state = Request::getVar('state');
			if (empty($state) || $storedState !== $state)
			{
				throw new Exception('Mismatched state');
			}
			Session::clear('state', 'globus');
			$token = $this->globus()->getAccessToken('authorization_code', array('code' => Request::getString('code')));
		}
		catch (\Exception $e)
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_GLOBUS_ERROR_RETRIEVING_PROFILE', $e->getMessage());
			return;
		}
		// Make sure we have a user_id (gc returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($token) && $token))
		{
			try
			{
				$globusResponse = $this->globus()->getResourceOwner($token);
				$id       = $globusResponse->getId();
				$firstname = $globusResponse->getGivenName();
				$lastname = $globusResponse->getFamilyName();
				$fullname = $globusResponse->getName();
				$email    = $globusResponse->getEmail();
				$fullname = empty($fullname) ? $firstname . ' ' . $lastname : $fullname;
			}
			catch (\Exception $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_GLOBUS_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}
			// Create the hubzero auth link
			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'globus', null, $id);
			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_GLOBUS_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', $email);

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'globus';
			$response->status    = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname  = $fullname;

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
				$sub_email    = explode('@', $email, 2);
				$tmp_username = $sub_email[0];
				App::get('session')->set('auth_link.tmp_username', $tmp_username);
			}
			$hzal->update();


			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'authenticator' => 'globus'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_GLOBUS_AUTHENTICATION_FAILED');
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
		try
		{
			$session = $this->globus()->getAccessToken('authorization_code', ['code' => Request::getString('code')]);
		}
		catch (\Exception $ex)
		{
			// When validation fails or other local issues
		}
		// Make sure we have a user_id (globus returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			try
			{
				$globusResponse = $this->globus()->getResourceOwner($session);
				$id       = $globusResponse->getId();
				$email    = $globusResponse->getEmail();
			}
			catch (\Exception $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_GLOBUS_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'globus', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $id))
			{
				// This globus account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_GLOBUS_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'globus', null, $id);
				// if `$hzal` === false, then either:
				//    the authenticator Domain couldn't be found,
				//    no username was provided,
				//    or the Link record failed to be created
				if ($hzal)
				{
					$hzal->set('user_id', User::get('id'));
					$hzal->set('email', $email);
					$hzal->update();
				}
				else
				{
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "globus", null, %s) returned false', $id));
				}
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_GLOBUS_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Generate return url
	 *
	 * @param   string  $return  url
	 * @param   bool    $encode  whether or not to encode return before using
	 * @return  string  url
	 */
	private static function getReturnUrl($return=null, $encode=false)
	{
		// Get the hub url
		$service = trim(Request::base(), '/');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		// Check if a return is specified
		$rtrn = '';
		if (isset($return) && !empty($return))
		{
			if ($encode)
			{
				$return = base64_encode($return);
			}
			$rtrn = '&return=' . $return;
		}

		return self::getRedirectUri('globus') . $rtrn;
	}
}
