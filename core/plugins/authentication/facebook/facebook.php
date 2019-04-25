<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
	 * Stores the initialized Facebook object.
	 *
	 * @var  object  Facebook
	 */
	private $facebook = null;

	/**
	 * Get the Facebook object, instantiating it if need be
	 *
	 * @return  object
	 */
	protected function facebook()
	{
		if (is_null($this->facebook))
		{
			$this->facebook = new \Facebook\Facebook([
				'app_id' => $this->params->get('app_id'),
				'app_secret' => $this->params->get('app_secret'),
				'default_graph_version' => $this->params->get('graph_version')
			]);
		}

		return $this->facebook;
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
	 * Check login status of current user with regards to facebook
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
	 * Method to call when redirected back from facebook after authentication
	 * Grab the return URL if set and handle denial of app privileges from facebook
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$return = '';
		$b64dreturn = '';
		if ($return = Request::getString('return', ''))
		{
			$b64dreturn = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		$options['return'] = $b64dreturn;

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
	 * Method to setup facebook params and redirect to facebook auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up the config for the facebook sdk instance
		$config = array(
			'appId'      => $this->params->get('app_id'),
			'secret'     => $this->params->get('app_secret'),
			'fileUpload' => false
		);

		// Set up params for the login call
		$params = array(
			'display'      => 'page',
			'redirect_uri' => self::getReturnUrl($view->return)
		);

		$loginUrl = $this->facebook()->getRedirectLoginHelper()->getLoginUrl($params['redirect_uri'], array('email'));

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
			$session = $this->facebook()->getRedirectLoginHelper()->getAccessToken();
		}
		catch (\Facebook\Exceptions\FacebookSDKException $ex)
		{
			// When Facebook returns an error
		}
		catch (\Exception $ex)
		{
			// When validation fails or other local issues
		}

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			try
			{
				// Fields we need
				$retrieve = array(
					'email',
					'name'
				);
				// Extra fields we might want to collect
				$fields = array(
					'age_range',
					'gender',
					'locale',
					'link',
					'timezone',
					'verified',
					'updated_time'
				);
				foreach ($fields as $field)
				{
					if ($this->params->get('profile_' . $field))
					{
						$retrieve[] = $field;
					}
				}

				$this->facebook()->setDefaultAccessToken($session);
				$facebookResponse = $this->facebook()->get('/me?fields=' . implode(',', $retrieve));
				$user_profile = $facebookResponse->getGraphUser();

				$id       = $user_profile->getId();
				$fullname = $user_profile->getName();
				$email    = $user_profile->getEmail();

				// Version 5 of the Facebook SDK no longer allows retrival of username
				// $username = $user_profile->getField('username');
			}
			catch (\Facebook\Exceptions\FacebookResponseException $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			// Create the hubzero auth link
			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'facebook', null, $id);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', $email);

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'facebook';
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

			// Save extra data
			foreach ($fields as $key)
			{
				$val = $user_profile->getField($key);

				if (in_array($key, $retrieve) && $val)
				{
					$datum = Hubzero\Auth\Link\Data::oneByLinkAndKey($hzal->id, $key);
					$datum->set(array(
						'link_id'      => $hzal->id,
						'domain_key'   => (string)$key,
						'domain_value' => (string)$val
					));
					$datum->save();
				}
			}

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				$apiVersion = $this->facebook()->getDefaultGraphVersion();
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => 'https://graph.facebook.com/' . $apiVersion . '/' . $id . '/picture?type=normal',
					'authenticator' => 'facebook'
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
		try
		{
			$session = $this->facebook()->getRedirectLoginHelper()->getAccessToken();
		}
		catch (\Facebook\Exceptions\FacebookSDKException $ex)
		{
			// When Facebook returns an error
		}
		catch (\Exception $ex)
		{
			// When validation fails or other local issues
		}

		// Make sure we have a user_id (facebook returns 0 for a non-logged in user)
		if ((isset($user_id) && $user_id > 0) || (isset($session) && $session))
		{
			try
			{
				$this->facebook()->setDefaultAccessToken($session);
				$facebookResponse = $this->facebook()->get('/me');
				$user_profile = $facebookResponse->getGraphUser();
				$graph_node = $facebookResponse->getGraphNode();

				$id       = $user_profile->getId();
				$email    = $graph_node->getField('email');

			}
			catch (\Facebook\Exceptions\FacebookRequestException $e)
			{
				// Error message?
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ERROR_RETRIEVING_PROFILE', $e->getMessage());
				return;
			}

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'facebook', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $id))
			{
				// This facebook account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_FACEBOOK_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'facebook', null, $id);
				$hzal->set('user_id', User::get('id'));
				$hzal->set('email', $email);
				$hzal->update();
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

		return self::getRedirectUri('facebook') . $rtrn;
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
				$html .= Lang::txt('PLG_AUTHENICATION_FACEBOOK_SIGN_IN');
			$html .= '</div>';
		$html .= '</a>';

		return $html;
	}
}
