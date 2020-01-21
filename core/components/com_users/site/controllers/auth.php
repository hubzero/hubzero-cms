<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Users\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Config\Registry;
use Hubzero\Utility\Uri;
use Exception;
use Document;
use Request;
use Config;
use Plugin;
use Notify;
use Event;
use Route;
use Lang;
use User;
use App;

/**
 * Login Controller
 */
class Auth extends SiteController
{
	/**
	 * Default task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get the view data.
		$user = User::getInstance();
		$params = $this->config;

		// Make sure we're using a secure connection
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			App::redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			die('insecure connection and redirection failed');
		}

		// Check for errors.
		if ($this->getError())
		{
			App::abort(500, implode('<br />', $errors));
		}

		// Get the active menu
		$menus = App::get('menu');
		$menu = $menus->getActive();

		$title = Config::get('sitename');
		$description = Config::get('MetaDesc');
		$rights = Config::get('MetaRights');
		$robots = Config::get('robots');
		// Lets cascade the parameters if we have menu item parameters
		if (is_object($menu))
		{
			$temp = $menu->params;

			$params->merge($temp);
			$title = $menu->title;
		}
		else
		{
			// get com_menu global settings
			$temp = clone \Component::params('com_menus');
			$params->merge($temp);

			// if supplied, use page title
			$title = $temp->get('page_title', $title);
		}

		$params->def('page_title', $title);
		$params->def('page_description', $description);
		$params->def('page_rights', $rights);
		$params->def('robots', $robots);


		$this->view->setLayout('login');

		// Escape strings for HTML output
		$pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$login = User::isGuest() ? true : false;
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		if ($menu)
		{
			// Check for layout override
			if (isset($menu->query['layout']))
			{
				$this->view->setLayout($menu->query['layout']);
			}

			$params->def('page_heading', $params->get('page_title', $menu->title));
		}
		else
		{
			$params->def('page_heading', $login ? Lang::txt('JLOGIN') : Lang::txt('JLOGOUT'));
		}

		$title = $params->get('page_title', '');
		if (empty($title))
		{
			$title = Config::get('sitename');
		}
		elseif (Config::get('sitename_pagetitles', 0) == 1)
		{
			$title = Lang::txt('JPAGETITLE', Config::get('sitename'), $title);
		}
		elseif (Config::get('sitename_pagetitles', 0) == 2)
		{
			$title = Lang::txt('JPAGETITLE', $title, Config::get('sitename'));
		}
		Document::setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			Document::setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			Document::setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			Document::setMetadata('robots', $params->get('robots'));
		}

		// Get the default return value
		$defaultReturn = Route::url('index.php?option=com_members&task=myaccount');
		$description   = '';
		if ($menu && isset($menu->params) && is_object($menu->params))
		{
			$defaultReturn = $menu->params->get('login_redirect_url', $defaultReturn);
			// Assume redirect URLs that start with a slash are internal
			// As such, we want to make sure the path has the appropriate root
			$root = Request::root(true);
			if (substr($defaultReturn, 0, 1) == '/'
			 && substr($defaultReturn, 0, strlen($root)) != $root)
			{
				$defaultReturn = rtrim($root, '/') . $defaultReturn;
			}
			$description   = $menu->params->get('login_description');
		}
		$defaultReturn = base64_encode($defaultReturn);

		$uri = Uri::getInstance();
		if ($rtrn = $uri->getVar('return'))
		{
			if (!$this->isBase64($rtrn))
			{
				// This isn't a base64 string and most likely is
				// someone trying to do something nasty (XSS)
				$uri->setVar('return', $defaultReturn);
			}
		}
		$furl = base64_encode($uri->toString());

		// HUBzero: If we have a return set with an authenticator in it, we're linking an existing account
		// Parse the return to retrive the authenticator, and remove it from the list below
		$auth = '';
		if ($return = Request::getString('return', '', 'get'))
		{
			if (!$this->isBase64($return))
			{
				// This isn't a base64 string and most likely is someone trying to do something nasty (XSS)
				$return = null;
				Request::setVar('return', null);
			}
			else
			{
				$decoded_return = base64_decode($return);

				$dr = new Uri($decoded_return);
				if ($dr->hasVar('authenticator'))
				{
					$auth = $dr->getVar('authenticator');
				}
				/*$query  = parse_url($decoded_return);
				if (is_array($query) && isset($query['query']))
				{
					$query  = $query['query'];
					$query  = explode('&', $query);
					$auth   = '';
					foreach ($query as $q)
					{
						$n = explode('=', $q);
						if ($n[0] == 'authenticator')
						{
							$auth = $n[1];
						}
					}
				}*/
			}
		}

		// Set return if it isn't already
		if (!$return && is_object($menu))
		{
			$return = $defaultReturn;
		}

		// Figure out whether or not any of our third party auth plugins are turned on
		// Don't include the 'hubzero' plugin, or the $auth plugin as described above
		$multiAuth      = false;
		$local          = false;
		$plugins        = Plugin::byType('authentication');
		$authenticators = array();
		$remember_me_default = 0;

		foreach ($plugins as $p)
		{
			$client  = App::get('client')->alias . '_login';
			$pparams = new Registry($p->params);

			// Make sure plugin is enabled for a given client
			if (!$pparams->get($client, false))
			{
				continue;
			}

			if ($p->name != 'hubzero' && $p->name != $auth)
			{
				$display = $pparams->get('display_name', ucfirst($p->name));

				$authenticators[$p->name] = array(
					'name'    => $p->name,
					'display' => $display
				);

				$multiAuth = true;
			}
			else if ($p->name == 'hubzero')
			{
				$remember_me_default = $pparams->get('remember_me_default', 0);
				$this->site_display  = $pparams->get('display_name', Config::get('sitename'));
				$local               = true;
			}
		}

		// Override $multiAuth if authenticator is set to hubzero
		if (Request::getWord('authenticator') == 'hubzero')
		{
			$multiAuth = false;
		}

		// Set the return if we have it...
		$returnQueryString = (!empty($return)) ? "&return={$return}" : '';

		// if authenticator is specified call plugin display method, otherwise (or if method does not exist) use default
		$authenticator = Request::getString('authenticator', '');

		Plugin::import('authentication');

		$status = array();
		$tpl = null;
		$this->return = $return;

		foreach ($plugins as $plugin)
		{
			$className = 'plg' . $plugin->type . $plugin->name;

			if (class_exists($className))
			{
				$myplugin = new $className($this, (array)$plugin);

				if (method_exists($className, 'status'))
				{
					$status[$plugin->name] = $myplugin->status();
					//$this->status = $status;
				}

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (method_exists($className, 'display'))
				{
					return $myplugin->display($this, $tpl);
				}
			}
		}

		$this->view
			->set('multiAuth', $multiAuth)
			->set('authenticators', $authenticators)
			->set('totalauths', count($plugins))
			->set('remember_me_default', $remember_me_default)
			->set('pageclass_sfx', $pageclass_sfx)
			->set('description', $description)
			->set('return', $return)
			->set('freturn', $furl)
			->set('status', $status)
			->set('user', $user)
			->set('params', $params)
			->set('returnQueryString', $returnQueryString)
			->set('local', $local)
			->setName('login')
			->setLayout('default')
			->addTemplatePath($this->getTemplatePath())
			->display();
	}

	/**
	 * Get users template override path
	 *
	 * @return  string
	 **/
	protected function getTemplatePath()
	{
		return App::get('template')->path . '/html/com_users/' . $this->view->getName();
	}

	/**
	 * Is the provided string base64 encoded?
	 *
	 * @param   string  $str
	 * @return  bool
	 **/
	protected function isBase64($str)
	{
		if (preg_match('/[^A-Za-z0-9\+\/\=]/', $str))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to log in a user.
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		// Populate the data array:
		$options = array();

		$data = array(
			'username' => Request::getString('username', '', 'post'),
			'password' => Request::getString('passwd', '', 'post'),
			'return'   => Request::getString('return', '', 'post')
		);
		if (!$this->isBase64($data['return']))
		{
			$data['return'] = '';
		}
		else
		{
			$data['return'] = base64_decode($data['return']);
		}

		$authenticator = Request::getString('authenticator', '');

		// If a specific authenticator is specified try to call the login method for that plugin
		if (!empty($authenticator))
		{
			Plugin::import('authentication');

			$plugins = Plugin::byType('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg' . $plugin->type . $plugin->name;

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (class_exists($className))
				{
					if (method_exists($className, 'login'))
					{
						$myplugin = new $className($this, (array)$plugin);

						$myplugin->login($credentials, $options);

						if (isset($options['return']))
						{
							$data['return'] = $options['return'];
						}
					}

					$options['authenticator'] = $authenticator;
					$options['action'] = 'core.login.site';
					break;
				}
			}
		}

		// If no authenticator is specified, or the login method for that plugin did not exist then use default
		if (!isset($myplugin))
		{
			// Check for request forgeries
			Session::checkToken('request');

			if ($return = Request::getString('return', ''))
			{
				if (!$this->isBase64($return))
				{
					$return = '';
				}
				else
				{
					$return = base64_decode($return);
					if (!Uri::isInternal($return))
					{
						$return = '';
					}
				}
			}

			if ($freturn = Request::getString('freturn', ''))
			{
				if (!$this->isBase64($freturn))
				{
					$freturn = '';
				}
				else
				{
					$freturn = base64_decode($freturn);
					if (!Uri::isInternal($freturn))
					{
						$freturn = '';
					}
				}
			}

			// Get the log in options.
			$options = array();
			$options['remember'] = Request::getBool('remember', false);
			$options['return']   = $data['return'];
			$options['action']   = 'core.login.site';
			if (!empty($authenticator))
			{
				$options['authenticator'] = $authenticator;
			}

			// Get the log in credentials.
			$credentials = array();
			$credentials['username'] = $data['username'];
			$credentials['password'] = $data['password'];
		}

		// Make sure return values are internal to the hub
		// Guards against querystring tampering
		if ($data['return'])
		{
			$return = $data['return'];
			if (!Uri::isInternal($return))
			{
				$data['return'] = '';
			}
		}

		// Set the return URL if empty.
		if (empty($data['return']))
		{
			$data['return'] = 'index.php?option=com_members&task=myaccount';
		}

		// Set the return URL in the user state to allow modification by plugins
		User::setState('login.form.return', $data['return']);

		try
		{
			$result = App::get('auth')->login($credentials, $options);
		}
		catch (Exception $e)
		{
			$result = $e;
		}

		// Perform the log in.
		if (true === $result)
		{
			// Success
			User::setState('login.form.data', array());

			$return = User::getState('login.form.return');

			// If no_html is set, return json response
			if (Request::getInt('no_html', 0))
			{
				echo json_encode(array(
					'success'  => true,
					'redirect' => Route::url($return, false)
				));
				exit;
			}
			else
			{
				App::redirect(Route::url(User::getState('login.form.return'), false));
			}
		}
		else
		{
			// Login failed !
			$data['remember'] = isset($options['remember']) ? (int)$options['remember'] : 0;
			User::setState('login.form.data', $data);

			// Facilitate third party login forms
			if (!isset($return) || !$return)
			{
				$return	= Route::url('index.php?option=com_users&view=login');
			}

			if (isset($freturn))
			{
				$return = $freturn;
			}

			$error = ($result) ? $result->getMessage() : Lang::txt('An unknown error has occurred');

			// If no_html is set, return json response
			if (Request::getInt('no_html', 0))
			{
				echo json_encode(array(
					'error'   => $error,
					'freturn' => Route::url($return, false)
				));
				exit;
			}
			else
			{
				// Redirect to a login form
				App::redirect(Route::url($return, false), $error, 'error');
			}
		}
	}

	/**
	 * Multifactor authentication page
	 *
	 * @return  void
	 **/
	public function factorsTask()
	{
		$factors = Event::trigger('authfactors.onRenderChallenge');

		$this->view
			->set('factors', $factors)
			->setName('factors')
			->setLayout('default')
			->addTemplatePath($this->getTemplatePath())
			->display();
	}

	/**
	 * User consent form
	 *
	 * @return  void
	 **/
	public function userconsentTask()
	{
		$this->view
			->setName('userconsent')
			->setLayout('default')
			->addTemplatePath($this->getTemplatePath())
			->display();
	}

	/**
	 * Grant user consent
	 *
	 * @return  void
	 **/
	public function consentTask()
	{
		Session::set('user_consent', true);

		$return = Request::getString('return');

		if ($this->isBase64($return))
		{
			$return = base64_decode($return);
		}
		else
		{
			$return = Reoute::url('index.php');
		}

		App::redirect($return);
	}

	/**
	 * Grant user consent
	 *
	 * @return  void
	 **/
	public function linkTask()
	{
		$user = User::getInstance();

		if ($user->isGuest()
		|| !$user->hasAttribute('auth_link_id')
		|| !is_numeric($user->get('username'))
		|| !$user->get('username') < 0)
	   {
		   $this->linkaccountsTask();
	   }

		// Look up a few things
		$hzal    = \Hubzero\Auth\Link::find_by_id($user->get('auth_link_id'));
		$hzad    = \Hubzero\Auth\Domain::find_by_id($hzal->auth_domain_id);
		$plugins = Plugin::byType('authentication');

		// Get the display name for the current plugin being used
		Plugin::import('authentication', $hzad->authenticator);
		$plugin       = Plugin::byType('authentication', $hzad->authenticator);
		$pparams      = new Registry($plugin->params);
		$refl         = new \ReflectionClass("plgAuthentication{$plugin->name}");
		$display_name = $pparams->get('display_name', $refl->hasMethod('onGetLinkDescription') ? $refl->getMethod('onGetLinkDescription')->invoke(null) : ucfirst($plugin->name));

		// Look for conflicts - first check in the hub accounts
		$profile_conflicts = \Hubzero\User\User::all()
			->whereEquals('email', $hzal->email)
			->rows();

		// Now check the auth_link table
		$link_conflicts = \Hubzero\Auth\Link::find_by_email($hzal->email, array($hzad->id));

		$conflict = array();

		if ($profile_conflicts)
		{
			foreach ($profile_conflicts as $auser)
			{
				$auth_link  = \Hubzero\Auth\Link::find_by_user_id($auser->id);
				$dname      = (is_object($auth_link) && $auth_link->auth_domain_name) ? $auth_link->auth_domain_name : 'hubzero';
				$conflict[] = array(
					'auth_domain_name' => $dname,
					'name'  => $auser->name,
					'email' => $auser->email
				);
			}
		}

		if ($link_conflicts)
		{
			foreach ($link_conflicts as $l)
			{
				$auser      = User::getInstance($l['user_id']);
				$conflict[] = array(
					'auth_domain_name' => $l['auth_domain_name'],
					'name'  => $auser->name,
					'email' => $l['email']
				);
			}
		}

		// Make sure we don't somehow have any duplicate conflicts
		$conflict = array_map('unserialize', array_unique(array_map('serialize', $conflict)));

		// @TODO: Could also check for high probability of name matches???

		// Get the site name
		$sitename = Config::get('sitename');

		// Assign variables to the view
		$this->view
			->set('hzal', $hzal)
			->set('hzad', $hzad)
			->set('plugins', $plugins)
			->set('display_name', $display_name)
			->set('conflict', $conflict)
			->set('sitename', $sitename)
			->set('user', $user)
			->setName('link')
			->setLayout('default')
			->addTemplatePath($this->getTemplatePath())
			->display();
	}

	/**
	 * Grant user consent
	 *
	 * @return  void
	 **/
	public function linkaccountsTask()
	{
		$user = User::getInstance();

		// First, they should already be logged in, so check for that
		if ($user->get('guest'))
		{
			App::abort(403, Lang::txt('You must be logged in to perform this function'));
		}

		// Do we have a return
		$return  = '';
		$options = array();
		if ($return = Request::getString('return', ''))
		{
			$return = base64_decode($return);
			if (!Uri::isInternal($return))
			{
				$return = '';
			}
			else
			{
				$options['return'] = base64_encode($return);
			}
		}

		$authenticator = Request::getString('authenticator', '');

		// If a specific authenticator is specified try to call the link method for that plugin
		if (!empty($authenticator))
		{
			Plugin::import('authentication');

			$plugin = Plugin::byType('authentication', $authenticator);

			$className = 'plg' . $plugin->type . $plugin->name;

			if (class_exists($className))
			{
				if (method_exists($className, 'link'))
				{
					$myplugin = new $className($this, (array)$plugin);
					$myplugin->link($options);
				}
				else
				{
					// No Link method is available
					Notify::error(Lang::txt('Linked accounts are not currently available for this provider.'));

					App::redirect(Route::url('index.php?option=com_members&id=' . $user->get('id') . '&active=account', false));
				}
			}
		}
		else
		{
			// No authenticator provided...
			App::abort(400, Lang::txt('Missing authenticator'));
		}

		// Success!  Redict with message
		Notify::success(Lang::txt('Your account has been successfully linked!'));

		App::redirect(Route::url('index.php?option=com_members&id=' . $user->get('id') . '&active=account', false));
	}

	/**
	 * End single sign-on
	 *
	 * @return  void
	 **/
	public function endsinglesignonTask()
	{
		// Assign variables to the view
		$authenticator = Request::getWord('authenticator', false);

		// Get the site name
		$sitename = Config::get('sitename');

		// Get the display name for the current plugin being used
		$plugin = Plugin::byType('authentication', $authenticator);
		$pparams = new Registry($plugin->params);
		$display_name = $pparams->get('display_name', ucfirst($plugin->name));

		$this->view
			->set('authenticator', $authenticator)
			->set('sitename', $sitename)
			->set('display_name', $display_name)
			->setName('endsinglesignon')
			->setLayout('default')
			->addTemplatePath($this->getTemplatePath())
			->display();
	}

	/**
	 * Method to log out a user.
	 *
	 * @return  void
	 */
	public function logoutTask()
	{
		if (Request::getCmd('view') == 'logout')
		{
			// Initialize variables
			$image    = '';

			$menu = App::get('menu');
			$item = $menu->getActive();
			if ($item)
			{
				$params	= $menu->getParams($item->id);
			}
			else
			{
				$params = new \Hubzero\Config\Registry('');
				$template = App::get('template')->template;
				$inifile = App::get('template')->path . DS .  'html' . DS . 'com_user' . DS . 'logout' . DS . 'config.ini';
				if (file_exists($inifile))
				{
					$params->parse(file_get_contents($inifile));
				}

				$params->def('page_title', Lang::txt( 'Logout' ));
			}

			$type = 'logout';

			// Set some default page parameters if not set
			$params->def( 'show_page_title', 1 );
			if (!$params->get( 'page_title'))
			{
				$params->set('page_title', Lang::txt( 'Logout' ));
			}

			if (!$item)
			{
				$params->def( 'header_logout', '' );
			}

			$params->def('pageclass_sfx', '');
			$params->def('logout', '/');
			$params->def('description_logout', 1);
			$params->def('description_logout_text', Lang::txt('LOGOUT_DESCRIPTION'));
			$params->def('image_logout', 'key.jpg');
			$params->def('image_logout_align', 'right');
			$usersConfig =  Component::params('com_members');
			$params->def('registration', $usersConfig->get('allowUserRegistration'));

			$title = Lang::txt('Logout');

			// Set page title
			Document::setTitle($title);

			// Build logout image if enabled
			if ($params->get('image_' . $type) != -1)
			{
				$image = '/images/stories/'.$params->get('image_' . $type);
				$image = '<img src="'. $image  .'" align="'. $params->get('image_'.$type.'_align') .'" hspace="10" alt="" />';
			}

			// Get the return URL
			if (!$url = Request::getString('return', ''))
			{
				$url = base64_encode($params->get($type));
			}

			$this->view->set('image', $image);
			$this->view->set('type', $type);
			$this->view->set('return', $url);
			$this->view->set('params', $params);
			$this->view->setName('logout');
			$this->view->setLayout('default');
			$this->view->display();
		}

		$app = App::get('app');
		$user = User::getInstance();

		$authenticator = Request::getString('authenticator', '');
		$singleSignOn  = Request::getVar('sso', false);

		if (empty($authenticator) || $authenticator == '')
		{
			$cookie = \Hubzero\Utility\Cookie::eat('authenticator');
			if (isset($cookie->authenticator))
			{
				$authenticator = $cookie->authenticator;
			}
			else
			{
				$authenticator = null;
			}
		}

		// If a specific authenticator is specified try to call the logout method for that plugin
		if (!empty($authenticator))
		{
			Plugin::import('authentication');

			$plugins = Plugin::byType('authentication');

			foreach ($plugins as $plugin)
			{
				$className = 'plg' . $plugin->type . $plugin->name;

				if ($plugin->name != $authenticator)
				{
					continue;
				}

				if (class_exists($className))
				{
					if (method_exists($className, 'logout'))
					{
						$myplugin = new $className($this, (array)$plugin);
					} // End verification of logout() method
				} // End plugin check
			} // End foreach
		} // End check for specified authenticator

		// Build the credentials array.
		$parameters = array();
		$parameters['username'] = $user->get('username');
		$parameters['id'] = $user->get('id');

		$options = array('clientid' => App::get('client')->id);

		$error = false;

		// OK, the credentials are built. Lets fire the onLogout event.
		$results = Event::trigger('user.onUserLogout', array($parameters, $options));

		// Check if any of the plugins failed. If none did, success.
		if (!in_array(false, $results, true))
		{
			// Use domain and path set in config for cookie if it exists.
			$cookie_domain = Config::get('cookie_domain', '');
			$cookie_path   = Config::get('cookie_path', '/');
			setcookie(App::hash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);
		}
		else
		{
			// Trigger onUserLoginFailure Event.
			Event::trigger('user.onUserLogoutFailure', array($parameters));

			$error = true;
		}

		// Check if the log out succeeded.
		if (!($error instanceof Exception))
		{
			// If the authenticator is empty, but they have an active third party session,
			// redirect to a page indicating this and offering complete signout
			if (isset($user->auth_link_id) && $user->auth_link_id && empty($authenticator))
			{
				$auth_domain_name = '';
				$auth_domain      = \Hubzero\Auth\Link::find_by_id($user->auth_link_id);

				if (is_object($auth_domain))
				{
					$auth_domain_id   = $auth_domain->auth_domain_id;
					$auth_domain_name = \Hubzero\Auth\Domain::find_by_id($auth_domain_id)->authenticator;
				}

				// Redirect to user third party signout view
				// Only do this for PUCAS for the time being (it's the one that doesn't lose session info after hub logout)
				if ($auth_domain_name == 'pucas')
				{
					// Get plugin params
					$plugin = Plugin::byType('authentication', $auth_domain_name);

					$pparams = new Registry($plugin->params);
					$auto_logoff = $pparams->get('auto_logoff', false);

					if ($auto_logoff)
					{
						App::redirect(Route::url('index.php?option=com_users&task=user.logout&authenticator=' . $auth_domain_name, false));
						return;
					}
					else
					{
						App::redirect(Route::url('index.php?option=com_users&view=endsinglesignon&authenticator=' . $auth_domain_name, false));
						return;
					}
				}
			}

			// Get the return url from the request and validate that it is internal.
			$return = Request::getString('return', 'index.php');

			if ($this->isBase64($return))
			{
				$return = base64_decode($return);
			}

			// Assume redirect URLs that start with a slash are internal
			// As such, we want to make sure the path has the appropriate root
			$root = Request::root(true);
			if (substr($return, 0, 1) == '/'
			 && substr($return, 0, strlen($root)) != $root)
			{
				$return = rtrim($root, '/') . $return;
			}

			if (!$return || !Uri::isInternal($return))
			{
				$return = 'index.php';
			}
		}
		else
		{
			$return = 'index.php?option=com_users&view=login';
		}

		$return = Route::url($return, false);

		// Redirect the user.
		App::redirect($return);
	}
}
