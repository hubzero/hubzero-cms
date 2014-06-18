<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Short description for 'Hubzero_API'
 *
 * Long description (if any) ...
 */
class Hubzero_API extends JApplication
{
	function reset()
	{
		$this->unregister_long_arrays();
		$this->unregister_globals();
		$this->fix_http_headers();
		$this->handle_method_override();

		$this->_enabled = false;
		$this->_component = false;
		$this->_route = array();
		$this->provider = null;
		$this->_authn = array();

		$this->request = new \Hubzero\Api\Request();
		$this->response = new \Hubzero\Api\Response();
		$this->output = '';
		JFactory::getSession()->restart();
		$this->checkSession();
		return $this; // chaining
	}

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


	public $output = '';
	/**
	 * Description for '_provider'
	 *
	 * @var unknown
	 */
	private $_provider = null;

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $config Parameter description (if any) ...
	 * @return     void
	 */
	function __construct($config = array())
	{
		JLoader::import('joomla.user.user');
		JLoader::register('JText' , JPATH_LIBRARIES . DS . 'joomla' . DS . 'methods.php');
		JLoader::import('joomla.filter.filterinput');
		JLoader::import('joomla.environment.uri');
		JLoader::import('Hubzero.Api.Response');
		JLoader::import('Hubzero.Api.Request');

		$config['clientId'] = 4;

		$this->unregister_long_arrays();
		$this->unregister_globals();
		$this->fix_http_headers();

		$this->request = new \Hubzero\Api\Request();
		$this->response = new \Hubzero\Api\Response();
		$this->output = '';

        parent::__construct($config);
	}

	/**
	 * For clients that can't (or programmer to lazy to) support all
	 * HTTP methods allow the method to be specified via HTTP_X_HTTP_METHOD_OVERRIDE
	 * header.
	 *
	 * @return     void
	 */
	function handle_method_override()
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
	function fix_http_headers()
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
	function unregister_long_arrays()
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
	function unregister_globals()
	{
		if (ini_get('register_globals'))
		{
			$variables_order = ini_get('variables_order');

			if (stripos($variables_order,"E") !== false)
			{
				foreach($_ENV as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"G") !== false)
			{
				foreach($_GET as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"P") !== false)
			{
				foreach($_POST as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"C") !== false)
			{
				foreach($_COOKIE as $key => $value)
				{
					unset($GLOBALS[$key]);
				}
			}

			if (stripos($variables_order,"S") !== false)
			{
				foreach($_SERVER as $key => $value)
				{
					unset($GLOBALS[$key]);
				}

			}
		}
	}

	/**
	 * Short description for 'initialise'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function initialise()
	{
		$this->response->setCachable(false);
		$this->response->setAcceptableMediaTypes($this->request->getHeader('Accept'));
		$this->response->setAcceptableEncodings($this->request->getHeader('Accept-Encoding'));
		$this->response->setSuppressResponseCodes($this->request->getSuppressResponseCodes());

		$this->_enabled = 1;//(JFactory::getConfig()->getValue('config.api_server') == true);
	}

	/**
	 * Short description for 'route'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function route()
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

			$filename = JPATH_SITE . DS . 'components' . DS . 'com_'
				. $segments[0] . DS . 'controllers' . DS . 'api.php';

			// Strip invalid characters from name
			$segments[0] = preg_replace("/[^a-zA-Z0-9_]/", '', strtolower($segments[0]));

			$classname = ucfirst($segments[0]) . 'ControllerApi';

			if (!class_exists($classname))
			{
				if (is_file($filename))
				{
					require($filename);
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
	 * Short description for 'authenticate'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function authenticate()
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
				foreach($queryvars as $key=>$value)
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
				foreach($postvars as $key=>$value)
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

		$oauthp = new \Hubzero\Oauth\Provider($params);
		$oauthp->setRequestTokenPath('/api/oauth/request_token');
		$oauthp->setAccessTokenPath('/api/oauth/access_token');
		$oauthp->setAuthorizePath('/api/oauth/authorize');

		$result = $oauthp->validateRequest($this->request->get('request'), $this->request->get('method'));

		if (is_array($result))
		{
			//$this->response->setResponseProvides('application/x-www-form-urlencoded;q=1.0,text/html;q=0.9');
			$this->response->setResponseProvides('application/x-www-form-urlencoded');
			$this->response->setMessage($result['message'], $result['status'], $result['reason']);
			return false;
		}

		$this->_provider = $oauthp;

		$this->_authn['oauth_token'] = $oauthp->getToken();
		$this->_authn['consumer_key'] = $oauthp->getConsumerKey();
		$this->_authn['user_id'] = null;

		if ($this->_authn['oauth_token'])
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

			$session_name = md5(JUtility::getHash('site'));
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
				$query = "SELECT userid FROM #__session WHERE session_id=" . $db->Quote($session_id) . "AND " .
					" time + " . (int) $timeout . " <= NOW() AND client_id = 0;";

				$db->setQuery($query);

				$user_id = $db->loadResult();

				if (!empty($user_id))
				{
					$this->_authn['user_id'] = $user_id;
				}
			}
		}

		$this->request->validApiKey = !empty($this->_authn['consumer_key']);
	}

	function getAuthN($key = null)
	{
		if ($key === null)
		{
			return $this->_authn;
		}

		if (in_array($key, array('session_id', 'user_id', 'oauth_token', 'consumer_key')))
		{
			if (array_key_exists($key, $this->_authn))
				return $this->_authn[$key];

			return null;
		}
	}

	/**
	 * Short description for 'dispatch'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	function dispatch()
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
	 * Short description for 'render'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function render()
	{
		global $_HUBZERO_API_START;

		$this->response->setHeader('X-Runtime: ' . (microtime(true) - $_HUBZERO_API_START));

		$this->response->send();
	}

	/**
	 * Short description for 'execute'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function execute($capture = false)
	{
		global $_HUBZERO_API_START;

		if (!isset($_HUBZERO_API_START))
		{
			$_HUBZERO_API_START = microtime(true);
		}

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

		unset($_HUBZERO_API_START);
	}

	/**
	 * Short description for 'close'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function close()
	{
		die('close() invalid in API application context');
	}

	/**
	 * Short description for 'redirect'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function redirect()
	{
		die('redirect() invalid in API application context');
	}

	/**
	 * Short description for 'enqueueMessage'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $msg Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     void
	 */
	function enqueueMessage( $msg, $type = 'message' )
	{
		die('enqueueMessage() invalid in API application context');
	}

	/**
	 * Short description for 'getMessageQueue'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getMessageQueue()
	{
		die('getMessageQueue() invalid in API application context');
	}

	// function getCfg( $varname ) inherited

	/**
	 * Short description for 'getName'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getName()
    {
        return 'api';
    }

	/**
	 * Short description for 'getUserState'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @return     void
	 */
	function getUserState( $key )
	{
		die('getUserState() invalid in API application context');
	}

	/**
	 * Short description for 'setUserState'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	function setUserState( $key, $value )
	{
		die('setUserState() invalid in API application context');
	}

	/**
	 * Short description for 'getUserStateFromRequest'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $key Parameter description (if any) ...
	 * @param      unknown $request Parameter description (if any) ...
	 * @param      unknown $default Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     void
	 */
	function getUserStateFromRequest( $key, $request, $default = null, $type = 'none' )
	{
		die('getUserStateFromRequest() invalid in API application context');
	}

	/**
	 * Short description for 'registerEvent'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $event Parameter description (if any) ...
	 * @param      unknown $handler Parameter description (if any) ...
	 * @return     void
	 */
	static function registerEvent($event, $handler)
	{
		die('registerEvent() invalid in API application context');
	}

	/**
	 * Short description for 'triggerEvent'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $event Parameter description (if any) ...
	 * @param      unknown $args Parameter description (if any) ...
	 * @return     void
	 */
	function triggerEvent($event, $args=null)
	{
		die('triggerEvent() invalid in API application context');
	}

	/**
	 * Short description for 'login'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $credentials Parameter description (if any) ...
	 * @param      array $options Parameter description (if any) ...
	 * @return     void
	 */
	function login($credentials, $options = array())
	{
		die('login() invalid in API application context');
	}

	/**
	 * Short description for 'logout'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $userid Parameter description (if any) ...
	 * @param      array $options Parameter description (if any) ...
	 * @return     void
	 */
	function logout($userid = null, $options = array())
	{
		die('logout() invalid in API application context');
	}

	/**
	 * Short description for 'getRouter'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      array $options Parameter description (if any) ...
	 * @return     void
	 */
	static function &getRouter($name = null, $options = array())
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
		$router =& parent::getRouter('api', $options);
		return $router;
	}

	/**
	 * Short description for 'getPathway'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      array $options Parameter description (if any) ...
	 * @return     void
	 */
	function &getPathway($name = null, $options = array())
	{
		die('getPathway() invalid in API application context');
	}

	/**
	 * Short description for 'getMenu'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      array $options Parameter description (if any) ...
	 * @return     void
	 */
	function &getMenu($name = null, $options = array())
	{
		//die('getMenu() invalid in API application context');
		$options = array();
		$menu =& parent::getMenu('site', $options);
		return $menu;
	}

	/**
	 * Short description for '_createSession'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @return     void
	 */
	function _createSession( $name )
	{
		return parent::_createSession(JUtility::getHash('site'));
	}

	/**
	 * Short description for 'appendPathWay'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $link Parameter description (if any) ...
	 * @return     void
	 */
	function appendPathWay( $name, $link = null )
	{
		die('appendPathWay() invalid in API application context');
	}

	/**
	 * Short description for 'getCustomPathWay'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getCustomPathWay()
	{
		die('getCustomPathWay() invalid in API application context');
	}

	/**
	 * Short description for 'getHead'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getHead()
	{
		die('getHead() invalid in API application context');
	}

	/**
	 * Short description for 'addMetaTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $content Parameter description (if any) ...
	 * @param      string $prepend Parameter description (if any) ...
	 * @param      string $append Parameter description (if any) ...
	 * @return     void
	 */
	function addMetaTag( $name, $content, $prepend = '', $append = '' )
	{
		die('addMetaTag() invalid in API application context');
	}

	/**
	 * Short description for 'appendMetaTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $content Parameter description (if any) ...
	 * @return     void
	 */
	function appendMetaTag( $name, $content )
	{
		die('appendMetaTag() invalid in API application context');
	}

	/**
	 * Short description for 'prependMetaTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $name Parameter description (if any) ...
	 * @param      unknown $content Parameter description (if any) ...
	 * @return     void
	 */
	function prependMetaTag( $name, $content )
	{
		die('prependMetaTag() invalid in API application context');
	}

	/**
	 * Short description for 'addCustomHeadTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $html Parameter description (if any) ...
	 * @return     void
	 */
	function addCustomHeadTag( $html )
	{
		die('addCustomHeadTag() invalid in API application context');
	}

	/**
	 * Short description for 'getBlogSectionCount'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getBlogSectionCount( )
	{
		die('getBlogSectionCount() invalid in API application context');
	}

	/**
	 * Short description for 'getBlogCategoryCount'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getBlogCategoryCount( )
	{
		die('getBlogCategoryCount() invalid in API application context');
	}

	/**
	 * Short description for 'getGlobalBlogSectionCount'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getGlobalBlogSectionCount( )
	{
		die('getGlobalBlogSectionCount() invalid in API application context');
	}

	/**
	 * Short description for 'getStaticContentCount'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getStaticContentCount( )
	{
		die('getStaticContentCount() invalid in API application context');
	}

	/**
	 * Short description for 'getContentItemLinkCount'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getContentItemLinkCount( )
	{
		die('getContentItemLinkCount() invalid in API application context');
	}

	/**
	 * Short description for 'getPath'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $varname Parameter description (if any) ...
	 * @param      unknown $user_option Parameter description (if any) ...
	 * @return     void
	 */
	function getPath($varname, $user_option = null)
	{
		die('getPath() invalid in API application context');
	}

	/**
	 * Short description for 'getBasePath'
	 *
	 * Long description (if any) ...
	 *
	 * @param      integer $client Parameter description (if any) ...
	 * @param      boolean $addTrailingSlash Parameter description (if any) ...
	 * @return     void
	 */
	function getBasePath($client=0, $addTrailingSlash = true)
	{
		die('getBasePath() invalid in API application context');
	}

	/**
	 * Short description for 'getUser'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function &getUser()
	{
		die('getUser() invalid in API application context');
	}

	/**
	 * Short description for 'getItemid'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     void
	 */
	function getItemid( $id )
	{
		die('getItemid() invalid in API application context');
	}

	/**
	 * Short description for 'setPageTitle'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $title Parameter description (if any) ...
	 * @return     void
	 */
	function setPageTitle( $title=null )
	{
		die('setPageTitle() invalid in API application context');
	}

	/**
	 * Short description for 'getPageTitle'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function getPageTitle()
	{
		die('getPageTitle() invalid in API application context');
	}
}
