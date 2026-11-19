<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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

	protected $stream_handler = null;
	protected $logger = null;

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

		if ($this->casAuthenticated())
		{
			phpCAS::logout(array('service'=>$service . $return, 'url'=>$service . $return));
		}
	}

	/**
	 * Check login status of current user with regards to Purdue CAS
	 *
	 * @return  array  $status
	 */
	public function status()
	{
		$status = array();

		$this->initialize();

		if ($this->casAuthenticated())
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

			$hzal = $this->findLink($username, $options);

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
				$user = self::matchingAccount($username);

				if ($user)
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


			$characteristics = phpCAS::getAttribute('i2a2characteristics');

			$chars = explode(',',$characteristics);

			// Only a rejected login is logged, with the data that rejected it;
			// logging every login's attributes filled the error log
			if (count($chars) <= 3)
			{
				error_log($response->username . ' login REJECTED due to insufficient I2A2 data: ' . var_export($characteristics, true));
				$response->status = \Hubzero\Auth\Status::FAILURE;
				$response->error_message = Lang::txt('PLG_AUTHENTICATION_PUCAS_AUTHENTICATION_FAILED');
				return;
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

			$result = $this->claimLink($username, User::get('id'), phpCAS::getAttribute('email'));

			if ($result === 'taken')
			{
				// This purdue cas account is already linked to another hub account
				App::redirect(
					Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
					Lang::txt('PLG_AUTHENTICATION_PUCAS_ACCOUNT_ALREADY_LINKED'),
					'error'
				);
			}
			elseif ($result === false)
			{
				// Either the authenticator Domain couldn't be found, no username
				// was provided, or the Link record failed to be created
				Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "pucas", null, %s) returned false', $username));
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
	 * Find the link for a career account on login, creating it only when it
	 * can lead to an account
	 *
	 * The administrator's com_login passes autoregister=false (the frontend
	 * leaves it unset), so an unknown career account there gets no row: a row
	 * without a user_id would make link() refuse that career account later.
	 * The one exception is an existing hub account that onUserAuthenticate()
	 * will claim the row for. With registration off, no row is ever created,
	 * as before.
	 *
	 * @param   string  $username  Career account name asserted by CAS
	 * @param   array   $options   Login options
	 * @return  mixed   Hubzero\Auth\Link, or false
	 */
	protected function findLink($username, $options)
	{
		$hzal = \Hubzero\Auth\Link::find('authentication', 'pucas', null, $username);

		if ($hzal === false && Component::params('com_members')->get('allowUserRegistration', false))
		{
			$autoregister = !isset($options['autoregister']) || $options['autoregister'];

			if ($autoregister || self::matchingAccount($username))
			{
				$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'pucas', null, $username);
			}
		}

		return $hzal;
	}

	/**
	 * The hub account a career account belongs to when no link says so: same
	 * username, and the career account's purdue.edu address, confirmed
	 *
	 * Anyone can register a username and type in an address, so an account
	 * whose address was never confirmed proves nothing about who owns it: the
	 * career account's CAS login would land in an account someone else holds
	 * the password to. Confirmed means activation > 0, as in
	 * Components\Members\Models\Member::isEmailConfirmed().
	 *
	 * @param   string  $username  Career account name
	 * @return  mixed   Hubzero\User\User, or null
	 */
	protected static function matchingAccount($username)
	{
		$user = \Hubzero\User\User::all()
			->whereEquals('username', $username)
			->whereEquals('email', $username . '@purdue.edu')
			->where('activation', '>', 0)
			->row();

		return ($user && $user->get('id')) ? $user : null;
	}

	/**
	 * Link a career account to a hub account
	 *
	 * A row with no user_id (left by an unfinished registration or an older
	 * administrator login) belongs to nobody, and CAS has just proven the
	 * visitor owns the career account, so it is claimed.
	 *
	 * @param   string   $username  Career account name asserted by CAS
	 * @param   integer  $userId    Hub account to link to
	 * @param   string   $email     Email address CAS released, if any
	 * @return  mixed    'linked', 'taken' when another hub account has it, or false
	 */
	protected function claimLink($username, $userId, $email)
	{
		$hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'pucas', null, $username);

		if (!$hzal)
		{
			return false;
		}

		$owner = (int) $hzal->get('user_id');

		if ($owner && $owner != (int) $userId)
		{
			return 'taken';
		}

		if (!$owner)
		{
			$hzal->set('user_id', $userId);
			$hzal->set('email', $email);

			if (!$hzal->update())
			{
				return false;
			}
		}

		return 'linked';
	}

	/**
	 * Check whether the visitor has a CAS session, honoring the
	 * "passive_sso" plugin option.
	 *
	 * When passive_sso is OFF (default), this uses phpCAS::isAuthenticated(),
	 * which only reports an already-established session (or validates a ticket
	 * the user just returned with) and never redirects when there is no
	 * session. This is the safe behavior for IdPs that mishandle passive
	 * checks — notably Purdue's Entra ID, which renders a "Stale Request"
	 * error page for a CAS gateway (SAML IsPassive="true") request instead of
	 * silently returning, breaking login on every page that probes status.
	 *
	 * When passive_sso is ON, this uses phpCAS::checkAuthentication(), which
	 * performs a transparent CAS gateway request to detect an existing SSO
	 * session without prompting (redirecting the browser to the IdP and back).
	 * Enable this only if/when the IdP handles gateway/IsPassive correctly.
	 *
	 * @return  bool  true if the user has an authenticated CAS session
	 **/
	private function casAuthenticated()
	{
		if ($this->params->get('passive_sso', 0))
		{
			return phpCAS::checkAuthentication();
		}

		return phpCAS::isAuthenticated();
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
				$debug_location = trim($this->params->get('debug_location', '/var/log/php/phpCAS.log'));

				if ($debug_location)
				{
					if ($this->logger == null)
					{
						$this->logger = new \Monolog\Logger('pucas');
						$this->stream_handler = new \Monolog\Handler\StreamHandler($debug_location, \Psr\Log\LogLevel::WARNING);
						$this->logger->pushHandler($this->stream_handler);
					}

					phpCAS::setLogger($this->logger);
				}
			}

			phpCAS::client(CAS_VERSION_2_0, 'sso.purdue.edu', 443, '/idp/profile/cas', Request::getSchemeAndHttpHost(), false);

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
