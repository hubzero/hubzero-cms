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

namespace Hubzero\Cache\Storage;

/**
 * Local Memory storage for Cache manager
 */
class Memory extends None
{
	/**
	 * Data container
	 *
	 * @var  array
	 */
	protected $data = array();

	/**
	 * Create a new file cache store instance.
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return true;
	}

	/**
	 * Add an item to the cache only if it doesn't already exist
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $minutes
	 * @return  void
	 */
	public function add($key, $value, $minutes)
	{
		if ($this->has($key))
		{
			return false;
		}

		return $this->put($key, $value, $minutes);
	}

	/**
	 * Store an item in the cache for a given number of minutes.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @param   int     $minutes
	 * @return  void
	 */
	public function put($key, $value, $minutes)
	{
		$this->data[$this->id($key)] = array(
			'time'  => time(),
			'value' => $value,
			'ttl'   => $this->expiration($minutes)
		);

		return true;
	}

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param   string  $key
	 * @param   mixed   $value
	 * @return  void
	 */
	public function forever($key, $value)
	{
		return $this->put($key, $value, 0);
	}

	/**
	 * Retrieve an item from the cache by key.
	 *
	 * @param   string  $key
	 * @return  mixed
	 */
	public function get($key)
	{
		if ($this->has($key))
		{
			return $this->data[$this->id($key)]['value'];
		}

		return null;
	}

	/**
	 * Check if an item exists in the cache
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function has($key)
	{
		$key = $this->id($key);

		if (!isset($this->data[$key]))
		{
			return false;
		}

		$value = $this->data[$key];

		if ($this->isDataExpired($value))
		{
			unset($this->data[$key]);
			return false;
		}

		return true;
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key)
	{
		if ($this->has($key))
		{
			unset($this->data[$this->id($key)]);
		}

		return true;
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		$prefix = $this->options['hash'] . '-cache-' . $group . '-';

		foreach ($this->data as $key => $value)
		{
			if (substr($key, 0, strlen($prefix)) == $prefix)
			{
				unset($this->data[$key]);
			}
		}

		return true;
	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @return  boolean
	 */
	public function gc()
	{
		$prefix = $this->options['hash'] . '-cache-';

		foreach ($this->data as $key => $value)
		{
			if (substr($key, 0, strlen($prefix)) == $prefix)
			{
				$value = $this->data[$key];

				if ($this->isDataExpired($value))
				{
					unset($this->data[$key]);
				}
			}
		}

		return true;
	}

	/**
	 * Get the expiration time based on the given minutes.
	 *
	 * @param   integer  $minutes
	 * @return  integer
	 */
	protected function expiration($minutes)
	{
		if ($minutes === 0) return 9999999999;

		return time() + ($minutes * 60);
	}

	/**
	 * Check if the given data is expired
	 *
	 * @param   array    $data
	 * @return  boolean
	 */
	protected function isDataExpired(array $data)
	{
		return $data['ttl'] !== 0 && time() > $data['ttl'];
		//return $data['ttl'] !== 0 && time() - $data['time'] > $data['ttl'];
	}
}
