<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * API application class
 */
class JApi extends JApplication
{
	/**
	 * Description for '_authn'
	 *
	 * @var array
	 */
	private $_authn = array(); // authentication variables

	/**
	 * Description for '_enabled'
	 *
	 * @var boolean
	 */
	private $_enabled = false; // is api server enabled

	/**
	 * Description for '_component'
	 *
	 * @var boolean
	 */
	private $_component = false; // component we are routing to

	/**
	 * Description for '_route'
	 *
	 * @var array
	 */
	private $_route = array(); // path segments to pass to api controller

	/**
	 * Description for 'response'
	 *
	 * @var object
	 */
	public $response = null;

	/**
	 * Description for 'request'
	 *
	 * @var object
	 */
	public $request = null;

	/**
	 * Output
	 *
	 * @var string
	 */
	public $output = '';

	/**
	 * Description for '_provider'
	 *
	 * @var unknown
	 */
	private $_provider = null;

	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A configuration array including optional elements such as session
	 * session_name, clientId and others. This is not exhaustive.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		JLoader::import('joomla.user.user');
		JLoader::register('JText' , JPATH_LIBRARIES . DS . 'joomla' . DS . 'methods.php');
		JLoader::import('joomla.filter.filterinput');
		JLoader::import('joomla.environment.uri');
		JLoader::import('Hubzero.Api.Response');
		JLoader::import('Hubzero.Api.Request');

		$config['clientId'] = 4;

		/*$this->unregister_long_arrays();
		$this->unregister_globals();
		$this->fix_http_headers();

		$this->request = new \Hubzero\Api\Request();
		$this->response = new \Hubzero\Api\Response();
		$this->output = '';*/

		parent::__construct($config);
	}

	/**
	 * Reset the application
	 *
	 * @return  object
	 */
	public function reset()
	{
		$this->unregister_long_arrays();
		$this->unregister_globals();
		$this->fix_http_headers();
		$this->handle_method_override();

		$this->_enabled   = false;
		$this->_component = false;
		$this->_route     = array();
		$this->provider   = null;
		$this->_authn     = array();

		$this->request  = new \Hubzero\Api\Request();
		$this->response = new \Hubzero\Api\Response();
		$this->output   = '';

		JFactory::getSession()->restart();
		$this->checkSession();

		return $this; // chaining
	}

	/**
	 * For clients that can't (or programmer to lazy to) support all
	 * HTTP methods allow the method to be specified via HTTP_X_HTTP_METHOD_OVERRIDE
	 * header.
	 *
	 * @return     void
	 */
	public function handle_method_override()
	{
		// @FIXME: should maybe only be valid if the real method is GET

		if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
		{
			$method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);

			if (in_array($method,array('GET','PUT','POST','DELETE','HEAD','OPTIONS','TRACE','CONNECT')))
			{
				$_SERVER['REQUEST_METHOD'] = $method;
			}
		}
	}

	/**
	 * Add some request headers that PHP suppresses by default
	 *
	 * @return     void
	 */
	public function fix_http_headers()
	{
		if (function_exists('apache_request_headers'))
		{
			$headers = apache_request_headers();

			if (isset($headers['Authorization']) && !isset($_SERVER['HTTP_AUTHORIZATION']))
			{
				$_SERVER['HTTP_AUTHORIZATION'] = $headers['Authorization'];

				// @TODO: pull out x_auth variables and insert into _POST/_REQUEST
				// since oauthp doesn't support them in the header
			};
		}

		if (ini_get('expose_php'))
		{
			$_SERVER['HTTP_X_POWERED_BY'] = 'PHP';
		}
	}

	/**
	 * Unset HTTP_* global variables if using a PHP installation that has these enabled
	 *
	 * @return     void
	 */
	public function unregister_long_arrays()
	{
		if (ini_get('register_long_arrays'))
		{
			unset($GLOBALS['HTTP_ENV_VARS']);
			unset($GLOBALS['HTTP_POST_VARS']);
			unset($GLOBALS['HTTP_GET_VARS']);
			unset($GLOBALS['HTTP_COOKIE_VARS']);
			unset($GLOBALS['HTTP_SERVER_VARS']);
			unset($GLOBALS['HTTP_POST_FILES']);
			unset($GLOBALS['HTTP_SESSION_VARS']);
		}
	}

	/**
	 * Unset 'magic' global variables if using a PHP installation that has these enabled
	 *
	 * @return     void
	 */
	public function unregister_globals()
	{
		if (ini_get('register_globals'))
		{
			$variables_order = ini_get('variables_order');

			if (stripos($variables_order,"E") !== false)
			{
				foreach ($_ENV as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"G") !== false)
			{
				foreach ($_GET as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"P") !== false)
			{
				foreach ($_POST as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"C") !== false)
			{
				foreach ($_COOKIE as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"S") !== false)
			{
				foreach ($_SERVER as $key => $value)
				{
					unset($GLOBALS[$key]);
				}

			}
		}
	}

	/**
	 * Initialise the application.
	 *
	 * @param   array  $options  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function initialise($options = array())
	{
		$this->request->import();

		$this->response->setCachable(false);
		$this->response->setAcceptableMediaTypes($this->request->getHeader('Accept'));
		$this->response->setAcceptableEncodings($this->request->getHeader('Accept-Encoding'));
		$this->response->setSuppressResponseCodes($this->request->getSuppressResponseCodes());

		$this->_enabled = 1;//(JFactory::getConfig()->getValue('config.api_server') == true);
	}

	/**
	 * Route the application.
	 *
	 * Routing is the process of examining the request environment to determine which
	 * component should receive the request. The component optional parameters
	 * are then set in the request object to be processed when the application is being
	 * dispatched.
	 *
	 * @return  void
	 */
	public function route()
	{
		if (!$this->_enabled)
		{
			return;
		}

		$route = $this->request->get('path');

		$segments = explode('/', $route);

		if (empty($segments[0]))
		{
			array_shift($segments);
		}
		// If "api" is in the array but not the first element
		// it means we're running from an instance where the site
		// isn't in the top-level webroot
		if (in_array('api', $segments) && $segments[0] != 'api')
		{
			// Ditch everything leading up to "api"
			foreach ($segments as $segment)
			{
				if ($segment == 'api')
				{
					break;
				}
				array_shift($segments);
			}
		}
		if ((count($segments) >= 2) && ($segments[0] == 'api'))
		{
			array_shift($segments);

			// Is the script name in the array?
			// If so, get rid of it
			if ($segments[0] == 'index.php')
			{
				array_shift($segments);
			}

			if (is_numeric($segments[0]))
			{
				$this->_version = $segments[0];

				array_shift($segments);
			}

			// Strip invalid characters from name
			$segments[0] = preg_replace("/[^a-zA-Z0-9_]/", '', strtolower($segments[0]));

			$classname = ucfirst($segments[0]) . 'ControllerApi';

			if (!class_exists($classname))
			{
				$paths = array(
					JPATH_SITE . DS . 'components' . DS . 'com_' . $segments[0] . DS . 'controllers' . DS . 'api.php',
					JPATH_SITE . DS . 'components' . DS . 'com_' . $segments[0] . DS . 'api' . DS . 'controllers' . DS . 'api.php'
				);
				foreach ($paths as $filename)
				{
					if (is_file($filename))
					{
						require($filename);
						break;
					}
				}
			}

			$this->_component = $segments[0];
			array_shift($segments);

			if ($segments === array())
			{
				$segments[0] = '';
			}

			$this->_route = $segments;
		}
	}

	/**
	 * Authenticate requests
	 *
	 * @return  mixed
	 */
	public function authenticate()
	{
		if (!$this->_route)
		{
			return;
		}

		JLoader::import('Hubzero.User.Profile');
		JLoader::import('Hubzero.User.Helper');
		JLoader::import('Hubzero.Oauth.Provider');
		JLoader::import('Hubzero.User');
		JLoader::import('Hubzero.Xml');

		/*
		 * If CLI then we have to gather all query, post and header values
		 * into params for Oauth_Provider's constructor.
		 */
		$params = array();
				;
		if ( php_sapi_name() == 'cli')
		{
			$queryvars = $this->request->get('queryvars');
			$postvars = $this->request->get('postdata');

			if (!empty($queryvars))
			{
				foreach ($queryvars as $key=>$value)
				{
					if (isset($queryvars[$key]))
					{
						$params[$key] = $queryvars[$key];
					}
					else if (isset($postvars[$key]))
					{
						$params[$key] = $postvars[$key];
					}
				}
			}

			if (!empty($postvars))
			{
				foreach ($postvars as $key=>$value)
				{
					if (isset($queryvars[$key]))
					{
						$params[$key] = $queryvars[$key];
					}
					else if (isset($postvars[$key]))
					{
						$params[$key] = $postvars[$key];
					}
				}
			}

			if (empty($params))
			{
				return false;
			}
		}

		/*
		    If request has a Basic Auth header Oauth will throw an exception if the header doesn't
		    conform to the OAuth protocol. We catch that (or any other)  exception and proceed as 
		    if there was no oauth data.

		    @TODO A better approach might be to inspect the Basic Auth header and see if it even
		    looks like OAuth was being attempted and throw an Oauth compliant error if it was.
		*/

		try
		{
			$oauthp = new \Hubzero\Oauth\Provider($params);

			$oauthp->setRequestTokenPath('/api/oauth/request_token');
			$oauthp->setAccessTokenPath('/api/oauth/access_token');
			$oauthp->setAuthorizePath('/api/oauth/authorize');

			$result = $oauthp->validateRequest($this->request->get('request'), $this->request->get('method'));

			if (is_array($result))
			{
				$this->response->setResponseProvides('application/x-www-form-urlencoded');
				$this->response->setMessage($result['message'], $result['status'], $result['reason']);
				return false;
			}

			$this->_provider = $oauthp;

			$this->_authn['oauth_token'] = $oauthp->getToken();
			$this->_authn['consumer_key'] = $oauthp->getConsumerKey();
		}
		catch (Exception $e)
		{
			$result = false;
		}

		$this->_authn['user_id'] = null;

		if (isset($this->_authn['oauth_token']) && $this->_authn['oauth_token'])
		{
			$data = $oauthp->getTokenData();

			if (!empty($data->user_id))
			{
				$this->_authn['user_id'] = $data->user_id;
			}

			$this->_authn['session_id'] = null;

			JFactory::getSession()->set('user', new JUser($data->user_id));
		}
		else
		{
			// well lets try to authenticate it with a session instead

			$session_name = md5(self::getHash('site'));
			$session_id = null;

			if (!empty($_COOKIE[$session_name]))
			{
				$session_id = $_COOKIE[$session_name];
			}

			$this->_authn['session_id'] = $session_id;
			$this->_authn['user_id'] = null;

			if (!empty($session_id))
			{
				$db = JFactory::getDBO();
				$timeout = JFactory::getConfig()->getValue('config.timeout');
				$query = "SELECT userid FROM `#__session` WHERE session_id=" . $db->Quote($session_id) . "AND " .
					" time + " . (int) $timeout . " <= NOW() AND client_id = 0;";

				$db->setQuery($query);

				$user_id = $db->loadResult();

				if (!empty($user_id))
				{
					$this->_authn['user_id'] = $user_id;
				}
			}

			// tool session authentication
			$toolSessionId    = JRequest::getInt('sessionnum', null, 'POST');
			$toolSessionToken = JRequest::getCmd('sessiontoken', null, 'POST');

			// use request headers as backup method to post vars
			if (!$toolSessionId && !$toolSessionToken)
			{
				$headers          = apache_request_headers();
				$toolSessionId    = (isset($headers['sessionnum'])) ? $headers['sessionnum'] : null;
				$toolSessionToken = (isset($headers['sessiontoken'])) ? $headers['sessiontoken'] : null;
			}

			// if we have a session id & token lets use those to authenticate
			if ($toolSessionId && $toolSessionToken)
			{
				// include neede libs
				require_once PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'helpers' . DS . 'utils.php';

				// instantiate middleware database
				$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

				// attempt to load session from db
				$query = "SELECT * FROM `session` WHERE `sessnum`= " . $mwdb->quote($toolSessionId) . " AND `sesstoken`=" . $mwdb->quote($toolSessionToken);
				$mwdb->setQuery($query);

				// only continue if a valid session was found
				if ($session = $mwdb->loadObject())
				{
					// check users IP against the session execution host IP
					if (JRequest::ip() == gethostbyname($session->exechost))
					{
						$profile = \Hubzero\User\Profile::getInstance($session->username);
						$this->_authn['user_id'] = $profile->get('uidNumber');
					}
				}
			}
		}

		$this->request->validApiKey = !empty($this->_authn['consumer_key']);
	}

	/**
	 * Get authenticated value
	 *
	 * @param   string $key
	 * @return  mixed
	 */
	public function getAuthN($key = null)
	{
		if ($key === null)
		{
			return $this->_authn;
		}

		if (in_array($key, array('session_id', 'user_id', 'oauth_token', 'consumer_key')))
		{
			if (array_key_exists($key, $this->_authn))
			{
				return $this->_authn[$key];
			}

			return null;
		}
	}

	/**
	 * Dispatch the application.
	 *
	 * Dispatching is the process of pulling the option from the request object and
	 * mapping them to a component. If the component does not exist, it handles
	 * determining a default component to dispatch.
	 *
	 * @param   string  $component  The component to dispatch.
	 * @return  void
	 */
	public function dispatch($component = null)
	{
		//if (!$this->_provider)
		//{
		//	return;
		//}

		$classname = ucfirst($this->_component) . 'ControllerApi';

		if (class_exists($classname))
		{
			$controller = new $classname();
			$controller->setRequest($this->request);
			$controller->setResponse($this->response);
			$controller->setProvider($this->_provider);
			$controller->setRouteSegments($this->_route);

			ob_start();
			$controller->execute();
			$output = ob_get_clean();

			$this->response->appendBody($output);
		}
	}

	/**
	 * Render the application.
	 *
	 * Rendering is the process of pushing the document buffers into the template
	 * placeholders, retrieving data from the document and pushing it into
	 * the JResponse buffer.
	 *
	 * @return  void
	 */
	public function render()
	{
		//global $_HUBZERO_API_START;

		//$this->response->setHeader('X-Runtime: ' . (microtime(true) - $_HUBZERO_API_START));

		$this->response->send();
	}

	/**
	 * Execute
	 *
	 * @param   boolean  $capture  Capture output?
	 * @return  void
	 */
	public function execute($capture = false)
	{
		/*global $_HUBZERO_API_START;

		if (!isset($_HUBZERO_API_START))
		{
			$_HUBZERO_API_START = microtime(true);
		}*/

		$this->initialise();

		$this->route();

		$this->authenticate();

		$this->dispatch();

		if ($capture)
		{
			ob_start();
		}

		$this->render();

		if ($capture)
		{
			$this->output = ob_get_clean();
		}

		//unset($_HUBZERO_API_START);
	}

	/**
	 * Exit the application.
	 *
	 * @param   integer  $code  Exit code
	 * @return  void     Dies
	 */
	public function close($code = 0)
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string   $url      The URL to redirect to. Can only be http/https URL
	 * @param   string   $msg      An optional message to display on redirect.
	 * @param   string   $msgType  An optional message type. Defaults to message.
	 * @param   boolean  $moved    True if the page is 301 Permanently Moved, otherwise 303 See Other is assumed.
	 * @return  void     Dies
	 */
	public function redirect($url, $msg = '', $msgType = 'message', $moved = false)
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Invalid in API application context
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 * @return  void
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  void
	 */
	public function getMessageQueue()
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	// function getCfg( $varname ) inherited

	/**
	 * Method to get the application name.
	 *
	 * The dispatcher name is by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor.
	 *
	 * @return  string  The name of the dispatcher.
	 */
	public function getName()
	{
		return 'api';
	}

	/**
	 * Gets a user state.
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 * @return  void    Dies
	 */
	public function getUserState($key, $default = null)
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  $key    The path of the state.
	 * @param   string  $value  The value of the variable.
	 * @return  void    Dies
	 */
	public function setUserState($key, $value)
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 * @return  void    Dies
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Registers a handler to a particular event group.
	 *
	 * @param   string  $event    The event name.
	 * @param   mixed   $handler  The handler, a function or an instance of a event object.
	 * @return  void
	 */
	public static function registerEvent($event, $handler)
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @param   string  $event  The event name.
	 * @param   array   $args   An array of arguments.
	 * @return  void
	 */
	public function triggerEvent($event, $args=null)
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Login authentication function.
	 *
	 * Username and encoded password are passed the onUserLogin event which
	 * is responsible for the user validation. A successful validation updates
	 * the current session record with the user's details.
	 *
	 * Username and encoded password are sent as credentials (along with other
	 * possibilities) to each observer (authentication plugin) for user
	 * validation.  Successful validation will update the current session with
	 * the user details.
	 *
	 * @param   array  $credentials  Array('username' => string, 'password' => string)
	 * @param   array  $options      Array('remember' => boolean)
	 * @return  void
	 */
	public function login($credentials, $options = array())
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Logout authentication function.
	 *
	 * Passed the current user information to the onUserLogout event and reverts the current
	 * session record back to 'anonymous' parameters.
	 * If any of the authentication plugins did not successfully complete
	 * the logout routine then the whole method fails. Any errors raised
	 * should be done in the plugin as this provides the ability to give
	 * much more information about why the routine may have failed.
	 *
	 * @param   integer  $userid   The user to load - Can be an integer or string - If string, it is converted to ID automatically
	 * @param   array    $options  Array('clientid' => array of client id's)
	 * @return  void
	 */
	public function logout($userid = null, $options = array())
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Returns the application JRouter object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 * @return  JRouter  A JRouter object
	 */
	static public function getRouter($name = null, array $options = array())
	{
		//die('getRouter() invalid in API application context');
		/*
		if(!isset($name)) {
			$name = $this->_name;
		}

		jimport( 'joomla.application.router' );
		$router =& JRouter::getInstance($name, $options);
		if (JError::isError($router)) {
			$null = null;
			return $null;
		}
		return $router;
		*/

		$config = JFactory::getConfig();
		$options['mode'] = $config->getValue('config.sef');
		$router = parent::getRouter('api', $options);
		return $router;
	}

	/**
	 * Returns the application JPathway object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function getPathway($name = null, $options = array())
	{
		die(__METHOD__ . ' invalid in API application context');
	}

	/**
	 * Returns the application JPathway object.
	 *
	 * @param   string  $name     The name of the application/client.
	 * @param   array   $options  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function getMenu($name = null, $options = array())
	{
		//die('getMenu() invalid in API application context');
		$options = array();
		$menu = parent::getMenu('site', $options);
		return $menu;
	}

	/**
	 * Create the user session.
	 *
	 * Old sessions are flushed based on the configuration value for the cookie
	 * lifetime. If an existing session, then the last access time is updated.
	 * If a new session, a session id is generated and a record is created in
	 * the #__sessions table.
	 *
	 * @param   string  $name  The sessions name.
	 * @return  JSession  JSession on success. May call exit() on database error.
	 */
	protected function _createSession($name)
	{
		return parent::_createSession(self::getHash('site'));
	}
}
