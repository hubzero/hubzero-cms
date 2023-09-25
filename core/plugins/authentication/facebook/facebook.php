<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

class plgAuthenticationFacebook extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Stores the initialized OAuth2 provider object.
	 *
	 * @var  object  Oauth2\Client\Provider
	 */
	private $provider = null;

	/**
	 * Stores the session scope key for session variables
	 *
	 * @var  string	Session scope key to store our session variables
	 */
	private $name = 'facebook';

	/**
	 * Get the provider object, instantiating it if need be
	 *
	 * @return  object
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		$this->provider = new \League\OAuth2\Client\Provider\Facebook ([
			'clientId' => $this->params->get('app_id'),
			'clientSecret' => $this->params->get('app_secret'),
			'redirectUri' => $this->getReturnUrl(),
			'graphApiVersion'   => 'v2.10',
		]);
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
	 * Check login status of current user with regards to the provider
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		// Get the hub url
		$service    = trim(Request::base(), '/');
		$channelUrl = $service . '/channel.phtml';

		// This can only currently be done using the Facebook JS API
		// (at least relying solely on the native methods provided by the language's specific API)
		$js = "$(document).ready(function () {
			$('body').append('<div id=\"fb-root\"></div>');
			$.ajaxSetup({ cache: true });
			$.getScript('//connect.facebook.net/en_US/all.js', function () {
				window.fbAsyncInit = function () {
					FB.init({
						appId: '{$this->params->get('app_id')}',
						channelUrl: '{$channelUrl}'
					});

					FB.getLoginStatus(function ( response ) {
						if (response.status === 'connected') {
							FB.api('/me', function ( response ) {
								var facebook = $('#facebook').siblings('.sign-out');
								facebook.find('.current-user').html(response.name);

								facebook.on('click', function( e ) {
									e.preventDefault();
									FB.logout(function() {
										facebook.animate({'margin-top': -42}, function() {
											facebook.find('.current-user').html('');
										});
									});
								});
							});
						}
					});
				};
			});
		});";

		Document::addScriptDeclaration($js);
	}

	/**
	 * Method to call when redirected back from provider after authentication
	 * Grab the return URL if set and handle denial of app privileges from provider
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$return = '';
		$b64dreturn = '';

		if ($return = Session::get('returnUrl', null, $this->name))
		{
			$b64dreturn = base64_decode($return);

			if (!\Hubzero\Utility\Uri::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

		Session::clear('returnUrl', $this->name);

		// Check to make sure they didn't deny our application permissions
		if (Request::getVar('error', null))
		{
			// User didn't authorize our app or clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				Lang::txt('PLG_AUTHENTICATION_FACEBOOK_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}
	}

	/**
	 * Method to setup provider params and redirect to provider auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up params for the login call
		$params = array(
			'scope' => ['email'],
			'redirect_uri' => $this->getReturnUrl()
		);

		$loginUrl = $this->provider->getAuthorizationUrl($params);

		Session::set('oauth2state', $this->provider->getState(), $this->name);
		Session::set('returnUrl', $view->return, $this->name);

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
		$code = Request::getVar('code', null);
		$state = Request::getVar('state', null);

		if ($code == null)
		{
			$authUrl = $this->provider->getAuthorizationUrl(array('scope' => ['email']));

			Session::set('oauth2state', $this->provider->getState(), $this->name);

			App::redirect($authUrl);
		}
		elseif ($state !== Session::get('oauth2state',null,$this->name))
		{
			Session::clear('oauth2state',$this->name);

			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', 'Mismatched state');

			return;
		}

		$token = $this->provider->getAccessToken('authorization_code', array('code' => Request::getString('code')));

		Session::clear('oauth2state',$this->name);

		// Make sure we have a user_id (provider returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($token) && $token))
		{
			try
			{	
				// We got an access token, let's now get the user's details
				$owner = $this->provider->getResourceOwner($token);

				$id        = $owner->getId();
				$firstname = $owner->getFirstName();
				$lastname  = $owner->getLastName();
				$fullname  = $owner->getName();
				$email     = $owner->getEmail();
				$minage    = $owner->getMinAge();
				$maxage    = $owner->getMaxAge();
				$timezone  = $owner->getTimezone();
				$link      = $owner->getLink();
				$locale    = $owner->getLocale();
				$gender    = $owner->getGender();
				$fullname  = empty($fullname) ? $firstname . ' ' . $lastname : $fullname;
			}
			catch (\Exception $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			// Create the hubzero auth link
			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', $this->name, null, $id);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', $email);

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = $this->name;
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
				Session::set('auth_link.tmp_username', $tmp_username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => $owner->getPictureUrl(),
					'authenticator' => $this->name,
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_AUTHENTICATION_FAILED');
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
		$code = Request::getVar('code', null);
		$state = Request::getVar('state', null);

		if ($code == null)
		{
			$authUrl = $this->provider->getAuthorizationUrl(array('scope' => ['email']));

			Session::set('oauth2state', $this->provider->getState(), $this->name);

			App::redirect($authUrl);
		}
		elseif ($state !== Session::get('oauth2state',null,$this->name))
		{
			Session::clear('oauth2state',$this->name);

			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', 'Mismatched state');

			return;
		}

		$token = $this->provider->getAccessToken('authorization_code', array('code' => Request::getString('code')));

		Session::clear('oauth2state',$this->name);

		// Make sure we have a user_id (provider returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($token) && $token))
		{
			try
			{
				$owner = $this->provider->getResourceOwner($token);
				$id       = $owner->getId();
				$email    = $owner->getEmail();
			}
			catch (\Exception $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', $this->name, '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $id))
			{
				// This account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', $this->name, null, $id);
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
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", ' . $this->name . ', null, %s) returned false', $id));
				}
			}
		}
		else
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_FACEBOOK_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
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
	private function getReturnUrl($return=null, $encode=false)
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

		return self::getRedirectUri($this->name) . $rtrn;
	}

	/**
	 * Display login button
	 *
	 * @param   string  $return
	 * @return  string
	 */
	public static function onRenderOption($return = null)
	{
		Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/facebook/assets/css/facebook.css');

		$html = '<a class="facebook account" href="' . Route::url('index.php?option=com_users&view=login&authenticator=facebook' . $return) . '">';
			$html .= '<div class="signin">';
				$html .= Lang::txt('PLG_AUTHENTICATION_FACEBOOK_SIGN_IN');
			$html .= '</div>';
		$html .= '</a>';

		return $html;
	}
}
