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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

/**
 * Repository class
 */
class Repository extends Registry
{
	/**
	 * The current client type (admin, site, api, etc).
	 *
	 * @var  string
	 */
	protected $client;

	/**
	 * The loader implementation.
	 *
	 * @var  object
	 */
	protected $loader;

	/**
	 * Create a new configuration repository.
	 *
	 * @param   object  $loader
	 * @param   string  $client
	 * @return  void
	 */
	public function __construct($client, $loader = null)
	{
		if (!$loader)
		{
			$loader = new \Hubzero\Config\FileLoader(PATH_APP . DS . 'config');
		}
		$this->loader = $loader;
		$this->client = $client;

		$items = $this->load($this->client);

		parent::__construct($items);
	}

	/**
	 * Load the configuration for a specified client.
	 *
	 * @param   string  $client
	 * @return  void
	 */
	public function load($client)
	{
		return $this->loader->load($client);
	}

	/**
	 * Get the loader implementation.
	 *
	 * @return  object
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Set the loader implementation.
	 *
	 * @param   object $loader
	 * @return  void
	 */
	public function setLoader($loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Set the current configuration client.
	 *
	 * @param   string  $client
	 * @return  void
	 */
	public function setClient($client)
	{
		$this->client = (string) $client;
	}

	/**
	 * Get the current configuration client.
	 *
	 * @return  string
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Get a registry value.
	 *
	 * @param   string  $path     Registry path (e.g. config.cache.file)
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 * @return  mixed   Value of entry or null
	 */
	public function get($path, $default = null)
	{
		// Return default value if path is empty
		if (empty($path))
		{
			return $default;
		}

		if (strpos($path, $this->separator))
		{
			return parent::get($path, $default);
		}

		$nodes = get_object_vars($this->data);
		$found = false;

		// Traverse the registry to find the correct node for the result.
		foreach ($nodes as $n => $node)
		{
			if (is_array($node) && isset($node[$path]))
			{
				$value = $node[$path];
				$found = true;
				continue;
			}

			if (!isset($node->$path))
			{
				continue;
			}

			$value = $node->$path;
			$found = true;
		}

		if (!$found || $value === null || $value === '')
		{
			//return $default;
			return parent::get($path, $default);
		}

		return $value;
	}
}
