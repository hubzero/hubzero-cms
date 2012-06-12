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
	 * Description for '_response'
	 * 
	 * @var object
	 */
	private $_response = null;

	/**
	 * Description for '_request'
	 * 
	 * @var object
	 */
	private $_request = null;

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
		JLoader::import('joomla.version');
		JLoader::import('joomla.user.user');
		JLoader::register('JText' , JPATH_LIBRARIES . DS . 'joomla' . DS . 'methods.php');
		JLoader::import('joomla.filter.filterinput');

		$version = new JVersion();

	/**
	 * Description for ''JVERSION''
	 */
		define('JVERSION', $version->getShortVersion());

		$config['clientId'] = 4;
        $config['session'] = false;

        parent::__construct($config);
	}

	/**
	 * Short description for 'handle_method_override'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function handle_method_override()
	{
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
	 * Short description for 'fix_http_headers'
	 * 
	 * Long description (if any) ...
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
		    header('X-Powered-By: PHP');
		}
	}

	/**
	 * Short description for 'unregister_long_arrays'
	 * 
	 * Long description (if any) ...
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
	 * Short description for 'unregister_globals'
	 * 
	 * Long description (if any) ...
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

	// static function &getInstance($client, $config, $prefix) inherited

	/**
	 * Short description for 'initialise'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function initialise()
	{
		require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Api' . DS . "Response.php";
		require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Api' . DS . "Request.php";

		$this->unregister_long_arrays();
		$this->unregister_globals();
		$this->fix_http_headers();
		$this->handle_method_override();

		$this->_request = new Hubzero_Api_Request();
		$this->_response = new Hubzero_Api_Response();

		$this->_response->setCachable(false);
		$this->_response->setRequestAccepts($this->_request->getHeaderField('Accept'));
		$this->_response->setRequestAcceptsEncodings($this->_request->getHeaderField('Accept-Encoding'));
		$this->_response->setSuppressResponseCodes($this->_request->getSuppressResponseCodes());

		$this->_enabled = (JFactory::getConfig()->getValue('config.api_server') == true);
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
		require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Api' . DS . "Controller.php";

		if (!$this->_enabled)
		{
			return;
		}

		$route = $this->_request->path;

		$segments = explode('/', $route);

		if (empty($segments[0]))
		{
			array_shift($segments);
		}

		if ((count($segments) > 2) && ($segments[0] == 'api'))
		{
			array_shift($segments);

			if (is_numeric($segments[0]))
			{
				$this->_version = $segments[0];

				array_shift($segments);
			}

			$filename = JPATH_SITE . DS . 'components' . DS . 'com_'
				. $segments[0] . DS . 'controllers' . DS . 'api.php';

			if (is_file($filename))
			{
				require_once($filename);

				$this->_component = $segments[0];

				array_shift($segments);
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
        
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_User_Helper');
		ximport('Hubzero_Oauth_Provider');
		ximport('Hubzero_User');
		ximport('Hubzero_Xml');
		//require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'User' . DS . "Profile.php";
		//require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'User' . DS . "Helper.php";
		//require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Oauth' . DS . "Provider.php";
		//require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'User.php';
		//require_once JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Xml.php';

		$oauthp = new Hubzero_Oauth_Provider();
		$oauthp->setResponse($this->_response);
		$oauthp->setRequestTokenPath('/api/oauth/request_token');
		$oauthp->setAccessTokenPath('/api/oauth/access_token');
		$oauthp->setAuthorizePath('/api/oauth/authorize');
        
		if (!$oauthp->validateRequest())
		{
			return;
		}

		$this->_provider = $oauthp;
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
		if (!$this->_provider)
		{
			return;
		}

		$classname = ucfirst($this->_component) . 'ApiController';

		if (class_exists($classname))
		{
			$controller = new $classname();
			$controller->setRequest($this->_request);
			$controller->setResponse($this->_response);
			$controller->setProvider($this->_provider);
			$controller->setRouteSegments($this->_route);
			$controller->execute();
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

		$this->_response->setHeader('X-Runtime: ' . (microtime(true) - $_HUBZERO_API_START));

		$this->_response->send();
	}

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function execute()
	{
		$this->initialise();

		$this->route();

		$this->authenticate();

		$this->dispatch();

		$this->render();
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
	function registerEvent($event, $handler)
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
	function &getRouter($name = null, $options = array())
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
		
		
		$config =& JFactory::getConfig();
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
	function &_createSession( $name )
	{
		die('_createSession() invalid in API application context');
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
