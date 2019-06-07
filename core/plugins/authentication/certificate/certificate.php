<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Auth plugin for certificate based authentication
 */
class plgAuthenticationCertificate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior.
	 * If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Actions to perform when logging out a user session
	 *
	 * @return  void
	 */
	public function logout()
	{
		// Nothing here...certificate authentication only relies on the default
		// HUBzero session for session handling
	}

	/**
	 * Check login status of current user with regards to their client certificate
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		$status = array();

		if ($this->isAuthenticated())
		{
			$status['username'] = $_SERVER['SSL_CLIENT_S_DN_CN'];
		}

		return $status;
	}

	/**
	 * Actions to perform when logging in a user session
	 *
	 * @param   array  $credentials  login credentials
	 * @param   array  $options      login options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
		// Check for return param
		if ($return = Request::getString('return', ''))
		{
			$return = base64_decode($return);
			if (!\Hubzero\Utility\Uri::isInternal($return))
			{
				$return = '';
			}
		}

		$options['return'] = $return;
	}

	/**
	 * Method to setup and redirect to certificate auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		$return = '';
		if ($view->return)
		{
			$return = '&return=' . $view->return;
		}

		// Get the hub url
		$service = trim(Request::base(), DS);

		if (substr($service, -13) == 'administrator')
		{
			$scope = '/administrator/index.php?option=com_users&view=logintask=login&authenticator=certificate';
		}
		else
		{
			// If someone is logged in already, then we're linking an account
			$task  = (User::isGuest()) ? 'user.login' : 'user.link';
			$scope = '/index.php?option=com_users&task=' . $task . '&authenticator=certificate';
		}

		App::redirect($scope . $return);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array   $credentials  the user credentials
	 * @param   array   $options      any extra options
	 * @param   object  $response     authentication response object
	 * @return  void
	 * @deprecated  1.3.1
	 */
	public function onAuthenticate($credentials, $options, &$response)
	{
		return $this->onUserAuthenticate($credentials, $options, $response);
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array   $credentials  the user credentials
	 * @param   array   $options      any extra options
	 * @param   object  $response     authentication response object
	 * @return  void
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		// Check for the required subject dn field
		if (isset($_SERVER['SSL_CLIENT_S_DN']) && $_SERVER['SSL_CLIENT_S_DN'])
		{
			$domain   = $_SERVER['SSL_CLIENT_I_DN_CN'];
			$username = $_SERVER['SSL_CLIENT_S_DN_CN'];

			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal   = \Hubzero\Auth\Link::$method('authentication', 'certificate', $domain, $username);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_CERTIFICATE_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', $_SERVER['SSL_CLIENT_S_DN_Email']);

			$response->auth_link = $hzal;
			$response->type      = 'certificate';
			$response->status    = \Hubzero\Auth\Status::SUCCESS;
			$response->fullname  = $username;

			// Try to deduce fullname from potential patern (ex: LAST.FIRST.MIDDLE.ID)
			if (preg_match('/([[:alpha:]]*)\.([[:alpha:]]*)\.([[:alpha:]]*)/', $username, $matches))
			{
				$response->fullname = ucfirst($matches[2]) . ' ' . ucfirst($matches[3]) . ' ' . ucfirst($matches[1]);
			}

			if ($hzal->user_id)
			{
				$user = User::getInstance($hzal->user_id);

				$response->username = $user->get('username');
				$response->email    = $user->get('email');
				$response->fullname = $user->get('name');
			}
			else
			{
				$response->username = '-' . $hzal->id;
				$response->email    = $response->username . '@invalid';

				// Also set a suggested username for their hub account
				App::get('session')->set('auth_link.tmp_username', $username);
			}

			$hzal->update();

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => $user->picture(0, false),
					'authenticator' => 'certificate'
				);

				$namespace = 'authenticator';
				$lifetime  = time() + 365*24*60*60;

				\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
			}
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_CERTIFICATE_AUTHENTICATION_FAILED');
		}
	}

	/**
	 * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
	 *
	 * @param   array  $options  additional options
	 * @return  void
	 */
	public function link($options=array())
	{
		// Check for the required subject dn field
		if ($this->isAuthenticated())
		{
			$domain   = $_SERVER['SSL_CLIENT_I_DN_CN'];
			$username = $_SERVER['SSL_CLIENT_S_DN_CN'];

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'certificate', $domain);

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This certificate account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_CERTIFICATE_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'certificate', $domain, $username);
				// if `$hzal` === false, then either:
				//    the authenticator Domain couldn't be found,
				//    no username was provided,
				//    or the Link record failed to be created
				if ($hzal)
				{
					$hzal->set('user_id', User::get('id'));
					$hzal->set('email', $_SERVER['SSL_CLIENT_S_DN_Email']);
					$hzal->update();
				}
				else
				{
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "certificate", %s, %s) returned false', $domain, $username));
				}
			}
		}
		else
		{
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_CERTIFICATE_ERROR_LINKING_CERT'),
				'error'
			);
		}
	}

	/**
	 * Encapsulates auth check for internal plugin use
	 *
	 * @return  bool
	 */
	private function isAuthenticated()
	{
		return (isset($_SERVER['SSL_CLIENT_S_DN']) && $_SERVER['SSL_CLIENT_S_DN']);
	}

	/**
	 * Display login button
	 *
	 * @param   string  $return
	 * @return  string
	 */
	public static function onRenderOption($return = null)
	{
		Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/certificate/assets/css/certificate.css');

		$html = '<a class="certificate account" href="' . Route::url('index.php?option=com_users&view=login&authenticator=certificate' . $return) . '">';
			$html .= '<div class="signin">';
				$html .= Lang::txt('PLG_AUTHENTICATION_CERTIFICATE_SIGN_IN');
			$html .= '</div>';
		$html .= '</a>';

		return $html;
	}
}
