<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once PATH_CORE . DS . 'libraries' . DS . 'CAS-1.3.3' . DS . 'CAS.php';

/**
 * Authentication Plugin class for PUCAS
 */
class plgAuthenticationPUCAS extends \Hubzero\Plugin\Plugin
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
	public function onUserLogout()
	{
		$this->initialize();

		$service = rtrim(Request::base(), '/');

		if (empty($service))
		{
			$service = $_SERVER['HTTP_HOST'];
		}

		$return = '';

		if ($return = Request::getString('return', ''))
		{
			$return = base64_decode($return);

			if (!\Hubzero\Utility\Uri::isInternal($return))
			{
				$return = '';
			}

			$return = '/' . ltrim($return, '/');
		}

		phpCAS::logout(array('service'=>$service . $return, 'url'=>$service . $return));
	}

	/**
	 * Check login status of current user with regards to Purdue CAS
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		$status = array();

		if (Config::Get('debug'))
		{
			$debug_location = $this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log');
			phpCAS::setDebug($debug_location);
		}

		$this->initialize();

		if (phpCAS::checkAuthentication())
		{
			$status['username'] = phpCAS::getUser();
		}

		return $status;
	}

	/**
	 * Actions to perform when logging in a user session
	 *
	 * @param   object  &$credentials
	 * @param   array   &$options
	 * @return  void
	 */
	public function login(&$credentials, &$options)
	{
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
	 * Method to setup Purdue CAS params and redirect to pucas auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	public function display($view, $tpl)
	{
		$this->initialize();

		$return = '';

		if ($view->return)
		{
			$return = '&return=' . $view->return;
		}

		phpCAS::setFixedServiceURL(self::getRedirectUri('pucas') . $return);

		phpCAS::forceAuthentication();

		App::redirect(self::getRedirectUri('pucas') . $return);
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
		$this->initialize();

		try
		{
			$authenticated = phpCAS::isAuthenticated();
		}
		catch (CAS_AuthenticationException $e)
		{
			throw new Exception(Lang::txt('PLG_AUTHENTICATION_PUCAS_ERROR_EXPIRED_TICKET'), 400);
		}

		$return = (isset($options['return'])) ? $options['return'] : '';
		if ($authenticated)
		{
			$username = phpCAS::getUser();

			$method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
			$hzal = \Hubzero\Auth\Link::$method('authentication', 'pucas', null, $username);

			if ($hzal === false)
			{
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_PUCAS_UNKNOWN_USER');
				return;
			}

			$hzal->set('email', $username . '@purdue.edu');

			$response->auth_link = $hzal;
			$response->type = 'pucas';
			$response->status = \Hubzero\Auth\Status::SUCCESS;

			$email = phpCAS::getAttribute('email');
			$name  = phpCAS::getAttribute('fullname');

			if (!empty($email))
			{
				$hzal->email = $email;
			}

			if (!empty($name))
			{
				$response->fullname = ucwords(strtolower($name));
			}

			if ($hzal->user_id)
			{
				$user = User::getInstance($hzal->user_id); // Bring this in line with the rest of the system

				$response->username = $user->get('username');
				$response->email    = $user->get('email');
				$response->fullname = $user->get('name');
			}
			else
			{
				// Check if an account with the same username and email address
				// already exists. If so, the user probably registered manually
				// rather than using the CAS plugin.
				$user = \Hubzero\User\User::all()
					->whereEquals('username', $username)
					->whereEquals('email', $username . '@purdue.edu')
					->row();

				if ($user->get('id'))
				{
					$response->username = $user->get('username');
					$response->email    = $user->get('email');
					$response->fullname = $user->get('name');

					$hzal->user_id = $user->get('id');
				}
				else
				{
					$response->username = '-' . $hzal->id; // The Open Group Base Specifications Issue 6, Section 3.426
					$response->email    = $response->username . '@invalid'; // RFC2606, section 2

					// Also set a suggested username for their hub account
					App::get('session')->set('auth_link.tmp_username', $username);
				}
			}

			$hzal->update();

			// Save extra data
			if ($this->params->get('profile_i2a2'))
			{
				$val = phpCAS::getAttribute('i2a2characteristics');

				$datum = Hubzero\Auth\Link\Data::oneByLinkAndKey($hzal->id, 'i2a2');
				$datum->set(array(
					'link_id'      => $hzal->id,
					'domain_key'   => 'i2a2',
					'domain_value' => (string)$val
				));
				$datum->save();
			}

			// If we have a real user, drop the authenticator cookie
			if (isset($user) && is_object($user))
			{
				// Set cookie with login preference info
				$prefs = array(
					'user_id'       => $user->get('id'),
					'user_img'      => $user->picture(0, false),
					'authenticator' => 'pucas'
				);
			}
			else
			{
				// A partially baked cookie when a new user account is created.
				$prefs = array(
					'authenticator' => 'pucas'
				);
			}

			$namespace = 'authenticator';
			$lifetime  = time() + 365*24*60*60;

			\Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
		}
		else
		{
			$response->status = \Hubzero\Auth\Status::FAILURE;
			$response->error_message = Lang::txt('PLG_AUTHENTICATION_PUCAS_AUTHENTICATION_FAILED');
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
		$this->initialize();

		if (phpCAS::isAuthenticated())
		{
			// Get unique username
			$username = phpCAS::getUser();

			$hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'pucas', '');

			// Create the link
			if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
			{
				// This purdue cas account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_PUCAS_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			else
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'pucas', null, $username);
				// if `$hzal` === false, then either:
				//    the authenticator Domain couldn't be found,
				//    no username was provided,
				//    or the Link record failed to be created
				if ($hzal)
				{
					$hzal->set('user_id', User::get('id'));
					$hzal->set('email', phpCAS::getAttribute('email'));
					$hzal->update();
				}
				else
				{
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "pucas", null, %s) returned false', $username));
				}
			}
		}
		else
		{
			// User somehow got redirect back without being authenticated (not sure how this would happen?)
			App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_PUCAS_ERROR_LINKING'),
				'error'
			);
		}
	}

	/**
	 * Initializes the PHP CAS client
	 *
	 * @return void
	 **/
	private function initialize()
	{ 
		if (!phpCAS::isInitialized())
		{
			if (Config::get('debug'))
			{
				$debug_location = trim($this->params->get('debug_location', '/var/log/apache2/php/phpCAS.log'));

				if ($debug_location)
				{
					phpCAS::setDebug($debug_location);
				}
			}

			phpCAS::client(CAS_VERSION_2_0, 'sso.purdue.edu', 443, '/idp/profile/cas', false);
			phpCAS::setCasServerCACert(__DIR__ . '/assets/PuCAS_CA.crt');
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
		Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/pucas/assets/css/pucas.css');

		$html = '<a class="pucas account" href="' . Route::url('index.php?option=com_users&view=login&authenticator=pucas' . $return) . '">';
			$html .= '<div class="signin">';
				$html .= Lang::txt('PLG_AUTHENTICATION_PUCAS_SIGN_IN');
			$html .= '</div>';
		$html .= '</a>';

		return $html;
	}

        /**
         * Builds the redirect URI based on the current URI and a few other assumptions
         *
         * @param   string  $name  The plugin name
         * @return  string
         **/
        protected static function getRedirectUri($name)
        {
                // Get the hub url
                $service = trim(\Request::base(), '/');

                $task = 'login';
                $option = 'login';

                if (\App::isSite())
                {
                        // Legacy support
                        if (\App::has('component') && \App::get('component')->isEnabled('com_users'))
                        {
                                // If someone is logged in already, then we're linking an account
                                $task   = (\User::isGuest()) ? 'user.login' : 'user.link';
                                $option = 'users';
                        }
                        else
                        {
                                $task   = (\User::isGuest()) ? 'login' : 'link';
                        }
                }

                $scope = '/index.php?option=com_' . $option . '&task=' . $task . '&authenticator=' . $name;

                return $service . $scope;
        }
}
