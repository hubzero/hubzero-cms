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

use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Cache\Auditor;

/**
 * APC storage for Cache manager
 */
class Apc extends None
{
	/**
	 * Indicates if APCu is supported.
	 *
	 * @var  bool
	 */
	protected $apcu = false;

	/**
	 * Create a new file cache store instance.
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		$this->apcu = function_exists('apcu_fetch');

		if (!self::isAvailable())
		{
			throw new RuntimeException('Cannot use Apc cache storage. Apc extension is not loaded.');
		}
	}

	/**
	 * Test to see if the cache storage is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isAvailable()
	{
		return extension_loaded('apc');
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
		$key = $this->id($key);
		$seconds = $minutes * 60;

		return $this->apcu ? apcu_add($key, $value, $seconds) : apc_add($key, $value, $seconds);
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
		$key = $this->id($key);
		$seconds = $minutes * 60;

		return $this->apcu ? apcu_store($key, $value, $seconds) : apc_store($key, $value, $seconds);
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
		$key = $this->id($key);
		return $this->apcu ? apcu_fetch($key) : apc_fetch($key);
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
		return $this->apcu ? apcu_exists($key) : apc_exists($key);
	}

	/**
	 * Remove an item from the cache.
	 *
	 * @param   string  $key
	 * @return  bool
	 */
	public function forget($key)
	{
		$key = $this->id($key);
		return $this->apcu ? apcu_delete($key) : apc_delete($key);
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = null)
	{
		$hash = $this->options['hash'];

		$allinfo = $this->apcu ? apcu_cache_info() : apc_cache_info('user');

		foreach ($allinfo['cache_list'] as $key)
		{
			if (strpos($key['info'], $hash . '-cache-' . $group . '-') === 0) // xor $mode != 'group')
			{
				$this->apcu ? apcu_delete($key['info']) : apc_delete($key['info']);
			}
		}
		return true;
	}

	/**
	 * Force garbage collect expired cache data as items are removed only on fetch!
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc()
	{
		$hash = $this->options['hash'];

		$allinfo = $this->apcu ? apcu_cache_info() : apc_cache_info('user');

		$keys = $allinfo['cache_list'];

		foreach ($allinfo['cache_list'] as $key)
		{
			if (strpos($key['info'], $hash . '-cache-'))
			{
				$this->apcu ? apcu_fetch($key['info']) : apc_fetch($key['info']);
			}
		}
	}

	/**
	 * Get all cached data
	 *
	 * @return  array
	 */
	public function all()
	{
		$allinfo = $this->apcu ? apcu_cache_info() : apc_cache_info('user');

		$keys = $allinfo['cache_list'];

		$hash = $this->options['hash'];

		$data = array();

		foreach ($keys as $key)
		{
			$name    = $key['info'];
			$namearr = explode('-', $name);

			if ($namearr !== false && $namearr[0] == $secret && $namearr[1] == 'cache')
			{
				$group = $namearr[2];

				if (!isset($data[$group]))
				{
					$item = new Auditor($group);
				}
				else
				{
					$item = $data[$group];
				}

				$item->tally($key['mem_size'] / 1024);

				$data[$group] = $item;
			}
		}

		return $data;
	}
}
