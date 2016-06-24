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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

use Hubzero\Base\Object;

/**
 * URI Class
 *
 * Parses a URI and provides a common interface for
 * the platform to access and manipulate a URI.
 */
class Uri extends Object
{
	/**
	 * Original URI
	 *
	 * @var  string
	 */
	protected $uri = null;

	/**
	 * Protocol
	 *
	 * @var  string
	 */
	protected $scheme = null;

	/**
	 * Host
	 *
	 * @var  string
	 */
	protected $host = null;

	/**
	 * Port
	 *
	 * @var  integer
	 */
	protected $port = null;

	/**
	 * Username
	 *
	 * @var  string
	 */
	protected $user = null;

	/**
	 * Password
	 *
	 * @var  string
	 */
	protected $pass = null;

	/**
	 * Path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * Query
	 *
	 * @var  string
	 */
	protected $query = null;

	/**
	 * Anchor
	 *
	 * @var  string
	 */
	protected $fragment = null;

	/**
	 * Query variable hash
	 *
	 * @var  array
	 */
	protected $vars = array();

	/**
	 * An array of JURI instances.
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Constructor.
	 *
	 * You can pass a URI string to the constructor to initialise a specific URI.
	 *
	 * @param   string  $uri  The optional URI string
	 * @return  void
	 */
	public function __construct($uri = null)
	{
		if (!is_null($uri))
		{
			$this->parse($uri);
		}
	}

	/**
	 * Magic method to get the string representation of the URI object.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Method to get the original source URI.
	 *
	 * @return  string
	 */
	public function uri()
	{
		return $this->uri;
	}

	/**
	 * Returns the global URI object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $uri  The URI to parse.  [optional: if null uses script URI]
	 * @return  object  The URI object.
	 */
	public static function getInstance($uri = 'SERVER')
	{
		if (empty(self::$instances[$uri]))
		{
			// Are we obtaining the URI from the server?
			if ($uri == 'SERVER')
			{
				// Determine if the request was over SSL (HTTPS).
				if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
				{
					$https = 's://';
				}
				else
				{
					$https = '://';
				}

				// Since we are assigning the URI from the server variables, we first need
				// to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
				// are present, we will assume we are running on apache.

				if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
				{
					// To build the entire URI we need to prepend the protocol, and the http host
					// to the URI string.
					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				}
				else
				{
					// Since we do not have REQUEST_URI to work with, we will assume we are
					// running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
					// QUERY_STRING environment variables.

					// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

					// If the query string exists append it to the URI string
					if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
					{
						$theURI .= '?' . $_SERVER['QUERY_STRING'];
					}
				}

				// Extra cleanup to remove invalid chars in the URL to prevent injections through the Host header
				$_SERVER['REQUEST_URI'] = str_replace(
					array("'", '"', '<', '>'),
					array('%27','%22','%3C','%3E'),
					$_SERVER['REQUEST_URI']
				);
				$theURI = str_replace(
					array("'", '"', '<', '>'),
					array('%27','%22','%3C','%3E'),
					$theURI
				);
			}
			else
			{
				// We were given a URI
				$theURI = $uri;
			}

			// Create the new URI instance
			self::$instances[$uri] = new self($theURI);
		}

		return self::$instances[$uri];
	}

	/**
	 * Returns the root URI
	 *
	 * @param   boolean  $pathonly  If false, prepend the scheme, host and port information. Default is false.
	 * @param   string   $path      The path
	 * @return  string   The root URI string.
	 */
	public function root($pathonly = false, $path = null)
	{
		$prefix = $this->toString(array('scheme', 'host', 'port'));

		if (!isset($path))
		{
			$path = rtrim($this->toString(array('path')), '/\\');
		}

		return $pathonly === false ? $prefix . $path . '/' : $path;
	}

	/**
	 * Parse a given URI and populate the class fields.
	 *
	 * @param   string   $uri  The URI string to parse.
	 * @return  boolean  True on success.
	 */
	public function parse($uri)
	{
		// Initialise variables
		$retval = false;

		// Set the original URI to fall back on
		$this->uri = $uri;

		// Parse the URI and populate the object fields.  If URI is parsed properly,
		// set method return value to true.
		if ($parts = parse_url($uri))
		{
			$retval = true;
		}

		// We need to replace &amp; with & for parse_str to work right...
		if (isset($parts['query']) && strpos($parts['query'], '&amp;'))
		{
			$parts['query'] = str_replace('&amp;', '&', $parts['query']);
		}

		$data = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment');
		foreach ($data as $part)
		{
			$this->$part = isset($parts[$part]) ? $parts[$part] : null;
		}

		// Parse the query
		if (isset($parts['query']))
		{
			parse_str($parts['query'], $this->vars);
		}

		return $retval;
	}

	/**
	 * Returns full uri string.
	 *
	 * @param   array   $parts  An array specifying the parts to render.
	 * @return  string  The rendered URI string.
	 */
	public function toString($parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'))
	{
		// Make sure the query is created
		$query = $this->getQuery();

		$uri  = '';
		$uri .= in_array('scheme', $parts) ? (!empty($this->scheme) ? $this->scheme . '://' : '') : '';
		$uri .= in_array('user', $parts) ? $this->user : '';
		$uri .= in_array('pass', $parts) ? (!empty($this->pass) ? ':' : '') . $this->pass . (!empty($this->user) ? '@' : '') : '';
		$uri .= in_array('host', $parts) ? $this->host : '';
		$uri .= in_array('port', $parts) ? (!empty($this->port) ? ':' : '') . $this->port : '';

		// Related to WIP for special group handling...
		//$uri .= in_array('path', $parts) ? $this->path : '';
		if (in_array('path', $parts))
		{
			$uri .= (empty($uri) || (!empty($this->path) && ($this->path{0} == '/'))) ? $this->path : '/' . $this->path;
		}

		$uri .= in_array('query', $parts) ? (!empty($query) ? '?' . $query : '') : '';
		$uri .= in_array('fragment', $parts) ? (!empty($this->fragment) ? '#' . $this->fragment : '') : '';

		return $uri;
	}

	/**
	 * Adds a query variable and value, replacing the value if it
	 * already exists and returning the old value.
	 *
	 * @param   string  $name   Name of the query variable to set.
	 * @param   string  $value  Value of the query variable.
	 * @return  object  $this
	 */
	public function setVar($name, $value)
	{
		$this->vars[$name] = $value;

		$this->query = null;

		return $this;
	}

	/**
	 * Checks if variable exists.
	 *
	 * @param   string   $name  Name of the query variable to check.
	 * @return  boolean  True if the variable exists.
	 */
	public function hasVar($name)
	{
		return array_key_exists($name, $this->vars);
	}

	/**
	 * Returns a query variable by name.
	 *
	 * @param   string  $name     Name of the query variable to get.
	 * @param   string  $default  Default value to return if the variable is not set.
	 * @return  array   Query variables.
	 */
	public function getVar($name, $default = null)
	{
		if (array_key_exists($name, $this->vars))
		{
			return $this->vars[$name];
		}

		return $default;
	}

	/**
	 * Removes an item from the query string variables if it exists.
	 *
	 * @param   string  $name  Name of variable to remove.
	 * @return  object  $this
	 */
	public function delVar($name)
	{
		if (array_key_exists($name, $this->vars))
		{
			unset($this->vars[$name]);

			$this->query = null;
		}

		return $this;
	}

	/**
	 * Sets the query to a supplied string in format:
	 * foo=bar&x=y
	 *
	 * @param   mixed   $query  The query string or array.
	 * @return  object  $this
	 */
	public function setQuery($query)
	{
		if (is_array($query))
		{
			$this->vars = $query;
		}
		else
		{
			if (strpos($query, '&amp;') !== false)
			{
				$query = str_replace('&amp;', '&', $query);
			}

			parse_str($query, $this->vars);
		}

		$this->query = null;

		return $this;
	}

	/**
	 * Returns flat query string.
	 *
	 * @param   boolean  $toArray  True to return the query as a key => value pair array.
	 * @return  string   Query string.
	 */
	public function getQuery($toArray = false)
	{
		if ($toArray)
		{
			return $this->vars;
		}

		// If the query is empty build it first
		if (is_null($this->query))
		{
			$this->query = $this->buildQuery($this->vars);
		}

		return $this->query;
	}

	/**
	 * Build a query from a array (reverse of the PHP parse_str()).
	 *
	 * @param   array   $params  The array of key => value pairs to return as a query string.
	 * @return  string  The resulting query string.
	 */
	public function buildQuery($params)
	{
		if (!is_array($params) || count($params) == 0)
		{
			return false;
		}

		return urldecode(http_build_query($params, '', '&'));
	}

	/**
	 * Get URI scheme (protocol)
	 * ie. http, https, ftp, etc...
	 *
	 * @return  string  The URI scheme.
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Set URI scheme (protocol)
	 * ie. http, https, ftp, etc...
	 *
	 * @param   string  $scheme  The URI scheme.
	 * @return  object  $this
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;

		return $this;
	}

	/**
	 * Get URI username
	 * Returns the username, or null if no username was specified.
	 *
	 * @return  string  The URI username.
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set URI username.
	 *
	 * @param   string  $user  The URI username.
	 * @return  object  $this
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get URI password
	 * Returns the password, or null if no password was specified.
	 *
	 * @return  string  The URI password.
	 */
	public function getPass()
	{
		return $this->pass;
	}

	/**
	 * Set URI password.
	 *
	 * @param   string  $pass  The URI password.
	 * @return  object  $this
	 */
	public function setPass($pass)
	{
		$this->pass = $pass;

		return $this;
	}

	/**
	 * Get URI host
	 * Returns the hostname/ip or null if no hostname/ip was specified.
	 *
	 * @return  string  The URI host.
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set URI host.
	 *
	 * @param   string  $host  The URI host.
	 * @return  object  $this
	 */
	public function setHost($host)
	{
		$this->host = $host;

		return $this;
	}

	/**
	 * Get URI port
	 * Returns the port number, or null if no port was specified.
	 *
	 * @return  integer  The URI port number.
	 */
	public function getPort()
	{
		return (isset($this->port)) ? $this->port : null;
	}

	/**
	 * Set URI port.
	 *
	 * @param   integer  $port  The URI port number.
	 * @return  object   $this
	 */
	public function setPort($port)
	{
		$this->port = $port;

		return $this;
	}

	/**
	 * Gets the URI path string.
	 *
	 * @return  string  The URI path string.
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the URI path string.
	 *
	 * @param   string  $path  The URI path string.
	 * @return  void
	 */
	public function setPath($path)
	{
		$this->path = $this->clean($path);

		return $this;
	}

	/**
	 * Get the URI archor string
	 * Everything after the "#".
	 *
	 * @return  string  The URI anchor string.
	 */
	public function getFragment()
	{
		return $this->fragment;
	}

	/**
	 * Set the URI anchor string
	 * everything after the "#".
	 *
	 * @param   string  $anchor  The URI anchor string.
	 * @return  object  $this
	 */
	public function setFragment($anchor)
	{
		$this->fragment = $anchor;

		return $this;
	}

	/**
	 * Checks whether the current URI is using HTTPS.
	 *
	 * @return  boolean  True if using SSL via HTTPS.
	 */
	public function isSecure()
	{
		return $this->scheme == 'https' ? true : false;
	}

	/**
	 * Checks if the supplied URL is internal
	 *
	 * @param   string  $url  The URL to check
	 * @return  boolean
	 */
	public static function isInternal($url)
	{
		$current = self::getInstance()->toString(['scheme', 'host']);
		$given   = self::getInstance($url);
		$base    = $given->toString(['scheme', 'host', 'path']);
		$host    = $given->toString(['scheme', 'host']);

		if (stripos($base, $current) !== 0 && !empty($host))
		{
			return false;
		}
		return true;
	}

	/**
	 * Resolves //, ../ and ./ from a path and returns
	 * the result. Eg:
	 *
	 * /foo/bar/../boo.php	=> /foo/boo.php
	 * /foo/bar/../../boo.php => /boo.php
	 * /foo/bar/.././/boo.php => /foo/boo.php
	 *
	 * @param   string  $path  The URI path to clean.
	 * @return  string  Cleaned and resolved URI path.
	 */
	protected function clean($path)
	{
		$path = explode('/', preg_replace('#(/+)#', '/', $path));

		for ($i = 0, $n = count($path); $i < $n; $i++)
		{
			if ($path[$i] == '.' or $path[$i] == '..')
			{
				if (($path[$i] == '.') or ($path[$i] == '..' and $i == 1 and $path[0] == ''))
				{
					unset($path[$i]);
					$path = array_values($path);
					$i--;
					$n--;
				}
				elseif ($path[$i] == '..' and ($i > 1 or ($i == 1 and $path[0] != '')))
				{
					unset($path[$i]);
					unset($path[$i - 1]);
					$path = array_values($path);
					$i -= 2;
					$n -= 2;
				}
			}
		}

		return implode('/', $path);
	}
}
