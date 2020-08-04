<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Hubzero\Spam\Honeypot;
use App;

/**
 * Request handler replaces the default PHP global variables
 * and functions by an object-oriented layer.
 *
 * A number of methods are inspired by or follow Joomla
 * syntax to maintain compatibility.
 *
 * Further inspiration for Laravel's Request class
 * http://laravel.com
 */
class Request extends BaseRequest
{
	/**
	 * Array of filters
	 *
	 * @var  array
	 */
	static $filters = array(
		'int'   => '/-?[0-9]+/',
		'float' => '/-?[0-9]+(\.[0-9]+)?/',
		'cmd'   => '/[^A-Z0-9_\.-]/i',
		'word'  => '/[^A-Z_]/i'
	);

	/**
	 * Set a variable in one of the request variables.
	 *
	 * @param   string   $name       Name
	 * @param   string   $value      Value
	 * @param   string   $hash       Hash
	 * @param   boolean  $overwrite  Boolean
	 * @return  boolean
	 */
	public function setVar($name, $value = null, $hash = 'method', $overwrite = true)
	{
		// If overwrite is true, makes sure the variable hasn't been set yet
		if (!$overwrite && $this->has($name))
		{
			return $this->getVar($name);
		}

		// Get the request hash value
		$hash = strtolower($hash);
		if ($hash === 'method')
		{
			$hash = strtolower($this->getMethod());
		}

		switch ($hash)
		{
			case 'server':
				$hash = 'server';
			break;

			case 'cookie':
			case 'cookies':
				$hash = 'cookies';
			break;

			case 'file':
			case 'files':
				$hash = 'files';
			break;

			case 'head':
			case 'get':
			case 'query':
				$hash = 'query';
			break;

			case 'header':
			case 'headers':
				$hash = 'headers';
			break;

			case 'post':
			case 'request':
			case 'delete':
			case 'put':
				$hash = 'request';
			break;

			default:
				$hash = 'query';
			break;
		}

		return $this->$hash->set($name, $value);
	}

	/**
	 * Get var
	 *
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @param   string   $type     Return type for the variable. [!] Deprecated. Joomla legacy support.
	 * @param   string   $mask     Filter mask for the variable. [!] Deprecated. Joomla legacy support.
	 * @return  integer  Request variable
	 */
	public function getVar($key, $default = null, $hash = 'input', $type = 'none', $mask = 0)
	{
		$hash = strtolower($hash);

		switch ($hash)
		{
			case 'server':
				return $this->server($key, $default);
			break;

			case 'cookie':
				return $this->cookie($key, $default);
			break;

			case 'files':
				/*$result = $this->file($key, $default);
				if ($type == 'array')
				{
					$res = array(
						'name'      => null,
						'tmp_name'  => null,
						'mime_type' => null,
						'extension' => null,
						'size'      => null
					);
					if ($result)
					{
						var_dump($_FILES); die();
						$res = array(
							'name'      => $result->getClientOriginalName(),
							'tmp_name'  => $result->getPathName(),
							'mime_type' => $result->getClientMimeType(),
							'extension' => $result->getExtension(),
							'size'      => $result->getClientSize()
						);
					}
					$result = $res;
				}*/
				$result = null;
				if (isset($_FILES[$key]) && $_FILES[$key] !== null)
				{
					$result = $_FILES[$key];
				}
				$result = ($result !== null ? $result : $default);
				if ($type == 'array')
				{
					$result = (array) $result;
				}
				return $result;
			break;

			case 'post':
				return $this->request($key, $default);
			break;

			case 'get':
				return $this->query($key, $default);
			break;

			default:
				return $this->input($key, $default);
			break;
		}
	}

	/**
	 * Get integer
	 *
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  itneger  Request variable
	 */
	public function getInt($key, $default = 0, $hash = 'input')
	{
		$str = $this->getVar($key, $default, $hash);
		$str = is_array($str) ? self::_flatten('', $str) : $str;
		preg_match('/-?[0-9]+/', $str, $matches);
		$result = @ $matches[0];
		return (!is_null($result) ? (int) $result : $default);
	}

	/**
	 * Get unsigned integer
	 *
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  integer  Request variable
	 */
	public function getUInt($name, $default = 0, $hash = 'input')
	{
		$result = $this->getInt($name, $default, $hash);
		return (!is_null($result) ? abs($result) : $default);
	}

	/**
	 * Get float
	 *
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  integer  Request variable
	 */
	public function getFloat($name, $default = 0.0, $hash = 'input')
	{
		$result = $this->getVar($key, $default, $hash);
		$result = is_array($result) ? self::_flatten('', $result) : $result;
		return preg_replace(static::$filters['float'], '', $result);
	}

	/**
	 * Get boolean
	 *
	 * @param   string   $key      Request key
	 * @param   mixed    $default  Default value
	 * @param   string   $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  boolean  Request variable
	 */
	public function getBool($key = null, $default = null, $hash = 'input')
	{
		$result = (bool) $this->getVar($key, $default, $hash);
		return $result ? true : false;
	}

	/**
	 * Get word
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  string  Request variable
	 */
	public function getWord($key, $default = null, $hash = 'input')
	{
		$result = $this->getVar($key, $default, $hash);
		$result = is_array($result) ? self::_flatten('', $result) : $result;
		return preg_replace(static::$filters['word'], '', $result);
	}

	/**
	 * Get cmd
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  string  Request variable
	 */
	public function getCmd($key = null, $default = null, $hash = 'input')
	{
		$result = $this->getVar($key, $default, $hash);
		$result = is_array($result) ? self::_flatten('', $result) : $result;
		$result = (string) preg_replace(static::$filters['cmd'], '', $result);
		return ltrim($result, '.');
	}

	/**
	 * Fetches and returns a given variable as an array.
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  array   Request variable
	 */
	public function getArray($key = null, $default = array(), $hash = 'input')
	{
		return (array) $this->getVar($key, $default, $hash);
	}

	/**
	 * Fetches and returns a given variable as a string.
	 *
	 * @param   string  $key      Request key
	 * @param   mixed   $default  Default value
	 * @param   string  $hash     Where the var should come from (POST, GET, FILES, COOKIE, METHOD)
	 * @return  string  Request variable
	 */
	public function getString($name, $default = null, $hash = 'input')
	{
		$result = $this->getVar($name, $default, $hash);
		$result = is_array($result) ? self::_flatten('', $result) : $result;
		return (string) $result;
	}

	/**
	 * Return the Request instance.
	 *
	 * @return  object
	 */
	public function instance()
	{
		return $this;
	}

	/**
	 * Get the request method.
	 *
	 * @return  string
	 */
	public function method()
	{
		return $this->getMethod();
	}

	/**
	 * Get the root URL for the application.
	 *
	 * @param   boolean  $pathonly  If false, prepend the scheme, host and port information. Default is false.
	 * @return  string
	 */
	public function root($pathonly = false)
	{
		$root = rtrim(($pathonly ? '' : $this->getSchemeAndHttpHost()) . $this->getBasePath(), '/');
		$root = explode('/', $root);
		if (in_array(end($root), array('administrator', 'api')))
		{
			array_pop($root);
		}

		return implode('/', $root) . '/';
	}

	/**
	 * Get the URL (no query string) for the request.
	 *
	 * @return  string
	 */
	public function current($query = false)
	{
		return ($query ? $this->getUri() : rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/'));
	}

	/**
	 * Get the full URL for the request.
	 *
	 * @return  string
	 */
	/*public function fullUrl()
	{
		$query = $this->getQueryString();

		return $query ? $this->current() . '?' . $query : $this->current();
	}*/

	/**
	 * Get the current path info for the request.
	 *
	 * @return  string
	 */
	public function path()
	{
		$pattern = trim($this->getPathInfo(), '/');

		return $pattern == '' ? '/' : '/' . $pattern;
	}

	/**
	 * Get the current path info for the request.
	 *
	 * @param   boolean  $pathonly  If false, prepend the scheme, host and port information. Default is false.
	 * @return  string
	 */
	public function base($pathonly = false)
	{
		$path = $this->getBasePath();

		if ($pathonly)
		{
			return $path;
		}

		return $this->getSchemeAndHttpHost() . '/' . ($path ? trim($path, '/') . '/' : '');
	}

	/**
	 * Get a segment from the URI (1 based index).
	 *
	 * @param   string  $index
	 * @param   mixed   $default
	 * @return  string
	 */
	public function segment($index, $default = null)
	{
		$segments = $this->segments();

		return isset($segments[$index - 1]) ? $segments[$index - 1] : $default;
	}

	/**
	 * Get all of the segments for the request path.
	 *
	 * @return  array
	 */
	public function segments()
	{
		$segments = explode('/', $this->path());

		return array_values(array_filter($segments, function ($v)
		{
			return $v != '';
		}));
	}

	/**
	 * Determine if the request is the result of an AJAX call.
	 *
	 * @return  bool
	 */
	public function ajax()
	{
		return $this->isXmlHttpRequest();
	}

	/**
	 * Determine if the request is over HTTPS.
	 *
	 * @return  bool
	 */
	public function secure()
	{
		return $this->isSecure();
	}

	/**
	 * Get the IP address of the client.
	 *
	 * @return  string
	 */
	public function ip()
	{
		return $this->getClientIp();
	}

	/**
	 * Get the request scheme.
	 *
	 * @return  string
	 */
	public function scheme()
	{
		return $this->getScheme();
	}

	/**
	 * Get the HTTP host.
	 *
	 * @return  string
	 */
	public function host()
	{
		return $this->getHost();
	}

	/**
	 * Determine if the request contains a given input item.
	 *
	 * @param   mixed  $key  string|array
	 * @return  bool
	 */
	public function has($key)
	{
		if (count(func_get_args()) > 1)
		{
			foreach (func_get_args() as $value)
			{
				if (!$this->has($value))
				{
					return false;
				}
			}

			return true;
		}

		if (is_bool($this->input($key)) || is_array($this->input($key)))
		{
			return true;
		}

		return (trim((string) $this->input($key)) !== '');
	}

	/**
	 * Retrieve an input item from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function input($key = null, $default = null)
	{
		$input = $this->getInputSource()->all() + $this->query->all();

		return isset($input[$key]) ? $input[$key] : $default;
	}

	/**
	 * Get the input source for the request.
	 *
	 * @return  object
	 */
	protected function getInputSource()
	{
		return $this->getMethod() == 'GET' ? $this->query : $this->request;
	}

	/**
	 * Retrieve a post item from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function request($key = null, $default = null)
	{
		return $this->retrieveItem('request', $key, $default);
	}

	/**
	 * Retrieve a query string item from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function query($key = null, $default = null)
	{
		return $this->retrieveItem('query', $key, $default);
	}

	/**
	 * Retrieve a cookie from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function cookie($key = null, $default = null)
	{
		return $this->retrieveItem('cookies', $key, $default);
	}

	/**
	 * Retrieve a file from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  mixed
	 */
	public function file($key = null, $default = null)
	{
		$array = $this->files->all();

		if (is_null($key))
		{
			return $array;
		}

		if (isset($array[$key]))
		{
			return $array[$key];
		}

		return $default;
	}

	/**
	 * Retrieve a header from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function header($key = null, $default = null)
	{
		return $this->retrieveItem('headers', $key, $default);
	}

	/**
	 * Retrieve a server variable from the request.
	 *
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	public function server($key = null, $default = null)
	{
		return $this->retrieveItem('server', $key, $default);
	}

	/**
	 * Retrieve a parameter item from a given source.
	 *
	 * @param   string  $source
	 * @param   string  $key
	 * @param   mixed   $default
	 * @return  string
	 */
	protected function retrieveItem($source, $key, $default)
	{
		if (is_null($key))
		{
			return $this->$source->all();
		}

		return $this->$source->get($key, $default, true);
	}

	/**
	 * Normalizes a query string.
	 *
	 * It builds a normalized query string, where keys/value pairs are alphabetized,
	 * have consistent escaping and unneeded delimiters are removed.
	 *
	 * @param   string  $qs  Query string
	 * @return  string  A normalized query string for the Request
	 */
	public static function normalizeQueryString($qs)
	{
		if ('' == $qs)
		{
			return '';
		}

		$parts = array();
		$order = array();

		foreach (explode('&', $qs) as $param)
		{
			if ('' === $param || '=' === $param[0])
			{
				// Ignore useless delimiters, e.g. "x=y&".
				// Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
				// PHP also does not include them when building _GET.
				continue;
			}

			$keyValuePair = explode('=', $param, 2);

			// GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
			// PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
			// RFC 3986 with rawurlencode.
			$parts[] = isset($keyValuePair[1]) ?
				rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) :
				rawurlencode(urldecode($keyValuePair[0]));
			$order[] = urldecode($keyValuePair[0]);
		}

		// [!] Work around Symfony's HttpFoundation Request
		// reordering incoming GET vars. The following:
		//
		// post[]=18&post[]=17&post[]=19&post[]=20&post[]=21&post[]=22
		//
		// ... would incorrectly result in this:
		//
		// Array
		// (
		//     [0] => 17  <- Wrong!
		//     [1] => 18  <- Wrong!
		//     [2] => 19
		//     [3] => 20
		//     [4] => 21
		//     [5] => 22
		// )
		//array_multisort($order, SORT_ASC, $parts);

		return implode('&', $parts);
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable. Optional.
	 * @return  The request user state.
	 */
	public function getState($key, $request, $default = null, $type = 'none')
	{
		$cur_state = App::has('user') ? App::get('user')->getState($key, $default) : $default;
		$new_state = $this->getVar($request, null, 'default', $type);

		// Save the new value only if it was set in this request.
		if ($new_state !== null)
		{
			switch ($type)
			{
				case 'int':
					$new_state = self::_flatten('', $new_state);
					$new_state = intval($new_state);
					break;
				case 'word':
					$new_state = (string) self::_flatten('', $new_state);
					$new_state = preg_replace('/[^A-Z_]/i', '', $new_state);
					break;
				case 'cmd':
					$new_state = (string) self::_flatten('', $new_state);
					$new_state = preg_replace('/[^A-Z0-9_\.-]/i', '', $new_state);
					break;
				case 'bool':
					$new_state = (bool) $new_state;
					break;
				case 'float':
					$new_state = (string) self::_flatten('', $new_state);
					$new_state = (float) preg_replace('/-?[0-9]+(\.[0-9]+)?/', '', $new_state);
					break;
				case 'string':
					$new_state = (string) self::_flatten('', $new_state);
					break;
				case 'array':
					$new_state = (array) $new_state;
					break;
			}

			if (App::has('user'))
			{
				App::get('user')->setState($key, $new_state);
			}
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Flatten a multi-dimensional array
	 *
	 * @param   string   $separator
	 * @param   mixed    $arrayvar
	 * @return  string
	 */
	private function _flatten($separator, $arrayvar)
	{
		$out = '';

		if (is_array($arrayvar))
		{
			foreach ($arrayvar as $av)
			{
				if (is_array($av))
				{
					$out .= self::_flatten($separator, $av); // Recursive Use of the Array
				}
				else
				{
					$out .= $separator . $av;
				}
			}
		}
		else
		{
			$out .= $separator . $arrayvar;
		}

		return $out;
	}

	/**
	 * Checks for a form token in the request.
	 *
	 * Use in conjunction with Html::input('token').
	 *
	 * @param   string   $method  The request method in which to look for the token key.
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public function checkToken($method = 'post')
	{
		return App::get('session')->checkToken($method);
	}

	/**
	 * Checks for a honeypot in the request
	 *
	 * @param   string   $name
	 * @param   integer  $delay
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public function checkHoneypot($name = null, $delay = 3)
	{
		$name = $name ?: Honeypot::getName();

		if ($honey = self::getVar($name, array(), 'post'))
		{
			if (!Honeypot::isValid($honey['p'], $honey['t'], $delay))
			{
				if (App::has('log'))
				{
					$fallback = 'option=' . $this->getCmd('option') . '&controller=' . $this->getCmd('controller') . '&task=' . $this->getCmd('task');

					$from = $this->getVar('REQUEST_URI', $fallback, 'server');
					$from = $from ?: $fallback;

					$msg = 'spam honeypot ' . $this->ip();
					if (App::has('user'))
					{
						$msg .= ' ' . App::get('user')->get('id') . ' ' . App::get('user')->get('username');
					}
					$msg .= ' ' . $from;

					App::get('log')->logger('spam')->info($msg);
				}

				return false;
			}
		}

		return true;
	}
}
