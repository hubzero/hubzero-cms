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

namespace Hubzero\Api;

/**
 * API request class
 */
class Request
{
	/**
	 * Description for 'suppress_response_codes'
	 *
	 * @var boolean
	 */
	public $suppress_response_codes = false;

	/**
	 * Description for 'accepts'
	 *
	 * @var string
	 */
	public $accepts = "text/plain";

	/**
	 * Description for 'method'
	 *
	 * @var string
	 */
	private $method = 'GET';
	/**
	 * Description for 'path'
	 *
	 * @var string
	 */
	private $scheme = 'http';
	private $username = '';
	private $password = '';
	private $hostname = 'localhost';
	private $port = '';
	private $path = '';
	private $query = '';
	private $fragment = '';

	private $version = 'HTTP/1.0';

	private $headers = array();

	private $body = '';

	private $_server = array();
	private $_get = array();
	private $_post = array();
	private $_files = array();
	private $_cookie = array();
	private $_session = array();
	private $_request = array();
	private $_env = array();



	/**
	 * Short description for 'getHeaderField'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $header Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function getHeaderField($header)
	{
		$header = strtoupper($header);

		if ($header == 'ACCEPT')
		{
			return $this->accepts;
		}

		$key = 'HTTP_' . strtoupper($header);

		if (isset($_SERVER[$key]))
		{
			return $SERVER[$key];
		}

		return null;
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	function __construct($options = array())
	{
		if (($options == '_SERVER') || (in_array('_SERVER', $options)))
		{
			$this->set('request','_SERVER');

			if (isset($_GET['format']))
			{
				$this->accepts = $this->_parse_accept($_GET['format']);
			}
			else if (isset($_POST['format']))
			{
				$this->accepts = $this->_parse_accept($_POST['format']);
			}
			else if (isset($_SERVER['HTTP_ACCEPT']))
			{
				$this->accepts = $_SERVER['HTTP_ACCEPT'];
			}

			if (empty($this->accepts))
			{
				$format = strrchr($_SERVER['REQUEST_URI'],'.');

				if (strchr($format,'/') === false)
				{
					$this->accepts = $this->_parse_accept(substr($format,1));
				}
			}

			if (isset($_GET['suppress_response_codes']))
			{
				$this->suppress_response_codes = true;
			}

			if (isset($_POST['suppress_response_codes']))
			{
				$this->suppress_response_codes = true;
			}

			if (isset($_SERVER['HTTP_X_HTTP_SUPPRESS_RESPONSE_CODES']))
			{
				$this->suppress_response_codes = true;
			}

			if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']))
			{
				$this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
			}
			else
			{
				if (isset($_SERVER['REQUEST_METHOD']))
				{
					$this->method = $_SERVER['REQUEST_METHOD'];
				}
				else
				{
					$this->method = '';
				}
			}
		}
	}

	function import($what = array('all'), $where = '_SYSTEM')
	{
		if ($where != '_SYSTEM')
		{
			return false;
		}

		$what = (array) $what;

		if (in_array('all',$what)) {
			$what = array_merge($what, array('method','request','version','headers','body','hostname','scheme','postdata'));
		}

		foreach ($what as $item) {
			switch ($item)
			{
				case 'method':
					$this->set('method', $_SERVER['REQUEST_METHOD']);
					break;
				case 'request':
					$this->set('request', $_SERVER['REQUEST_URI']);
					break;
				case 'version':
					$this->set('version', $_SERVER['SERVER_PROTOCOL']);
					break;
				case 'headers':
					$this->set('header', null);

					foreach ($_SERVER as $key=>$value)
					{
						if (strncmp($key, 'HTTP_', 5) == 0)
						{
							$header = explode('_', strtolower($key));
							array_shift($header);
							$header = array_map('ucfirst', $header);
							$header = implode('-', $header);
							$this->headers[$header] = $value;
						}
					}
					break;
				case 'body':
					$this->set('body','php://input');
					break;
				case 'hostname':
					$this->set('hostname', $_SERVER['HTTP_HOST']);
					break;
				case 'scheme':
					if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'])
					{
						$this->set('scheme','https');
					}
					else
					{
						$this->set('scheme','http');
					}
				case 'postdata':
					$this->set('postdata', $_POST);
					break;
			}
		}

	}

	function export($what = 'all', $where = '_SYSTEM')
	{
		if ($where != '_SYSTEM')
		{
			return false;
		}

		$what = (array) $what;

		if (in_array('all',$what))
		{
			$what = array_merge($what, array('uri','get','method','version','headers','postdata'));
		}


		// uri.... fill _SERVER: SCRIPT_*, QUERY_STRING
		// method... fill _SERVER: REQUEST_METHOD
		// headers... fill _SERVER HTTP_*
		// get... fill _GET
		// post.... fill _POST
		// cookies.. fill _COOKIE
		// recompute _REQUEST if _GET or _POST changed

		foreach ($what as $item)
		{
			switch ($item)
			{
				case 'version':
					$_SERVER['SERVER_PROTOCOL'] = $this->version;
					break;

				case 'uri':
					$_SERVER['REQUEST_URI'] = '/' . $this->path;
					$_SERVER['SCRIPT_NAME'] = '/' . $this->path;
					$_SERVER['PHP_SELF'] = '/' . $this->path;
					$_SERVER['SCRIPT_URL'] 	= '/' . $this->path;

					$request = '';

					if ($this->scheme)
					{
						$request .= $this->scheme . "://";
					}

					if ($this->username && $this->password)
					{
						$request .= $this->username . ":" . $this->password . '@';
					}

					$request .= $this->hostname;

					if ($this->port)
					{
						$request .= ":" . $this->port . "/";
					}

					$request .= $this->path;

					$_SERVER['SCRIPT_URI'] 	= $request;

					if (!empty($this->query))
					{
						$_SERVER['REQUEST_URI'] .= '?'.rawurldecode($this->get('query'));
					}

					if ($this->get('scheme') == 'https')
					{
						$_SERVER['HTTPS'] = 'on';
					}
					else
					{
						unset($_SERVER['HTTPS']);
					}

					$_SERVER['QUERY_STRING'] = rawurldecode($this->get('query'));

					break;
				case 'method':
					$_SERVER['REQUEST_METHOD'] = $this->get('method');
					break;
				case 'get':
					{
						// can add variables to scope, so keep these braces to block out scope
						parse_str($this->get('query'), $_GET);
						parse_str($this->get('query'), $_REQUEST); // @FIXME: quick hack, will break when _POST support added
					}
					break;
				case 'postdata':
					$_POST = $this->get('postdata');
					break;
				case 'headers':
					foreach ($this->headers as $key=>$value)
					{
						$key = str_replace('-','_',$key);
						$key = "HTTP_" . strtoupper($key);
						$_SERVER[$key] = $value;
					}
					if (!isset($_SERVER['HTTP_HOST']))
					{
						$_SERVER['HTTP_HOST'] = $this->get('hostname');
					}
					break;
			}
		}

		$order = ini_get('request_order');

		if (empty($order))
		{
			$order = ini_get('variables_order');
		}

		if (empty($order))
		{
			$order = "GP";
		}

		$g = stripos($order, 'g');
		$p = stripos($order, 'p');

		if ($g < $p)
		{
			$_REQUEST = $_GET;

			if (!empty($_POST))
			{
				foreach ($_POST as $k=>$v)
				{
					$_REQUEST[$k] = $v;
				}
			}
		}
		else
		{
			$_REQUEST = $_POST;

			if (!empty($_GET))
			{
				foreach ($_GET as $k=>$v)
				{
					$_REQUEST[$k] = $v;
				}
			}
		}

		$GLOBALS['_JREQUEST'] = array();
	}

	/**
	 * Short description for 'getMethod'
	 *
	 * Long description (if any) ...
	 *
	 * @return     string Return description (if any) ...
	 */
	function getMethod()
	{
		return $this->method;
	}

	/**
	 * Short description for 'getSuppressResponseCodes'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	function getSuppressResponseCodes()
	{
		return $this->suppress_response_codes;
	}

	/**
	 * Short description for '_parse_accept'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $input Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	function _parse_accept($input)
	{
		static $_types = array(
			'xml' => 'application/xml',
			'html' => 'text/html',
			'xhtml' => 'application/xhtml+xml',
			'json' => 'application/json',
			'text' => 'text/plain',
			'txt' => 'text/plain',
			'plain' => 'text/plain',
			'php_serialized' => 'application/vnd.php.serialized',
			'php' => 'application/php',
		);

		if (isset($_types[$input]))
		{
			return $_types[$input];
		}

		return '';
	}

	public function get($key, $default = '')
	{
		switch ($key)
		{
			case 'version':
				return isset($this->version) ? $this->version : $default;
			case 'method':
				return isset($this->method) ? $this->method : $default;
			case 'scheme':
				return isset($this->scheme) ? $this->scheme: $default;
			case 'username':
				return isset($this->username) ? $this->username: $default;
			case 'password':
				return isset($this->password) ? $this->password: $default;
			case 'hostname':
				return isset($this->hostname) ? $this->hostname: $default;
			case 'port':
				return isset($this->port) ? $this->port: $default;
			case 'path':
				return isset($this->path) ? $this->path: $default;
			case 'query':
				return isset($this->query) ? $this->query: $default;
			case 'fragment':
				return isset($this->fragment) ? $this->fragment: $default;
			case 'request': // @FIXME: this work should probably be cached

				$request = '';

				if ($this->scheme)
				{
					$request .= $this->scheme . "://";
				}

				if ($this->username && $this->password)
				{
					$request .= $this->username . ":" . $this->password . '@';
				}

				$request .= $this->hostname;

				if ($this->port)
				{
					$request .= ":" . $this->port . "/";
				}

				$request .= $this->path;

				if ($this->query)
				{
					$request .= "?" . $this->query;
				}

				if ($this->fragment)
				{
					$request .= "#" . $this->fragment;
				}

				return !empty($request) ? $request : $default;

			case 'queryvars': // @FIXME: this work should be cached
			{
				// can add variables to scope, so keep these braces to block out scope
				parse_str($this->get('query'), $queryvars);

				return $queryvars;
			}
			case 'postdata':
				return $this->_post;
			case 'sbs':
				if (empty($this->method))
				{
					return false;
				}

				$sbs = oauth_get_sbs($this->method, $this->get('request'), $this->_post);
				return $sbs;

			default:
				break;

		}
	}

	public function setHeader($key, $value)
	{
		if (empty($value))
		{
			unset($this->headers[$key]);
		}
		else
		{
			$this->headers[$key] = $value;
		}
	}

	public function getHeader($key, $default = '')
	{
		if (isset($this->headers[$key]))
		{
			return $this->headers[$key];
		}

		return $default;
	}

	public function add($property, $key, $value)
	{
		switch ($property)
		{
			case 'query':

				if (!empty($this->query))
				{
					$this->query .= '&';
				}

				$this->query .= $key . '=' . rawurlencode($value);

				break;

			case 'postdata':
				if ($value === null)
				{
					unset($this->_post[$key]);
				}
				else
				{
					$this->_post[$key] = $value;
				}
		}
	}

	public function set($key, $value = null)
	{
		switch ($key)
		{
			case 'method':
				$this->method = $value;
				break;
			case 'scheme':
				$this->scheme = $value;
				break;
			case 'username':
				$this->username = $value;
				break;
			case 'password':
				$this->password = $value;
				break;
			case 'hostname':
				$this->hostname = $value;
				break;
			case 'port':
				$this->port = $value;
				break;
			case 'path':
				$this->path = $value;
				break;
			case 'query':
				if (is_array($value))
				{
					$this->query = '';

					foreach ($value as $key=>$value)
					{
						if (!empty($this->query))
						{
							$this->query .= '&';
						}

						$this->query .= $key . '=' . rawurlencode($value);
					}
				}
				else
				{
					$this->query = $value;
				}
				break;
			case 'fragment':
				$this->fragment = $value;
				break;
			case 'version':
				$this->version = $value;
				break;
			case 'headers':
				$this->headers = $value;
				break;
			case 'body';
				$this->body = $value;
				break;
			case 'postdata':
				$this->_post = $value;
				break;
			case 'request':
			{
				if (is_string($value))
				{
					if ($value == '_SERVER')
					{
						$u = $_SERVER['SCRIPT_URI'];
					}
					else
					{
						$u = parse_url($value);
					}

					$this->scheme = (isset($u['scheme'])) ? $u['scheme'] : '';
					$this->username = (isset($u['username'])) ? $u['username'] : '';
					$this->password = (isset($u['password'])) ? $u['password'] : '';
					$this->hostname = (isset($u['hostname'])) ? $u['hostname'] : '';
					$this->port = (isset($u['port'])) ? $u['port'] : '';
					$this->path = (isset($u['path'])) ? $u['path'] : '';
					$this->query = (isset($u['query'])) ? $u['query'] : '';
					$this->fragment = (isset($u['fragment'])) ? $u['fragment'] : '';
				}
				else
				{
					$this->scheme = '';
					$this->username = '';
					$this->password = '';
					$this->hostname = '';
					$this->port = '';
					$this->path = '';
					$this->query = '';
					$this->fragment = '';
				}
			}
		}
	}

	public function sign($type = 'oauth', $key = '', $secret1 = '', $secret2 = '', $method = '')
	{
		if (empty($method))
		{
			$method = $this->get('method');

			if ($method == 'GET')
			{
				$qkey = 'query';
			}
			else
			{
				$qkey = 'postdata';
			}
		}
		//$qkey = 'query';
		switch ($type)
		{
			case 'oauth':
				$queryvars = $this->get('queryvars');
				$postvars = $this->get('postdata');

				if (!isset($queryvars['oauth_nonce']) && !isset($postvars['oauth_nonce']))
				{
					$this->add($qkey, 'oauth_nonce', uniqid());
				}
				if (!isset($queryvars['oauth_timestamp']) && !isset($postvars['oauth_timestamp']))
				{
					$this->add($qkey, 'oauth_timestamp', time());
				}
				if (!isset($queryvars['oauth_token']) && !isset($postvars['oauth_token']))
				{
					$this->add($qkey, 'oauth_token', '');
				}
				if (!isset($queryvars['oauth_consumer_key']) && !isset($postvars['oauth_consumer_key']))
				{
					$this->add($qkey, 'oauth_consumer_key', oauth_urlencode($key));
				}
				if (!isset($queryvars['oauth_signature_method']) && !isset($postvars['oauth_signature_method']))
				{
					$this->add($qkey, 'oauth_signature_method', 'HMAC-SHA1');
				}
				if (!isset($queryvars['oauth_version']) && !isset($postvars['oauth_version']))
				{
					$this->add($qkey, 'oauth_version', '1.0');
				}
				if (isset($queryvars['oauth_signature']) || isset($postvars['oauth_signature']))
				{
					return false;
				}

				$sbs = $this->get('sbs');

				$secret = (!empty($secret1)) ? oauth_urlencode($secret1) : '';
				$token_secret = (!empty($secret2)) ? oauth_urlencode($secret2) : '';
				$secret = $secret . '&' . $token_secret;

				$signature = base64_encode( hash_hmac('sha1', $sbs, $secret, true) );

				$this->add($qkey, 'oauth_signature', $signature);

				break;

			default:
				return false;
		}
	}

}