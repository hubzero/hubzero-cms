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

namespace Hubzero\Routing;

use Hubzero\Routing\Router\Rules;
use Hubzero\Utility\Uri;
use InvalidArgumentException;
use Closure;

/**
 * Class to create and parse routes
 */
class Router
{
	/**
	 * Host and port of current URI
	 *
	 * @var  string
	 */
	protected $prefix = '';

	/**
	 * An array of variables
	 *
	 * @var  array
	 */
	protected $vars = array();

	/**
	 * An array of rules
	 *
	 * @var  array
	 */
	protected $rules = array(
		'build' => null,
		'parse' => null
	);

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Array of options
	 * @return  void
	 */
	public function __construct($vars = array(), $prefix = '')
	{
		$this->flush()->bind($vars);

		$this->prefix = $prefix;
	}

	/**
	 * Translates an internal URL to a humanly readable URL.
	 *
	 * @param   string   $url    Absolute or Relative URI to resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compliance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             0: Leave URI in the same secure state as it was passed to the function.
	 *                            -1: Make URI unsecure using the global unsecure site URI.
	 * @return  string  The translated humanly readable URL.
	 */
	public function url($url, $xhtml = true, $ssl = null)
	{
		if ((strpos($url, '&') !== 0)
		 && (strpos($url, 'index.php') !== 0))
		{
			return $url;
		}

		// Build route.
		$uri = $this->build($url);
		$url = $uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'));

		// Replace spaces.
		$url = preg_replace('/\s/u', '%20', $url);

		// Get the secure/unsecure URLs.
		//
		// If the first 5 characters of the BASE are 'https', then we are on an ssl connection over
		// https and need to set our secure URL to the current request URL, if not, and the scheme is
		// 'http', then we need to do a quick string manipulation to switch schemes.
		if ((int) $ssl)
		{
			/*static $prefix;

			if (!$prefix)
			{
				$prefix = \App::get('request');
			}*/

			// Determine which scheme we want.
			$scheme = ((int) $ssl === 1) ? 'https' : 'http';

			// Make sure our URL path begins with a slash.
			if (!preg_match('#^/#', $url))
			{
				$url = '/' . $url;
			}

			// Build the URL.
			$url = $scheme . '://' . $this->prefix . $url;
		}

		if ($xhtml)
		{
			$url = htmlspecialchars($url);
		}

		return $url;
	}

	/**
	 * Get a set of rules
	 *
	 * @param   string  $type
	 * @return  object
	 */
	public function rules($type)
	{
		$type = strtolower(trim($type));

		if (!isset($this->rules[$type]))
		{
			throw new InvalidArgumentException(sprintf('Rule type of %s not supported', $type));
		}

		return $this->rules[$type];
	}

	/**
	 * Function to convert an internal URI to a route
	 *
	 * @param   string  $uri  The internal URL
	 * @return  string  The absolute search engine friendly URL
	 */
	public function build($uri)
	{
		// Create the URI object
		$uri = $this->createUri($uri);

		// Process the uri information based on custom defined rules
		foreach ($this->rules['build'] as $rule)
		{
			$uri = $rule($uri);
		}

		return $uri;
	}

	/**
	 * Function to convert a route to an internal URI
	 *
	 * @param   string  $uri  The request URL
	 * @return  array
	 */
	public function parse($uri)
	{
		// Create the URI object
		$uri = $this->createUri($uri);

		$this->bind($uri->getQuery(true));

		// Process the parsed variables based on custom defined rules
		foreach ($this->rules['parse'] as $rule)
		{
			if ($rule($uri))
			{
				break;
			}
		}

		$vars = array_merge($uri->getQuery(true), $this->vars());

		$this->bind($vars);

		return $this->vars();
	}

	/**
	 * Clear any set rules
	 *
	 * @return  object
	 */
	public function flush()
	{
		$this->rules = array(
			'build' => new Rules(),
			'parse' => new Rules()
		);
		$this->vars = array();

		return $this;
	}

	/**
	 * Set a router variable, creating it if it doesn't exist
	 *
	 * @param   string   $key     The name of the variable
	 * @param   mixed    $value   The value of the variable
	 * @param   boolean  $create  If True, the variable will be created if it doesn't exist yet
	 * @return  object
	 */
	public function set($key, $value, $create = true)
	{
		if ($create || array_key_exists($key, $this->vars))
		{
			$this->vars[$key] = $value;
		}

		return $this;
	}

	/**
	 * Set the router variable array
	 *
	 * @param   array    $vars   An associative array with variables
	 * @param   boolean  $merge  If True, the array will be merged instead of overwritten
	 * @return  object
	 */
	public function bind($vars = array(), $merge = true)
	{
		if ($merge)
		{
			$this->vars = array_merge($this->vars, $vars);
		}
		else
		{
			$this->vars = $vars;
		}

		return $this;
	}

	/**
	 * Get a router variable
	 *
	 * @param   string  $key  The name of the variable
	 * @return  mixed   Value of the variable
	 */
	public function get($key)
	{
		$result = null;

		if (isset($this->vars[$key]))
		{
			$result = $this->vars[$key];
		}

		return $result;
	}

	/**
	 * Unset a router variable
	 *
	 * @param   string  $key  The name of the variable
	 * @return  object
	 */
	public function forget($key)
	{
		if (array_key_exists($key, $this->vars))
		{
			unset($this->vars[$key]);
		}

		return $this;
	}

	/**
	 * Get the router variable array
	 *
	 * @return  array  An associative array of router variables
	 */
	public function vars()
	{
		return $this->vars;
	}

	/**
	 * Create a uri based on a full or partial url string
	 *
	 * @param   string  $url  The URI
	 * @return  object
	 */
	protected function createUri($url)
	{
		if ($url instanceof Uri)
		{
			return $url;
		}

		// Create full URL if we are only appending variables to it
		if (substr($url, 0, 1) == '&')
		{
			$vars = array();

			if (strpos($url, '&amp;') !== false)
			{
				$url = str_replace('&amp;', '&', $url);
			}

			parse_str($url, $vars);

			$vars = array_merge($this->vars(), $vars);

			foreach ($vars as $key => $var)
			{
				if ($var == '')
				{
					unset($vars[$key]);
				}
			}

			$url = 'index.php?' . urldecode(http_build_query($vars, '', '&'));
		}

		return new Uri($url);
	}
}
