<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Include LinkedIn php library
require_once __DIR__ . DS . 'simplelinkedin-php' . DS . 'linkedin_3.2.0.class.php';

class plgAuthenticationLinkedIn extends \Hubzero\Plugin\OauthClient
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

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
	 * Check login status of current user with regards to linkedin
	 *
	 * @return  array $status
	 */
	public function status()
	{
		$js = "$(document).ready(function() {
					$.getScript('https://platform.linkedin.com/in.js?async=true', function success() {
						onLinkedInLoad = function () {
							if (IN.User.isAuthorized()) {
								IN.API.Profile('me').result(function(profile) {
									var linkedin = $('#linkedin').siblings('.sign-out');
									linkedin
										.find('.current-user')
										.html(profile.values[0].firstName+' '+profile.values[0].lastName);

									linkedin.on('click', function( e ) {
										e.preventDefault();
										IN.User.logout(function() {
											linkedin.animate({'margin-top': -42}, function() {
												linkedin.find('.current-user').html('');
											});
										});
									});
								});
							}
						}

						IN.init({
							api_key   : '{$this->params->get('api_key')}',
							onLoad    : 'onLinkedInLoad',
							authorize : true
						});
					});
				});";

		Document::addScriptDeclaration($js);
	}

	/**
	 * Method to call when redirected back from linkedin after authentication
	 * Grab the return URL if set and handle denial of app privileges from linkedin
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		$session    = App::get('session');
		$b64dreturn = '';

		// Check to see if a return parameter was specified
		if ($return = Request::getString('return', ''))
		{
			$b64dreturn = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($b64dreturn))
			{
				$b64dreturn = '';
			}
		}

		// Set the return variable
		$options['return'] = $b64dreturn;

		// Set up linkedin configuration
		$linkedin_config['appKey']      = $this->params->get('api_key');
		$linkedin_config['appSecret']   = $this->params->get('app_secret');
		$linkedin_config['callbackUrl'] = self::getRedirectUri('linkedin');

		// Create Object
		$linkedin_client = new LinkedIn($linkedin_config);

		if (!Request::getString('oauth_verifier'))
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return),
				Lang::txt('PLG_AUTHENTICATION_LINKEDIN_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
				'error'
			);
		}

		// LinkedIn has sent a response, user has granted permission, take the temp access token,
		// the user's secret and the verifier to request the user's real secret key
		$request = $session->get('linkedin.oauth.request');
		$reply = $linkedin_client->retrieveTokenAccess(
			$request['oauth_token'],
			$request['oauth_token_secret'],
			Request::getString('oauth_verifier')
		);
		if ($reply['success'] === true)
		{
			// The request went through without an error, gather user's 'access' tokens
			$session->set('linkedin.oauth.access', $reply['linkedin']);

			// Set the user as authorized for future quick reference
			$session->set('linkedin.oauth.authorized', true);
		}
		else
		{
			return new Exception(Lang::txt('PLG_AUTHENTICATION_LINKEDIN_ERROR'), 500);
		}
	}

	/**
	 * Method to setup linkedin params and redirect to linkedin auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		// Set up the redirect URL
		$return      = isset($view->return) ? '&return=' . $view->return : '';
		$redirect_to = self::getRedirectUri('linkedin') . $return;

		// User initiated LinkedIn connection, setup linkedin configuration
		$config = array(
			'callbackUrl' => $redirect_to . '&' . LINKEDIN::_GET_TYPE . '=initiate&' . LINKEDIN::_GET_RESPONSE . '=1',
			'appKey'      => $this->params->get('api_key'),
			'appSecret'   => $this->params->get('app_secret')
		);

		// Create linkedin object
		$client = new LinkedIn($config);

		// Check for a response from LinkedIn
		$_GET[LINKEDIN::_GET_RESPONSE] = (isset($_GET[LINKEDIN::_GET_RESPONSE])) ? $_GET[LINKEDIN::_GET_RESPONSE] : '';
		if (!$_GET[LINKEDIN::_GET_RESPONSE])
		{
			// LinkedIn hasn't sent us a response, the user is initiating the connection
			// Send a request for a LinkedIn access token
			$reply = $client->retrieveTokenRequest();
			if ($reply['success'] === true)
			{
				// Store the request token
				App::get('session')->set('linkedin.oauth.request', $reply['linkedin']);

				// Redirect the user to the LinkedIn authentication/authorization page to initiate validation
				App::redirect(LINKEDIN::_URL_AUTH . $reply['linkedin']['oauth_token']);
			}
			return;
		}

		// Are the already logged on?
		return new Exception(Lang::txt('PLG_AUTHENTICATION_LINKEDIN_ERROR'), 500);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
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
		// Make sure we have authorization
		$session = App::get('session');

		if ($session->get('linkedin.oauth.authorized') == true)
		{
			// User initiated LinkedIn connection, set up config
			$config = array(
				'appKey'      => $this->params->get('api_key'),
				'appSecret'   => $this->params->get('app_secret'),
				'callbackUrl' => self::getRedirectUri('linkedin')
			);

			// Create the object
			$linkedin_client = new LinkedIn($config);
			$linkedin_client->setTokenAccess($session->get('linkedin.oauth.access'));

			// Fields we need
			$retrieve = array(
				'id',
				'first-name',
				'last-name',
				'email-address'
			);
			// Extra fields we might want to collect
			$fields = array(
				'num-connections',
				'summary',
				'specialties',
				'public-profile-url',
				'industry',
				'location',
				'positions'
			);
			foreach ($fields as $field)
			{
				if ($this->params->get('profile_' . $field))
				{
					$retrieve[] = $field;
				}
			}

			// Get the linked in profile
			$profile = $linkedin_client->profile('~:(' . implode(',', $retrieve) . ',picture-urls::(original))');
			$profile = $profile['linkedin'];

			// Parse the profile XML
			$profile = new SimpleXMLElement($profile);

			// Get the profile values
			$li_id      = $profile->{'id'};
			$first_name = $profile->{'first-name'};
			$last_name  = $profile->{'last-name'};
			$full_name  = $first_name . ' ' . $last_name;
			$username   = (string) $li_id; // (make sure this is unique)

			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'linkedin', null, $username);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_LINKEDIN_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', (string) $profile->{'email-address'});

			// Set response variables
			$response->auth_link = $hzal;
			$response->type      = 'linkedin';
			$response->status    = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname  = $full_name;

			if ($hzal->user_id)
			{
				$user = User::getInstance($hzal->user_id);

				$response->username = $user->username;
				$response->email    = $user->email;
				$response->fullname = $user->name;
			}
			else
			{
				$response->username = '-'.$hzal->id;
				$response->email    = $response->username . '@invalid';

				// Also set a suggested username for their hub account
				$sub_email    = explode('@', (string) $profile->{'email-address'}, 2);
				$tmp_username = $sub_email[0];
				$session->set('auth_link.tmp_username', $tmp_username);
			}

			$hzal->update();

			// Save extra data
			foreach ($fields as $key)
			{
				$val = $profile->$key;

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
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => (string) $profile->{'picture-urls'}->{'picture-url'},
					'authenticator' => 'linkedin'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else // no authorization
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_LINKEDIN_AUTHENTICATION_FAILED');
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
		$session = App::get('session');

		// Set up linkedin configuration
		$linkedin_config['appKey']      = $this->params->get('api_key');
		$linkedin_config['appSecret']   = $this->params->get('app_secret');
		$linkedin_config['callbackUrl'] = self::getRedirectUri('linkedin');

		// Create Object
		$linkedin_client = new LinkedIn($linkedin_config);

		if (!Request::getString('oauth_verifier'))
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_LINKEDIN_MUST_AUTHORIZE_TO_LOGIN', App::get('sitename')),
				'error'
			);
		}

		// LinkedIn has sent a response, user has granted permission, take the temp access token,
		// the user's secret and the verifier to request the user's real secret key
		$request = $session->get('linkedin.oauth.request');
		$reply = $linkedin_client->retrieveTokenAccess(
			$request['oauth_token'],
			$request['oauth_token_secret'],
			Request::getString('oauth_verifier')
		);
		if ($reply['success'] === true)
		{
			// The request went through without an error, gather user's 'access' tokens
			$session->set('linkedin.oauth.access', $reply['linkedin']);

			// Set the user as authorized for future quick reference
			$session->set('linkedin.oauth.authorized', true);
		}
		else
		{
			return new Exception(Lang::txt('Access token retrieval failed'), 500);
		}

		if ($session->get('linkedin.oauth.authorized') == true)
		{
			$linkedin_client->setTokenAccess($session->get('linkedin.oauth.access'));

			// Get the linked in profile
			$profile = $linkedin_client->profile('~:(id,first-name,last-name,email-address)');
			$profile = $profile['linkedin'];

			// Parse the profile XML
			$profile = new SimpleXMLElement($profile);

			// Get the profile values
			$li_id      = $profile->{'id'};
			$username   = (string) $li_id; // (make sure this is unique)

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'linkedin', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This linkedin account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_LINKEDIN_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'linkedin', null, $username);
				// if `$hzal` === false, then either:
				//    the authenticator Domain couldn't be found,
				//    no username was provided,
				//    or the Link record failed to be created
				if ($hzal)
				{
					$hzal->set('user_id', User::get('id'));
					$hzal->set('email', (string) $profile->{'email-address'});
					$hzal->update();
				}
				else
				{
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "linkedin", null, %s) returned false', $username));
				}
			}
		}
		else // no authorization
		{
			// User didn't authorize our app, or, clicked cancel
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_LINKEDIN_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
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
		Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/linkedin/assets/css/linkedin.css');

		$html = '<a class="linkedin account" href="' . Route::url('index.php?option=com_users&view=login&authenticator=linkedin' . $return) . '">';
			$html .= '<div class="signin">';
				$html .= Lang::txt('PLG_AUTHENTICATION_LINKEDIN_SIGN_IN');
			$html .= '</div>';
		$html .= '</a>';

		return $html;
	}
}
