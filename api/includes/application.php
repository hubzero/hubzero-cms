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

class Hubzero_API extends JApplication
{
	private $_enabled = false; // is api server enabled
	private $_component = false; // component we are routing to
	private $_route = array(); // path segments to pass to api controller

	private $_response = null;
	private $_request = null;
	private $_provider = null;

	function __construct($config = array())
	{
		JLoader::import('joomla.version');
		JLoader::import('joomla.user.user');
		JLoader::register('JText' , JPATH_LIBRARIES . DS . 'joomla' . DS . 'methods.php');
		JLoader::import('joomla.filter.filterinput');

		$version = new JVersion();

		define('JVERSION', $version->getShortVersion());

		$config['clientId'] = 4;
        $config['session'] = false;

        parent::__construct($config);
	}

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

	function initialise()
	{
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Api' . DS . "Response.php";
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Api' . DS . "Request.php";

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

	function route()
	{
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Api' . DS . "Controller.php";

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
				. $segments[0] . DS . 'api' . DS . 'controller.php';

			if (is_file($filename))
			{
				require_once($filename);

				$this->_component = $segments[0];

				array_shift($segments);
			}

			$this->_route = $segments;
		}
	}

	function authenticate()
	{
		if (!$this->_route)
		{
			return;
		}

		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'User' . DS . "Profile.php";
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'User' . DS . "Helper.php";
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Oauth' . DS . "Provider.php";
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'User.php';
		require JPATH_LIBRARIES . DS . 'Hubzero' . DS . 'Xml.php';

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

	function render()
	{
		global $_HUBZERO_API_START;

		$this->_response->setHeader('X-Runtime: ' . (microtime(true) - $_HUBZERO_API_START));

		$this->_response->send();
	}

	function execute()
	{
		$this->initialise();

		$this->route();

		$this->authenticate();

		$this->dispatch();

		$this->render();
	}

	function close()
	{
		die('close() invalid in API application context');
	}

	function redirect()
	{
		die('redirect() invalid in API application context');
	}

	function enqueueMessage( $msg, $type = 'message' )
	{
		die('enqueueMessage() invalid in API application context');
	}

	function getMessageQueue()
	{
		die('getMessageQueue() invalid in API application context');
	}

	// function getCfg( $varname ) inherited

	function getName()
    {
        return 'api';
    }

	function getUserState( $key )
	{
		die('getUserState() invalid in API application context');
	}

	function setUserState( $key, $value )
	{
		die('setUserState() invalid in API application context');
	}

	function getUserStateFromRequest( $key, $request, $default = null, $type = 'none' )
	{
		die('getUserStateFromRequest() invalid in API application context');
	}

	function registerEvent($event, $handler)
	{
		die('registerEvent() invalid in API application context');
	}

	function triggerEvent($event, $args=null)
	{
		die('triggerEvent() invalid in API application context');
	}

	function login($credentials, $options = array())
	{
		die('login() invalid in API application context');
	}

	function logout($userid = null, $options = array())
	{
		die('logout() invalid in API application context');
	}

	function &getRouter($name = null, $options = array())
	{
		die('getRouter() invalid in API application context');
	}

	function &getPathway($name = null, $options = array())
	{
		die('getPathway() invalid in API application context');
	}

	function &getMenu($name = null, $options = array())
	{
		die('getMenu() invalid in API application context');
	}

	function &_createSession( $name )
	{
		die('_createSession() invalid in API application context');
	}

	function appendPathWay( $name, $link = null )
	{
		die('appendPathWay() invalid in API application context');
	}

	function getCustomPathWay()
	{
		die('getCustomPathWay() invalid in API application context');
	}

	function getHead()
	{
		die('getHead() invalid in API application context');
	}

	function addMetaTag( $name, $content, $prepend = '', $append = '' )
	{
		die('addMetaTag() invalid in API application context');
	}

	function appendMetaTag( $name, $content )
	{
		die('appendMetaTag() invalid in API application context');
	}

	function prependMetaTag( $name, $content )
	{
		die('prependMetaTag() invalid in API application context');
	}

	function addCustomHeadTag( $html )
	{
		die('addCustomHeadTag() invalid in API application context');
	}

	function getBlogSectionCount( )
	{
		die('getBlogSectionCount() invalid in API application context');
	}

	function getBlogCategoryCount( )
	{
		die('getBlogCategoryCount() invalid in API application context');
	}

	function getGlobalBlogSectionCount( )
	{
		die('getGlobalBlogSectionCount() invalid in API application context');
	}

	function getStaticContentCount( )
	{
		die('getStaticContentCount() invalid in API application context');
	}

	function getContentItemLinkCount( )
	{
		die('getContentItemLinkCount() invalid in API application context');
	}

	function getPath($varname, $user_option = null)
	{
		die('getPath() invalid in API application context');
	}

	function getBasePath($client=0, $addTrailingSlash = true)
	{
		die('getBasePath() invalid in API application context');
	}

	function &getUser()
	{
		die('getUser() invalid in API application context');
	}

	function getItemid( $id )
	{
		die('getItemid() invalid in API application context');
	}

	function setPageTitle( $title=null )
	{
		die('setPageTitle() invalid in API application context');
	}

	function getPageTitle()
	{
		die('getPageTitle() invalid in API application context');
	}
}
