<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	public function __construct($vars = array())
	{
		$this->flush()->bind($vars);
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

		$vars = array();

		// Process the parsed variables based on custom defined rules
		foreach ($this->rules['parse'] as $rule)
		{
			if ($rule($uri))
			{
				break;
			}
		}

		return array_merge($this->vars(), $uri->getQuery(true));
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
